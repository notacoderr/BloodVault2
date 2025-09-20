<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BloodBank;
use Carbon\Carbon;

class BloodBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing blood bank records
        BloodBank::truncate();
        
        // Sample blood types and quantities
        $bloodTypes = [
            'A+' => 45,
            'A-' => 15,
            'B+' => 38,
            'B-' => 12,
            'AB+' => 8,
            'AB-' => 5,
            'O+' => 52,
            'O-' => 18
        ];
        
        foreach ($bloodTypes as $type => $quantity) {
            BloodBank::create([
                'donor' => 3, // Use existing user ID 3
                'blood_type' => $type,
                'acquisition_date' => Carbon::now()->subDays(rand(1, 30)),
                'expiration_date' => Carbon::now()->addDays(rand(30, 90)),
                'quantity' => $quantity,
                'status' => 1, // Approved status
            ]);
        }
        
        $this->command->info('Blood bank sample data seeded successfully!');
        $this->command->info('Created ' . count($bloodTypes) . ' blood type records with total ' . array_sum($bloodTypes) . ' units.');
    }
}
