<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hotel;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotels = [
            [
                'name' => 'Grand Plaza Hotel',
                'description' => 'A luxurious 5-star hotel in the heart of the city with world-class amenities and exceptional service.',
                'address' => '123 Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10001',
                'phone' => '+1-555-0123',
                'email' => 'info@grandplaza.com',
                'website' => 'https://grandplaza.com',
                'latitude' => 40.7589,
                'longitude' => -73.9851,
                'star_rating' => 5,
                'amenities' => ['WiFi', 'Parking', 'Pool', 'Gym', 'Restaurant', 'Bar', 'Spa', 'Room Service', 'Concierge', 'Business Center'],
                'is_active' => true,
            ],
            [
                'name' => 'Coastal Resort & Spa',
                'description' => 'A beautiful beachfront resort offering stunning ocean views and relaxing spa treatments.',
                'address' => '456 Ocean Drive',
                'city' => 'Miami',
                'state' => 'FL',
                'country' => 'USA',
                'postal_code' => '33139',
                'phone' => '+1-555-0456',
                'email' => 'reservations@coastalresort.com',
                'website' => 'https://coastalresort.com',
                'latitude' => 25.7907,
                'longitude' => -80.1300,
                'star_rating' => 4,
                'amenities' => ['WiFi', 'Parking', 'Pool', 'Spa', 'Restaurant', 'Bar', 'Beach Access', 'Room Service', 'Concierge'],
                'is_active' => true,
            ],
            [
                'name' => 'Mountain Lodge Inn',
                'description' => 'A cozy mountain retreat perfect for nature lovers and outdoor enthusiasts.',
                'address' => '789 Mountain View Road',
                'city' => 'Denver',
                'state' => 'CO',
                'country' => 'USA',
                'postal_code' => '80202',
                'phone' => '+1-555-0789',
                'email' => 'stay@mountainlodge.com',
                'website' => 'https://mountainlodge.com',
                'latitude' => 39.7392,
                'longitude' => -104.9903,
                'star_rating' => 3,
                'amenities' => ['WiFi', 'Parking', 'Restaurant', 'Bar', 'Hiking Trails', 'Pet Friendly', 'Non-smoking'],
                'is_active' => true,
            ],
            [
                'name' => 'Business Center Hotel',
                'description' => 'A modern business hotel designed for corporate travelers with excellent conference facilities.',
                'address' => '321 Corporate Plaza',
                'city' => 'Chicago',
                'state' => 'IL',
                'country' => 'USA',
                'postal_code' => '60601',
                'phone' => '+1-555-0321',
                'email' => 'business@centerhotel.com',
                'website' => 'https://centerhotel.com',
                'latitude' => 41.8781,
                'longitude' => -87.6298,
                'star_rating' => 4,
                'amenities' => ['WiFi', 'Parking', 'Business Center', 'Conference Rooms', 'Restaurant', 'Bar', 'Gym', 'Laundry'],
                'is_active' => true,
            ],
            [
                'name' => 'Historic Downtown Inn',
                'description' => 'A charming historic hotel in the downtown district with classic architecture and modern comforts.',
                'address' => '654 Heritage Street',
                'city' => 'Boston',
                'state' => 'MA',
                'country' => 'USA',
                'postal_code' => '02108',
                'phone' => '+1-555-0654',
                'email' => 'heritage@downtowninn.com',
                'website' => 'https://downtowninn.com',
                'latitude' => 42.3601,
                'longitude' => -71.0589,
                'star_rating' => 3,
                'amenities' => ['WiFi', 'Parking', 'Restaurant', 'Bar', 'Concierge', 'Historic Tours', 'Non-smoking'],
                'is_active' => true,
            ],
        ];

        foreach ($hotels as $hotelData) {
            Hotel::create($hotelData);
        }
    }
}
