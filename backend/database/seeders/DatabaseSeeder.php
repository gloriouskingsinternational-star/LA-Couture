<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::firstOrCreate(
            ['email' => 'admin@lacouture.com'],
            [
                'name'     => 'L.A. Couture Admin',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'LaCouture@Admin2026!')),
                'role'     => 'admin',
                'status'   => 'approved',
            ]
        );

        // Seed product catalog
        $products = [
            ['sku' => 'P001', 'name' => 'Abuja Power Suit',        'category' => 'Luxury Suits',      'price' => 200000, 'tag' => 'New',        'image' => 'img/black-breasted-suit.jpg'],
            ['sku' => 'P002', 'name' => 'Royal Agbada Set',         'category' => 'Agbada & Native',   'price' => 150000, 'tag' => 'Bestseller', 'image' => 'img/agbada-collection.jpg'],
            ['sku' => 'P003', 'name' => 'Bespoke Commission',       'category' => 'Bespoke',           'price' => 90000,  'tag' => 'Custom',     'image' => 'img/bespoke-design.jpg'],
            ['sku' => 'P004', 'name' => 'Lagos Street Luxe Set',    'category' => 'Smart Casual',      'price' => 150000, 'tag' => null,         'image' => 'img/kings-dashiki.jpg'],
            ['sku' => 'P005', 'name' => 'FCT Executive',            'category' => 'Formal Wear',       'price' => 80000,  'tag' => 'New',        'image' => 'img/fct-executive.jpg'],
            ['sku' => 'P006', 'name' => 'Gold Cufflink & Tie Set',  'category' => 'Accessories',       'price' => 35000,  'tag' => null,         'image' => 'img/gold-accessories.jpg'],
            ['sku' => 'P007', 'name' => 'Abuja Three-Piece',        'category' => 'Luxury Suits',      'price' => 155000, 'tag' => 'Limited',    'image' => 'img/luxury-suits.jpg'],
            ['sku' => 'P008', 'name' => 'Aso-Oke Senator Set',      'category' => 'Agbada & Native',   'price' => 120000, 'tag' => null,         'image' => 'img/senator-attire.jpg'],
            ['sku' => 'P009', 'name' => 'Weekend Linen Set',        'category' => 'Smart Casual',      'price' => 45000,  'tag' => null,         'image' => 'img/weekend-linen.jpg'],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(
                ['sku' => $p['sku']],
                array_merge($p, ['active' => true, 'stock' => 10])
            );
        }
    }
}
