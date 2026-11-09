<?php

namespace App\Support;

use App\Models\Branch;
use App\Models\FloorTable;
use App\Models\Room;

/**
 * Starter floor every new restaurant gets: one room with Table 1–4, each with 4 chairs,
 * so orders and QR codes work on day one. The owner can rename or add more later.
 */
class DefaultTables
{
    public const TABLES = ['Table 1', 'Table 2', 'Table 3', 'Table 4'];

    /** One chair on each side of the table. */
    public const CHAIR_SIDES = ['top', 'bottom', 'left', 'right'];

    public static function seed(Branch $branch): void
    {
        $room = Room::withoutGlobalScopes()->create([
            'branch_id' => $branch->id,
            'name'      => 'Main Hall',
        ]);

        $i = 0;
        foreach (self::TABLES as $tableNo) {
            $table = FloorTable::withoutGlobalScopes()->create([
                'branch_id' => $branch->id,
                'room_id'   => $room->id,
                'table_no'  => $tableNo,
                'capacity'  => count(self::CHAIR_SIDES),
                'pos_x'     => 80 + 140 * $i++, // one row on the floor plan, like the demo data
                'pos_y'     => 80,
                'status'    => 'available',
            ]);

            foreach (self::CHAIR_SIDES as $side) {
                $table->chairs()->create(['side' => $side]);
            }
        }
    }
}
