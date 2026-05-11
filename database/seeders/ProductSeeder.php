<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Electronics' => ['Laptop Pro', 'Wireless Mouse', 'HD Monitor', 'Mechanical Keyboard', 'USB-C Dock', 'Webcam 4K', 'Noise Canceling Headphones', 'Smart Watch'],
            'Raw Materials' => ['Steel Sheet 2mm', 'Aluminum Rod', 'Copper Wire 100m', 'Plastic Pellets (HDPE)', 'Glass Panel 4x4', 'Wood Plank (Oak)', 'Iron Bar', 'Textile Roll (Cotton)'],
            'Office Supplies' => ['A4 Paper Box', 'Printer Ink Black', 'Gel Pens (12 Pack)', 'File Folder (Blue)', 'Stapler Heavy Duty', 'Sticky Notes Pack', 'Notebook Large', 'Whiteboard Marker'],
            'Accessories' => ['Laptop Sleeve', 'Desk Mat Large', 'Phone Stand', 'Monitor Stand', 'Cables Organizer', 'Mouse Pad', 'Headset Stand', 'Ergonomic Footrest'],
            'Packaging Materials' => ['Cardboard Box Med', 'Bubble Wrap Roll', 'Stretch Film', 'Packing Tape 50m', 'Shipping Labels', 'Plastic Mailer', 'Padded Envelope', 'Wooden Pallet'],
        ];

        foreach ($categories as $category => $items) {
            foreach ($items as $index => $name) {
                Product::create([
                    'sku' => strtoupper(substr($category, 0, 1)) . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(4)),
                    'name' => $name,
                    'category' => $category,
                    'unit' => $this->getUnit($category),
                    'cost_price' => rand(10, 500) + (rand(0, 99) / 100),
                    'sell_price' => rand(600, 1500) + (rand(0, 99) / 100),
                    'stock_qty' => rand(20, 500),
                    'min_stock' => rand(5, 20),
                ]);
            }
        }
    }

    private function getUnit($category)
    {
        return match ($category) {
            'Electronics', 'Accessories', 'Office Supplies' => 'pcs',
            'Raw Materials' => 'kg',
            'Packaging Materials' => 'roll',
            default => 'unit',
        };
    }
}
