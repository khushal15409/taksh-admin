<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting City data import...');

        // Check if cities table is empty
        $existingCount = DB::table('cities')->count();
        if ($existingCount > 0) {
            $this->command->warn("Cities table already has {$existingCount} records. Skipping import.");
            return;
        }

        $sqlFile = database_path('cities (2).sql');
        
        if (!File::exists($sqlFile)) {
            $this->command->error("SQL file not found: {$sqlFile}");
            return;
        }

        $content = File::get($sqlFile);
        
        // Extract all INSERT statements (may span multiple lines)
        preg_match_all(
            "/INSERT INTO `cities` \(`id`, `name`, `state_id`, `active_status`\) VALUES\s*(.*?);/s",
            $content,
            $matches
        );

        if (empty($matches[1])) {
            $this->command->error('No INSERT statements found in SQL file.');
            return;
        }

        $insertData = [];
        $batchSize = 500;
        $totalInserted = 0;
        $skippedCount = 0;

        foreach ($matches[1] as $valuesBlock) {
            // Parse each row: (id, 'name', state_id, active_status)
            // Handle multi-line values and escaped quotes
            preg_match_all(
                "/\((\d+),\s*'((?:[^'\\\\]|\\\\'|'')*)',\s*(\d+),\s*(\d+|NULL)\)/",
                $valuesBlock,
                $rows,
                PREG_SET_ORDER
            );

            foreach ($rows as $row) {
                $id = (int) $row[1];
                $name = str_replace(["''", "\\'"], "'", $row[2]); // Handle escaped quotes
                $stateId = (int) $row[3];
                $activeStatus = isset($row[4]) && $row[4] !== 'NULL' ? (int) $row[4] : 0;

                // Verify state exists
                $stateExists = DB::table('states')->where('id', $stateId)->exists();
                if (!$stateExists) {
                    $skippedCount++;
                    if ($skippedCount <= 10) {
                        $this->command->warn("Skipping city '{$name}' (ID: {$id}) - State ID {$stateId} does not exist.");
                    }
                    continue;
                }

                $insertData[] = [
                    'id' => $id,
                    'state_id' => $stateId,
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Insert in batches
                if (count($insertData) >= $batchSize) {
                    try {
                        DB::table('cities')->insert($insertData);
                        $totalInserted += count($insertData);
                        $this->command->info("Inserted {$totalInserted} cities...");
                    } catch (\Exception $e) {
                        $this->command->error("Error inserting batch: " . $e->getMessage());
                    }
                    $insertData = [];
                }
            }
        }

        if ($skippedCount > 10) {
            $this->command->warn("Skipped {$skippedCount} cities due to missing states.");
        }

        // Insert remaining data
        if (!empty($insertData)) {
            try {
                DB::table('cities')->insert($insertData);
                $totalInserted += count($insertData);
            } catch (\Exception $e) {
                $this->command->error("Error inserting final batch: " . $e->getMessage());
            }
        }

        // Reset auto-increment to max ID + 1
        if ($totalInserted > 0) {
            $maxId = DB::table('cities')->max('id');
            if ($maxId) {
                $driver = DB::getDriverName();
                if ($driver === 'mysql') {
                    DB::statement("ALTER TABLE cities AUTO_INCREMENT = " . ($maxId + 1));
                } elseif ($driver === 'sqlite') {
                    // SQLite doesn't support AUTO_INCREMENT reset, but it's fine
                }
            }
        }

        $this->command->info("Successfully imported {$totalInserted} cities!");
        if ($skippedCount > 0) {
            $this->command->warn("Skipped {$skippedCount} cities due to missing states.");
        }
    }
}
