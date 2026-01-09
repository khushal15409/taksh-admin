<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Miniwarehouse;
use App\Models\LmCenter;
use App\Models\FmRtCenter;
use Illuminate\Support\Facades\Log;
use Faker\Factory as Faker;

class MiniwarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_IN'); // Use Indian locale for realistic Indian data
        
        $this->command->info('Creating 10 dummy Miniwarehouse records...');

        // Get existing records for mapping (if available)
        $availableLmCenters = LmCenter::where('status', 1)->limit(20)->get();
        $availableFmRtCenters = FmRtCenter::where('status', 1)->limit(20)->get();
        
        // Indian cities and states for realistic data
        $cities = ['Mumbai', 'Delhi', 'Bangalore', 'Hyderabad', 'Chennai', 'Kolkata', 'Pune', 'Ahmedabad', 'Jaipur', 'Surat'];
        $states = ['Maharashtra', 'Delhi', 'Karnataka', 'Telangana', 'Tamil Nadu', 'West Bengal', 'Gujarat', 'Rajasthan'];
        
        // Miniwarehouse types/names
        $miniwarehouseTypes = ['Mini', 'Small', 'Local', 'Satellite', 'Sub', 'Branch', 'Outlet', 'Point'];

        for ($i = 1; $i <= 10; $i++) {
            // Generate unique email and mobile (exactly 10 digits)
            $email = 'miniwarehouse' . $i . '@taksh.com';
            $mobileNumber = '6' . str_pad($i, 9, '0', STR_PAD_LEFT); // e.g., 6000000001

            // Generate pincode (6 digits)
            $pincode = $faker->numerify('######');

            // Generate coordinates (Indian locations)
            $latitude = $faker->latitude(8.0, 37.0); // India latitude range
            $longitude = $faker->longitude(68.0, 97.0); // India longitude range

            // Generate miniwarehouse name
            $miniwarehouseType = $faker->randomElement($miniwarehouseTypes);
            $city = $faker->randomElement($cities);
            $miniwarehouseName = $city . ' ' . $miniwarehouseType . ' Warehouse ' . $i;

            // Generate dummy images array
            $images = [];
            $imageCount = $faker->numberBetween(1, 4);
            for ($j = 0; $j < $imageCount; $j++) {
                $images[] = [
                    'img' => 'miniwarehouse/miniwarehouse_' . $i . '_image_' . ($j + 1) . '.jpg',
                    'storage' => 'public'
                ];
            }

            // Generate dummy documents array
            $documents = [];
            $documentTypes = [
                'rent_agreement' => 'Rent Agreement',
                'permission_letter_local' => 'Permission Letter',
                'electricity_bill' => 'Electricity Bill',
                'cin' => 'CIN Certificate',
                'gst' => 'GST Certificate',
                'coi' => 'Certificate of Incorporation'
            ];

            // Randomly add some documents (60% chance for each)
            foreach ($documentTypes as $key => $docName) {
                if ($faker->boolean(60)) {
                    $documents[$key] = [
                        'file' => 'miniwarehouse/documents/miniwarehouse_' . $i . '_' . $key . '.pdf',
                        'storage' => 'public'
                    ];
                }
            }

            // Add other documents (30% chance)
            if ($faker->boolean(30)) {
                $otherDocCount = $faker->numberBetween(1, 2);
                $documents['other_documents'] = [];
                for ($k = 0; $k < $otherDocCount; $k++) {
                    $documents['other_documents'][] = [
                        'name' => 'Other Document ' . ($k + 1),
                        'file' => 'miniwarehouse/documents/miniwarehouse_' . $i . '_other_' . ($k + 1) . '.pdf',
                        'storage' => 'public'
                    ];
                }
            }

            // Create Miniwarehouse
            $miniwarehouse = Miniwarehouse::create([
                'name' => $miniwarehouseName,
                'owner_name' => $faker->randomElement(['taksh', $faker->company()]),
                'full_address' => $faker->streetAddress() . ', ' . $faker->city() . ', ' . $faker->state() . ' - ' . $pincode,
                'location' => $faker->city(),
                'pincode' => $pincode,
                'latitude' => (string) $latitude,
                'longitude' => (string) $longitude,
                'state' => $faker->randomElement($states),
                'city' => $city,
                'email' => $email,
                'mobile_number' => $mobileNumber,
                'images' => !empty($images) ? $images : null,
                'documents' => !empty($documents) ? $documents : null,
                'zone_id' => null, // Can be set later if zones exist
                'status' => $faker->boolean(85), // 85% active
            ]);

            // Link to random LM centers if available
            if ($availableLmCenters->count() > 0) {
                $randomCount = $faker->numberBetween(0, min(5, $availableLmCenters->count()));
                if ($randomCount > 0) {
                    $selected = $availableLmCenters->random($randomCount);
                    $miniwarehouse->lmCenters()->sync($selected->pluck('id')->toArray());
                }
            }

            // Link to random FM/RT centers if available
            if ($availableFmRtCenters->count() > 0) {
                $randomCount = $faker->numberBetween(0, min(5, $availableFmRtCenters->count()));
                if ($randomCount > 0) {
                    $selected = $availableFmRtCenters->random($randomCount);
                    $miniwarehouse->fmRtCenters()->sync($selected->pluck('id')->toArray());
                }
            }

            $this->command->info("Created Miniwarehouse {$i}: {$miniwarehouse->name} (Email: {$email}, Mobile: {$mobileNumber})");
        }

        $this->command->info('');
        $this->command->info('Successfully created 10 Miniwarehouse records!');
        $this->command->info('Note: Image and document files are dummy paths. Actual files need to be uploaded separately.');
    }
}

