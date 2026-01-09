<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FmRtCenter;
use App\Models\Pincode;
use Illuminate\Support\Facades\Log;
use Faker\Factory as Faker;

class FmRtCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_IN'); // Use Indian locale for realistic Indian data
        
        $this->command->info('Creating 10 dummy FM/RT Center records...');

        // Get some existing pincodes to link (if available)
        $availablePincodes = Pincode::where('status', 1)->limit(50)->get();
        
        // Indian cities and states for realistic data
        $cities = ['Mumbai', 'Delhi', 'Bangalore', 'Hyderabad', 'Chennai', 'Kolkata', 'Pune', 'Ahmedabad', 'Jaipur', 'Surat'];
        $states = ['Maharashtra', 'Delhi', 'Karnataka', 'Telangana', 'Tamil Nadu', 'West Bengal', 'Gujarat', 'Rajasthan'];
        
        // Bank names for realistic data
        $bankNames = ['State Bank of India', 'HDFC Bank', 'ICICI Bank', 'Axis Bank', 'Punjab National Bank', 'Bank of Baroda', 'Canara Bank', 'Union Bank of India'];
        
        // IFSC code prefixes
        $ifscPrefixes = ['SBIN', 'HDFC', 'ICIC', 'UTIB', 'PUNB', 'BARB', 'CNRB', 'UBIN'];

        for ($i = 1; $i <= 10; $i++) {
            // Generate unique email and mobile (exactly 10 digits)
            $email = 'fmrtcenter' . $i . '@taksh.com';
            $mobileNumber = '5' . str_pad($i, 9, '0', STR_PAD_LEFT); // e.g., 5000000001
            $ownerMobile = '4' . str_pad($i, 9, '0', STR_PAD_LEFT); // e.g., 4000000001
            $ownerEmail = 'fmrtowner' . $i . '@taksh.com';

            // Generate pincodes (6 digits)
            $pincode = $faker->numerify('######');
            $ownerPincode = $faker->numerify('######');

            // Generate coordinates (Indian locations)
            $latitude = $faker->latitude(8.0, 37.0); // India latitude range
            $longitude = $faker->longitude(68.0, 97.0); // India longitude range
            $ownerLatitude = $faker->latitude(8.0, 37.0);
            $ownerLongitude = $faker->longitude(68.0, 97.0);

            // Generate Aadhaar number (12 digits)
            $aadhaarNumber = $faker->numerify('##########') . $faker->numerify('##');

            // Generate PAN number (format: ABCDE1234F)
            $panCardNumber = strtoupper($faker->bothify('?????####?'));

            // Select random bank
            $bankIndex = $faker->numberBetween(0, count($bankNames) - 1);
            $bankName = $bankNames[$bankIndex];
            $ifscCode = $ifscPrefixes[$bankIndex] . $faker->numerify('0####');

            // Generate bank account number (10-18 digits)
            $bankAccountNumber = $faker->numerify(str_repeat('#', $faker->numberBetween(10, 18)));

            // Generate dummy images array
            $images = [];
            $imageCount = $faker->numberBetween(1, 4);
            for ($j = 0; $j < $imageCount; $j++) {
                $images[] = [
                    'img' => 'fm-rt-center/fm_rt_center_' . $i . '_image_' . ($j + 1) . '.jpg',
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
                        'file' => 'fm-rt-center/documents/fm_rt_center_' . $i . '_' . $key . '.pdf',
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
                        'file' => 'fm-rt-center/documents/fm_rt_center_' . $i . '_other_' . ($k + 1) . '.pdf',
                        'storage' => 'public'
                    ];
                }
            }

            // Create FM/RT Center
            $fmRtCenter = FmRtCenter::create([
                'center_name' => 'FM/RT Center ' . $i . ' - ' . $faker->company(),
                'full_address' => $faker->streetAddress() . ', ' . $faker->city() . ', ' . $faker->state(),
                'location' => $faker->city(),
                'pincode' => $pincode,
                'latitude' => (string) $latitude,
                'longitude' => (string) $longitude,
                'email' => $email,
                'mobile_number' => $mobileNumber,
                'owner_name' => $faker->name(),
                'owner_address' => $faker->streetAddress() . ', ' . $faker->city() . ', ' . $faker->state(),
                'owner_pincode' => $ownerPincode,
                'owner_latitude' => (string) $ownerLatitude,
                'owner_longitude' => (string) $ownerLongitude,
                'owner_mobile' => $ownerMobile,
                'owner_email' => $ownerEmail,
                'aadhaar_card' => 'fm-rt-center/documents/aadhaar_' . $i . '.pdf',
                'pan_card' => 'fm-rt-center/documents/pan_' . $i . '.pdf',
                'bank_name' => $bankName,
                'bank_account_number' => $bankAccountNumber,
                'bank_ifsc_code' => $ifscCode,
                'bank_branch' => $faker->city() . ' Branch',
                'bank_holder_name' => $faker->name(),
                'images' => !empty($images) ? $images : null,
                'documents' => !empty($documents) ? $documents : null,
                'state' => $faker->randomElement($states),
                'city' => $faker->randomElement($cities),
                'zone_id' => null, // Can be set later if zones exist
                'status' => $faker->boolean(80), // 80% active
            ]);

            // Link to random pincodes if available
            if ($availablePincodes->count() > 0) {
                $randomPincodeCount = $faker->numberBetween(1, min(5, $availablePincodes->count()));
                $selectedPincodes = $availablePincodes->random($randomPincodeCount);
                $fmRtCenter->pincodes()->sync($selectedPincodes->pluck('id')->toArray());
            }

            $this->command->info("Created FM/RT Center {$i}: {$fmRtCenter->center_name} (Email: {$email}, Mobile: {$mobileNumber})");
        }

        $this->command->info('');
        $this->command->info('Successfully created 10 FM/RT Center records!');
        $this->command->info('Note: Document files are dummy paths. Actual files need to be uploaded separately.');
    }
}

