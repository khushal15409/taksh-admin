<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LmCenter;
use App\Models\Pincode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Faker\Factory as Faker;
use App\Services\LogisticApiService;

class LmCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_IN'); // Use Indian locale for realistic Indian data
        $logisticApiService = new LogisticApiService();
        
        $this->command->info('Creating 10 dummy LM Center records...');
        $this->command->info('Note: Each LM Center will also be created in the logistic system via API call.');

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
            $email = 'lmcenter' . $i . '@taksh.com';
            $mobileNumber = '9' . str_pad($i, 9, '0', STR_PAD_LEFT); // e.g., 9000000001
            $ownerMobile = '8' . str_pad($i, 9, '0', STR_PAD_LEFT); // e.g., 8000000001
            $ownerEmail = 'owner' . $i . '@taksh.com';

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

            // Call external API to create user with role in logistic system
            $this->command->info("Calling logistic API for LM Center {$i}...");
            $apiResult = $logisticApiService->createUserWithRole($email, $mobileNumber, 'lm-center');

            if (!$apiResult['success']) {
                $errorMessage = $apiResult['message'] ?? 'Unknown error';
                $this->command->warn("⚠️  API call failed for LM Center {$i}: {$errorMessage}");
                $this->command->warn("   Continuing with database record creation...");
                Log::warning("LM Center Seeder: API call failed", [
                    'lm_center_number' => $i,
                    'email' => $email,
                    'mobile_number' => $mobileNumber,
                    'error' => $errorMessage,
                ]);
            } else {
                $this->command->info("✓ API call successful for LM Center {$i}");
            }

            // Create LM Center
            $lmCenter = LmCenter::create([
                'center_name' => 'LM Center ' . $i . ' - ' . $faker->company(),
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
                'owner_id' => 'OWNER' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'aadhaar_card' => 'lm-center/documents/aadhaar_' . $i . '.pdf',
                'aadhaar_number' => $aadhaarNumber,
                'aadhaar_verified' => $faker->boolean(70), // 70% verified
                'pan_card' => 'lm-center/documents/pan_' . $i . '.pdf',
                'pan_card_number' => $panCardNumber,
                'pan_verified' => $faker->boolean(70), // 70% verified
                'bank_name' => $bankName,
                'bank_account_number' => $bankAccountNumber,
                'bank_ifsc_code' => $ifscCode,
                'bank_branch' => $faker->city() . ' Branch',
                'bank_holder_name' => $faker->name(),
                'bank_document' => 'lm-center/documents/bank_' . $i . '.pdf',
                'state' => $faker->randomElement($states),
                'city' => $faker->randomElement($cities),
                'zone_id' => null, // Can be set later if zones exist
                'status' => $faker->boolean(80), // 80% active
                'thirty_min_delivery' => $faker->boolean(50), // 50% have 30 min delivery
                'normal_delivery' => $faker->boolean(90), // 90% have normal delivery
            ]);

            // Link to random pincodes if available
            if ($availablePincodes->count() > 0) {
                $randomPincodeCount = $faker->numberBetween(1, min(5, $availablePincodes->count()));
                $selectedPincodes = $availablePincodes->random($randomPincodeCount);
                $lmCenter->pincodes()->sync($selectedPincodes->pluck('id')->toArray());
            }

            $apiStatus = $apiResult['success'] ? '✓ API Success' : '⚠️  API Failed';
            $this->command->info("Created LM Center {$i}: {$lmCenter->center_name} (Email: {$email}, Mobile: {$mobileNumber}) [{$apiStatus}]");
        }

        $this->command->info('');
        $this->command->info('Successfully created 10 LM Center records!');
        $this->command->info('Note: Document files are dummy paths. Actual files need to be uploaded separately.');
        $this->command->info('Note: If any API calls failed, check the logs for details.');
    }
}

