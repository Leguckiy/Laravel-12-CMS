<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AddImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy images from resources/seed-images to storage/app/public';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting image copying...');

        $sourcePath = resource_path('seed-images');
        $destinationPath = Storage::disk('public')->path('');

        if (! File::exists($sourcePath)) {
            $this->error("Source directory does not exist: {$sourcePath}");
            $this->info('Please create resources/seed-images/ directory with your images');

            return self::FAILURE;
        }

        // Clean destination directory before copying, but preserve .gitignore
        if (File::isDirectory($destinationPath)) {
            $this->info('Cleaning destination directory...');
            $gitignorePath = $destinationPath.DIRECTORY_SEPARATOR.'.gitignore';
            $gitignoreContent = null;

            // Save .gitignore if it exists
            if (File::exists($gitignorePath)) {
                $gitignoreContent = File::get($gitignorePath);
            }

            // Clean directory
            File::cleanDirectory($destinationPath);

            // Restore .gitignore if it existed
            if ($gitignoreContent !== null) {
                File::put($gitignorePath, $gitignoreContent);
            }
        }

        $copied = $this->copyDirectory($sourcePath, $destinationPath);

        $this->info("Completed! Copied {$copied} file(s).");

        return self::SUCCESS;
    }

    /**
     * Recursively copy directory contents.
     */
    private function copyDirectory(string $source, string $destination): int
    {
        $copied = 0;

        if (! File::isDirectory($source)) {
            return $copied;
        }

        if (! File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $source = realpath($source);
        $files = File::allFiles($source);

        foreach ($files as $file) {
            $filePath = $file->getPathname();
            $relativePath = str_replace($source.DIRECTORY_SEPARATOR, '', $filePath);
            $relativePath = ltrim($relativePath, DIRECTORY_SEPARATOR);
            $destinationFile = $destination.DIRECTORY_SEPARATOR.$relativePath;
            $destinationDir = dirname($destinationFile);

            if (! File::isDirectory($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
            }

            File::copy($filePath, $destinationFile);
            $copied++;
        }

        return $copied;
    }
}
