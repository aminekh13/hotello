<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\Hotel;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = RoomCategory::all();
        $hotels = Hotel::all();

        // Create rooms for each category and assign to hotels
        foreach ($categories as $category) {
            $roomCount = match($category->name) {
                'Single Room' => 20,
                'Double Room' => 30,
                'Twin Room' => 25,
                'Deluxe Suite' => 10,
                'Presidential Suite' => 5,
                default => 10,
            };

            for ($i = 1; $i <= $roomCount; $i++) {
                $floor = match($category->name) {
                    'Single Room', 'Double Room', 'Twin Room' => rand(1, 5),
                    'Deluxe Suite' => rand(6, 8),
                    'Presidential Suite' => 9,
                    default => rand(1, 5),
                };

                $roomNumber = match($category->name) {
                    'Single Room' => sprintf('S%03d', $i),
                    'Double Room' => sprintf('D%03d', $i),
                    'Twin Room' => sprintf('T%03d', $i),
                    'Deluxe Suite' => sprintf('DS%03d', $i),
                    'Presidential Suite' => sprintf('PS%03d', $i),
                    default => sprintf('R%03d', $i),
                };

                Room::create([
                    'hotel_id' => $hotels->random()->id, // Assign to random hotel
                    'room_category_id' => $category->id,
                    'room_number' => $roomNumber,
                    'floor' => $floor,
                    'description' => "Beautiful {$category->name} with modern amenities",
                    'price_per_night' => $category->base_price + rand(-20, 50), // Add some price variation
                    'is_available' => rand(0, 1) ? true : false,
                    'is_active' => true,
                ]);
            }
        }
    }
}
