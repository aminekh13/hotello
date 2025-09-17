<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RoomCategory;

class RoomCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Single Room',
                'description' => 'A comfortable single room perfect for solo travelers',
                'max_occupancy' => 1,
                'base_price' => 99.00,
                'amenities' => ['WiFi', 'TV', 'Air Conditioning', 'Private Bathroom', 'Mini Bar'],
                'is_active' => true,
            ],
            [
                'name' => 'Double Room',
                'description' => 'Spacious double room ideal for couples',
                'max_occupancy' => 2,
                'base_price' => 149.00,
                'amenities' => ['WiFi', 'TV', 'Air Conditioning', 'Private Bathroom', 'Mini Bar', 'Balcony'],
                'is_active' => true,
            ],
            [
                'name' => 'Twin Room',
                'description' => 'Two separate beds perfect for friends or family',
                'max_occupancy' => 2,
                'base_price' => 149.00,
                'amenities' => ['WiFi', 'TV', 'Air Conditioning', 'Private Bathroom', 'Mini Bar'],
                'is_active' => true,
            ],
            [
                'name' => 'Deluxe Suite',
                'description' => 'Luxurious suite with separate living area',
                'max_occupancy' => 4,
                'base_price' => 299.00,
                'amenities' => ['WiFi', 'TV', 'Air Conditioning', 'Private Bathroom', 'Mini Bar', 'Balcony', 'Living Area', 'Kitchenette'],
                'is_active' => true,
            ],
            [
                'name' => 'Presidential Suite',
                'description' => 'Our most luxurious accommodation with premium amenities',
                'max_occupancy' => 6,
                'base_price' => 599.00,
                'amenities' => ['WiFi', 'TV', 'Air Conditioning', 'Private Bathroom', 'Mini Bar', 'Balcony', 'Living Area', 'Kitchenette', 'Jacuzzi', 'Butler Service'],
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            RoomCategory::create($category);
        }
    }
}
