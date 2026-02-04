<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the SQLite database to the storage directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = 'database_' . date('Y-m-d_H-i-s') . '.sqlite';
        $source = database_path('database.sqlite');
        $destinationFolder = storage_path('backups');

        // Ensure folder exists
        if (!file_exists($destinationFolder)) {
            mkdir($destinationFolder, 0755, true);
        }

        $destination = $destinationFolder . DIRECTORY_SEPARATOR . $filename;

        $this->info('Starting database backup...');

        if (copy($source, $destination)) {
            $this->info("✓ Database backed up successfully to: $filename");

            // Cleanup: Keep only the 7 most recent backups
            $files = glob($destinationFolder . '/*.sqlite');
            if (count($files) > 7) {
                // Sort by modification time (oldest first)
                usort($files, function($a, $b) {
                    return filemtime($a) - filemtime($b);
                });

                // Get all files except the last 7
                $toDelete = array_slice($files, 0, count($files) - 7);

                foreach($toDelete as $file) {
                    unlink($file);
                    $this->comment("Deleted old backup: " . basename($file));
                }
            }
        } else {
            $this->error("X Failed to backup database.");
            return 1;
        }

        return 0;
    }
}
