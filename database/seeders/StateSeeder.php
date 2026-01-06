<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting State data import...');

        // Check if states table is empty
        $existingCount = DB::table('states')->count();
        if ($existingCount > 0) {
            $this->command->warn("States table already has {$existingCount} records. Skipping import.");
            return;
        }

        $sqlFile = database_path('states (2).sql');
        
        if (!File::exists($sqlFile)) {
            $this->command->error("SQL file not found: {$sqlFile}");
            return;
        }

        $content = File::get($sqlFile);
        
        // Extract all INSERT statements (may span multiple lines)
        preg_match_all(
            "/INSERT INTO `states` \(`id`, `name`, `country_id`, `active_status`, `test_status`\) VALUES\s*(.*?);/s",
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

        foreach ($matches[1] as $valuesBlock) {
            // Parse each row: (id, 'name', country_id, active_status, test_status)
            // Handle multi-line values and escaped quotes
            preg_match_all(
                "/\((\d+),\s*'((?:[^'\\\\]|\\\\'|'')*)',\s*(\d+),\s*(\d+),\s*(NULL|'[^']*')\)/",
                $valuesBlock,
                $rows,
                PREG_SET_ORDER
            );

            foreach ($rows as $row) {
                $id = (int) $row[1];
                $name = str_replace(["''", "\\'"], "'", $row[2]); // Handle escaped quotes
                $countryId = (int) $row[3];
                $activeStatus = (int) $row[4];
                $testStatus = $row[5] === 'NULL' ? null : trim($row[5], "'");

                $insertData[] = [
                    'id' => $id,
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Insert in batches
                if (count($insertData) >= $batchSize) {
                    try {
                        DB::table('states')->insert($insertData);
                        $totalInserted += count($insertData);
                        $this->command->info("Inserted {$totalInserted} states...");
                    } catch (\Exception $e) {
                        $this->command->error("Error inserting batch: " . $e->getMessage());
                    }
                    $insertData = [];
                }
            }
        }

        // Insert remaining data
        if (!empty($insertData)) {
            try {
                DB::table('states')->insert($insertData);
                $totalInserted += count($insertData);
            } catch (\Exception $e) {
                $this->command->error("Error inserting final batch: " . $e->getMessage());
            }
        }

        // Reset auto-increment to max ID + 1
        if ($totalInserted > 0) {
            $maxId = DB::table('states')->max('id');
            if ($maxId) {
                $driver = DB::getDriverName();
                if ($driver === 'mysql') {
                    DB::statement("ALTER TABLE states AUTO_INCREMENT = " . ($maxId + 1));
                } elseif ($driver === 'sqlite') {
                    // SQLite doesn't support AUTO_INCREMENT reset, but it's fine
                }
            }
        }

        $this->command->info("Successfully imported {$totalInserted} states!");
    }
}
