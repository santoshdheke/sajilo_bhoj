<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chair;
use App\Models\FloorTable;
use App\Models\FloorTablePhoto;
use App\Models\Room;
use App\Models\TableBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TableController extends Controller
{
    public function index(): View
    {
        $rooms = Room::with(['floorTables.chairs', 'floorTables.photos'])->get();

        $allTables = FloorTable::with(['room', 'chairs', 'todayBookings'])->get();

        $stats = [
            'total'     => $allTables->count(),
            'available' => $allTables->where('status', 'available')->count(),
            'occupied'  => $allTables->where('status', 'occupied')->count(),
            'reserved'  => $allTables->where('status', 'reserved')->count(),
            'cleaning'  => $allTables->where('status', 'cleaning')->count(),
            'bookings_today' => TableBooking::whereDate('booking_date', today())
                                            ->whereIn('status', ['pending', 'confirmed'])->count(),
        ];

        $todayBookings = TableBooking::with('floorTable')
            ->whereDate('booking_date', today())
            ->orderBy('booking_time')
            ->get();

        return view('admin.theme_one.table.index', compact('rooms', 'allTables', 'stats', 'todayBookings'));
    }

    // Table status
    public function updateTableStatus(Request $request, FloorTable $table): JsonResponse
    {
        $request->validate(['status' => 'required|in:available,occupied,reserved,cleaning']);
        $table->update(['status' => $request->status]);
        return response()->json(['success' => true, 'status' => $table->status]);
    }

    // Bookings
    public function storeBooking(Request $request): JsonResponse
    {
        $data = $request->validate([
            'table_id'       => 'required|exists:tables,id',
            'name'           => 'required|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'booking_date'   => 'required|date|after_or_equal:today',
            'booking_time'   => 'required|date_format:H:i',
            'party_size'     => 'required|integer|min:1|max:50',
            'notes'          => 'nullable|string|max:500',
        ]);
        $booking = TableBooking::create($data + ['status' => 'pending']);
        return response()->json($booking->load('floorTable'));
    }

    public function updateBookingStatus(Request $request, TableBooking $booking): JsonResponse
    {
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled']);
        $booking->update(['status' => $request->status]);
        return response()->json(['success' => true, 'status' => $booking->status]);
    }

    public function destroyBooking(TableBooking $booking): JsonResponse
    {
        $booking->delete();
        return response()->json(['success' => true]);
    }

    // Rooms
    public function storeRoom(Request $request): JsonResponse
    {
        $room = Room::create($request->validate(['name' => 'required|string|max:100']));
        return response()->json($room);
    }

    public function updateRoom(Request $request, Room $room): JsonResponse
    {
        $room->update($request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]));
        return response()->json($room);
    }

    public function uploadRoomPhoto(Request $request, Room $room): JsonResponse
    {
        $request->validate(['photo' => 'required|image|max:3072']);
        if ($room->photo) {
            Storage::disk('public')->delete($room->photo);
        }
        $path = $request->file('photo')->store('photos/rooms', 'public');
        $room->update(['photo' => $path]);
        return response()->json(['url' => Storage::url($path)]);
    }

    public function destroyRoom(Room $room): JsonResponse
    {
        if ($room->photo) {
            Storage::disk('public')->delete($room->photo);
        }
        $room->delete();
        return response()->json(['success' => true]);
    }

    // Tables
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name'    => 'required|string|max:50',
            'pos_x'   => 'required|integer|min:0',
            'pos_y'   => 'required|integer|min:0',
        ]);
        $table = FloorTable::create($data);
        return response()->json($table->load('chairs', 'photos'));
    }

    public function update(Request $request, FloorTable $table): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'sometimes|string|max:50',
            'pos_x'       => 'sometimes|integer|min:0',
            'pos_y'       => 'sometimes|integer|min:0',
            'description' => 'sometimes|nullable|string|max:1000',
        ]);
        $table->update($data);
        return response()->json($table);
    }

    public function destroy(FloorTable $table): JsonResponse
    {
        foreach ($table->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }
        $table->delete();
        return response()->json(['success' => true]);
    }

    // Table photos
    public function storeTablePhotos(Request $request, FloorTable $table): JsonResponse
    {
        $request->validate(['photos' => 'required|array', 'photos.*' => 'image|max:3072']);
        $saved = [];
        foreach ($request->file('photos') as $file) {
            $path  = $file->store('photos/tables', 'public');
            $photo = $table->photos()->create(['path' => $path, 'sort_order' => 0]);
            $saved[] = ['id' => $photo->id, 'url' => Storage::url($path)];
        }
        return response()->json($saved);
    }

    public function destroyTablePhoto(FloorTablePhoto $tablePhoto): JsonResponse
    {
        Storage::disk('public')->delete($tablePhoto->path);
        $tablePhoto->delete();
        return response()->json(['success' => true]);
    }

    // Chairs
    public function storeChair(Request $request): JsonResponse
    {
        $data = $request->validate([
            'table_id'       => 'required|exists:tables,id',
            'side'           => 'required|in:top,bottom,left,right',
        ]);
        $chair = Chair::create($data);
        return response()->json($chair);
    }

    public function destroyChair(Chair $chair): JsonResponse
    {
        $chair->delete();
        return response()->json(['success' => true]);
    }
}
