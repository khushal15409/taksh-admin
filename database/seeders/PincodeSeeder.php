<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PincodeSeeder extends Seeder
{
    /**
     * Chunk size for batch inserts (configurable)
     * Adjust based on your server's memory and performance requirements
     * Recommended: 500-1000 for optimal balance between speed and memory usage
     */
    private int $chunkSize = 1000;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Pincode data import...');

        // Get CSV file path from database folder
        $csvFile = database_path('pincode.csv');

        // Validate CSV file exists
        if (!File::exists($csvFile)) {
            $this->command->error("CSV file not found: {$csvFile}");
            return;
        }

        // Check file size for progress estimation
        $fileSize = File::size($csvFile);
        $this->command->info("CSV file size: " . number_format($fileSize / 1024 / 1024, 2) . " MB");

        // Truncate pincodes table before importing fresh data
        // This clears all existing records to ensure clean import
        // Note: Temporarily disable foreign key checks to allow truncate
        // since pincodes table is referenced by other tables (fm_rt_center_pincode, lm_center_pincode)
        $this->command->info("Truncating pincodes table...");
        
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate the table
        DB::table('pincodes')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info("Pincodes table truncated successfully.");

        // Disable query logging to save memory during bulk inserts
        // This is critical for large datasets to prevent memory overflow
        DB::connection()->disableQueryLog();

        // Open CSV file handle for memory-efficient reading
        // Using fopen instead of loading entire file into memory
        $handle = fopen($csvFile, 'r');
        
        if ($handle === false) {
            $this->command->error("Failed to open CSV file: {$csvFile}");
            return;
        }

        try {
            // Wrap entire import in a database transaction for data integrity
            // If any error occurs, all inserts will be rolled back
            DB::beginTransaction();

            // Read and skip header row
            $headers = fgetcsv($handle);
            if ($headers === false) {
                throw new \Exception("Failed to read CSV headers");
            }

            // Normalize header names (trim whitespace, lowercase for comparison)
            $headers = array_map('trim', $headers);
            $this->command->info("CSV Headers: " . implode(', ', $headers));

            // Validate headers match expected columns
            $expectedColumns = [
                'circlename', 'regionname', 'divisionname', 'officename', 
                'pincode', 'officetype', 'delivery', 'district', 
                'statename', 'latitude', 'longitude'
            ];
            
            $headerMap = [];
            foreach ($expectedColumns as $expectedCol) {
                $found = false;
                foreach ($headers as $index => $header) {
                    if (strtolower(trim($header)) === strtolower($expectedCol)) {
                        $headerMap[$expectedCol] = $index;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $this->command->warn("Warning: Expected column '{$expectedCol}' not found in CSV headers");
                }
            }

            $chunk = [];
            $totalProcessed = 0;
            $totalInserted = 0;
            $totalSkipped = 0;
            $errors = [];

            $this->command->info("Starting import with chunk size: {$this->chunkSize}");

            // Process CSV file line by line to avoid memory overflow
            while (($row = fgetcsv($handle)) !== false) {
                $totalProcessed++;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    // Map CSV columns to database columns
                    $data = $this->mapRowToData($row, $headerMap);

                    // Skip if pincode is missing (required field)
                    if (empty($data['pincode'])) {
                        $totalSkipped++;
                        continue;
                    }

                    // Add timestamps
                    $data['created_at'] = now();
                    $data['updated_at'] = now();

                    // Add to chunk for batch insert (all records will be inserted, including duplicates)
                    $chunk[] = $data;

                    // Insert chunk when it reaches configured size
                    if (count($chunk) >= $this->chunkSize) {
                        $inserted = $this->insertChunk($chunk);
                        $totalInserted += $inserted;
                        $chunk = [];

                        // Progress feedback every chunk
                        if ($totalProcessed % ($this->chunkSize * 10) === 0) {
                            $this->command->info("Processed: {$totalProcessed} rows | Inserted: {$totalInserted} | Skipped: {$totalSkipped}");
                        }
                    }
                } catch (\Exception $e) {
                    // Log errors but continue processing
                    $errors[] = "Row {$totalProcessed}: " . $e->getMessage();
                    if (count($errors) <= 10) {
                        $this->command->warn("Error processing row {$totalProcessed}: " . $e->getMessage());
                    }
                    $totalSkipped++;
                }
            }

            // Insert remaining records in chunk
            if (!empty($chunk)) {
                $inserted = $this->insertChunk($chunk);
                $totalInserted += $inserted;
            }

            // Commit transaction if everything succeeded
            DB::commit();

            // Re-enable query logging
            DB::connection()->enableQueryLog();

            // Close file handle
            fclose($handle);

            // Display summary
            $this->command->info("=== Import Summary ===");
            $this->command->info("Total rows processed: {$totalProcessed}");
            $this->command->info("Successfully inserted: {$totalInserted}");
            if ($totalSkipped > 0) {
                $this->command->warn("Skipped (missing pincode): {$totalSkipped}");
            }
            if (!empty($errors)) {
                $this->command->error("Errors encountered: " . count($errors));
                if (count($errors) > 10) {
                    $this->command->warn("Showing first 10 errors. Total errors: " . count($errors));
                }
            }
            $this->command->info("Pincode import completed successfully!");

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            DB::connection()->enableQueryLog();
            fclose($handle);
            $this->command->error("Import failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Map CSV row to database data array
     * Handles data type conversion and null values
     */
    private function mapRowToData(array $row, array $headerMap): array
    {
        $data = [];

        // Helper function to get value from row by column name
        $getValue = function ($columnName) use ($row, $headerMap) {
            if (!isset($headerMap[$columnName])) {
                return null;
            }
            $index = $headerMap[$columnName];
            return isset($row[$index]) ? trim($row[$index]) : null;
        };

        // Map string columns
        $data['circlename'] = $getValue('circlename') ?: null;
        $data['regionname'] = $getValue('regionname') ?: null;
        $data['divisionname'] = $getValue('divisionname') ?: null;
        $data['officename'] = $getValue('officename') ?: null;
        $data['pincode'] = $getValue('pincode') ?: null;
        $data['officetype'] = $getValue('officetype') ?: null;
        $data['delivery'] = $getValue('delivery') ?: null;
        $data['district'] = $getValue('district') ?: null;
        $data['statename'] = $getValue('statename') ?: null;

        // Handle latitude - convert "NA" or empty to null, ensure numeric
        $latitude = $getValue('latitude');
        if (empty($latitude) || strtoupper($latitude) === 'NA' || !is_numeric($latitude)) {
            $data['latitude'] = null;
        } else {
            $data['latitude'] = (float) $latitude;
        }

        // Handle longitude - convert "NA" or empty to null, ensure numeric
        $longitude = $getValue('longitude');
        if (empty($longitude) || strtoupper($longitude) === 'NA' || !is_numeric($longitude)) {
            $data['longitude'] = null;
        } else {
            $data['longitude'] = (float) $longitude;
        }

        return $data;
    }

    /**
     * Insert chunk of records using batch insert
     * Returns number of successfully inserted records
     * All records will be inserted, including duplicates
     */
    private function insertChunk(array $chunk): int
    {
        try {
            // Use insert() for batch insert - much faster than individual inserts
            // Laravel will automatically handle the batch insert
            DB::table('pincodes')->insert($chunk);
            return count($chunk);
        } catch (\Exception $e) {
            // If batch insert fails, try inserting one by one to identify problematic records
            $inserted = 0;
            foreach ($chunk as $record) {
                try {
                    DB::table('pincodes')->insert($record);
                    $inserted++;
                } catch (\Exception $recordError) {
                    // Log individual record errors but continue
                    Log::warning("Failed to insert pincode record", [
                        'pincode' => $record['pincode'] ?? 'unknown',
                        'error' => $recordError->getMessage()
                    ]);
                }
            }
            return $inserted;
        }
    }

    /**
     * Set chunk size (useful for testing or different environments)
     */
    public function setChunkSize(int $size): self
    {
        $this->chunkSize = max(100, min(5000, $size)); // Clamp between 100 and 5000
        return $this;
    }
}

/*
 * ALTERNATIVE ULTRA-FAST APPROACH USING LOAD DATA INFILE (MySQL only)
 * 
 * For MySQL databases, you can use LOAD DATA INFILE which is significantly faster
 * than chunked inserts. However, this requires:
 * 1. MySQL server access
 * 2. File must be accessible by MySQL server (not just PHP)
 * 3. Proper permissions (FILE privilege)
 * 4. CSV must be properly formatted
 * 
 * Example implementation:
 * 
 * public function run(): void
 * {
 *     $csvFile = database_path('pincode.csv');
 *     $tableName = 'pincodes';
 *     
 *     // Ensure file is accessible by MySQL
 *     // You may need to copy file to MySQL's secure_file_priv directory
 *     
 *     $query = "
 *         LOAD DATA INFILE '{$csvFile}'
 *         INTO TABLE {$tableName}
 *         FIELDS TERMINATED BY ','
 *         ENCLOSED BY '\"'
 *         ESCAPED BY '\\\\'
 *         LINES TERMINATED BY '\\n'
 *         IGNORE 1 ROWS
 *         (circlename, regionname, divisionname, officename, pincode, 
 *          officetype, delivery, district, statename, latitude, longitude)
 *         SET created_at = NOW(), updated_at = NOW()
 *     ";
 *     
 *     DB::statement($query);
 * }
 * 
 * Note: This approach bypasses Laravel's query builder and requires careful
 * handling of file paths, permissions, and CSV formatting. Use with caution.
 */

