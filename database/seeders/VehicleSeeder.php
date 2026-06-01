<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $riyadhBranch = Branch::where('code', 'RIYADH')->first();
        $branchId = $riyadhBranch ? $riyadhBranch->id : null;

        $vehicles = [
            [
                'vin' => '1FTFW1RG5JFA88881',
                'make' => 'Ford',
                'model' => 'F-150 Raptor',
                'year' => 2024,
                'trim' => 'SuperCrew 4WD',
                'color' => 'Oxford White',
                'mileage' => 0,
                'fuel_type' => 'Petrol',
                'transmission' => 'Automatic',
                'condition' => 'new',
                'status' => 'available',
                'cost_price' => 310000.00,
                'listing_price' => 365000.00,
                'branch_id' => $branchId,
            ],
            [
                'vin' => 'JTMBU5JR0N5049992',
                'make' => 'Toyota',
                'model' => 'Land Cruiser LC300',
                'year' => 2025,
                'trim' => 'VXR 3.5T',
                'color' => 'Precious White Pearl',
                'mileage' => 50,
                'fuel_type' => 'Petrol',
                'transmission' => 'Automatic',
                'condition' => 'new',
                'status' => 'available',
                'cost_price' => 340000.00,
                'listing_price' => 410000.00,
                'branch_id' => $branchId,
            ],
            [
                'vin' => 'WBA8E1C5XLF655553',
                'make' => 'BMW',
                'model' => '740i M Sport',
                'year' => 2024,
                'trim' => 'Executive M Sport',
                'color' => 'Carbon Black Metallic',
                'mileage' => 12000,
                'fuel_type' => 'Hybrid',
                'transmission' => 'Automatic',
                'condition' => 'used',
                'status' => 'reserved',
                'cost_price' => 380000.00,
                'listing_price' => 445000.00,
                'branch_id' => $branchId,
            ],
            [
                'vin' => 'SALYA2EV3HA912224',
                'make' => 'Range Rover',
                'model' => 'Vogue SE',
                'year' => 2023,
                'trim' => 'Autobiography',
                'color' => 'Silicon Silver',
                'mileage' => 45000,
                'fuel_type' => 'Diesel',
                'transmission' => 'Automatic',
                'condition' => 'used',
                'status' => 'available',
                'cost_price' => 450000.00,
                'listing_price' => 510000.00,
                'branch_id' => $branchId,
            ]
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
