<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HostessServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Event Hosting',
                'slug' => 'event-hosting',
                'description' => 'Professional hosting for corporate events, parties, and gatherings',
                'display_order' => 1,
            ],
            [
                'name' => 'Promotional Modeling',
                'slug' => 'promotional-modeling',
                'description' => 'Brand representation and product promotion at trade shows and events',
                'display_order' => 2,
            ],
            [
                'name' => 'VIP Hospitality',
                'slug' => 'vip-hospitality',
                'description' => 'Premium hosting services for VIP guests and high-profile clients',
                'display_order' => 3,
            ],
            [
                'name' => 'Trade Show Hostess',
                'slug' => 'trade-show-hostess',
                'description' => 'Engaging booth attendants for trade shows and exhibitions',
                'display_order' => 4,
            ],
            [
                'name' => 'Product Demonstration',
                'slug' => 'product-demonstration',
                'description' => 'Demonstrating products and services to potential customers',
                'display_order' => 5,
            ],
            [
                'name' => 'Concierge Services',
                'slug' => 'concierge-services',
                'description' => 'Personalized assistance and guest services',
                'display_order' => 6,
            ],
            [
                'name' => 'Brand Ambassador',
                'slug' => 'brand-ambassador',
                'description' => 'Long-term representation of brands at various events',
                'display_order' => 7,
            ],
            [
                'name' => 'Corporate Event Greeter',
                'slug' => 'corporate-event-greeter',
                'description' => 'Welcoming and directing guests at corporate functions',
                'display_order' => 8,
            ],
        ];

        foreach ($services as $service) {
            DB::table('hostess_services')->insert([
                'name' => $service['name'],
                //'slug' => $service['slug'],
                //'description' => $service['description'],
                'display_order' => $service['display_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}