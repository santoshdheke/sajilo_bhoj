<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\FloorTable;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Room;
use App\Models\TableBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $rooms = Room::with([
            'floorTables.chairs',
            'floorTables.photos',
        ])->get();

        return view('frontend.theme_one.table.index', compact('rooms'));
    }

    public function showRoom(Room $room)
    {
        $room->load(['floorTables.chairs', 'floorTables.photos']);
        return view('frontend.theme_one.table.room', compact('room'));
    }

    public function showTable(FloorTable $table)
    {
        $table->load(['room.floorTables', 'chairs', 'photos']);

        $otherTables = $table->room->floorTables()
            ->with(['chairs', 'photos'])
            ->where('id', '!=', $table->id)
            ->get();

        // Today's bookings for this table
        $todayBookings = TableBooking::where('table_id', $table->id)
            ->whereDate('booking_date', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('booking_time')
            ->get();

        // Menu for ordering from this table
        $categories = MenuCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $menuItems = MenuItem::with('category')
            ->whereHas('category')
            ->where('is_available', true)
            ->orderBy('menu_category_id')
            ->orderBy('sort_order')
            ->get();

        return view('frontend.theme_one.table.detail', compact(
            'table', 'otherTables', 'todayBookings', 'categories', 'menuItems'
        ));
    }

    public function book(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'table_id'       => 'required|exists:tables,id',
            'name'           => 'required|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'booking_date'   => 'required|date|after_or_equal:today',
            'booking_time'   => 'required',
            'party_size'     => 'required|integer|min:1|max:50',
            'notes'          => 'nullable|string|max:500',
        ]);

        TableBooking::create($data + ['status' => 'pending']);

        return back()->with('success', 'Your booking is confirmed! We\'ll reach out to you shortly.');
    }
}
