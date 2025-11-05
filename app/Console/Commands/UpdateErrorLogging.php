<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateErrorLogging extends Command
{
    protected $signature = 'errors:standardize';
    protected $description = 'Update all controllers to use standardized error logging';

    public function handle()
    {
        $this->info('Updating controllers with standardized error logging...');
        
        $controllersPath = app_path('Http/Controllers');
        $files = File::allFiles($controllersPath);
        
        $updatedCount = 0;
        $filesUpdated = [];
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $content = File::get($file->getRealPath());
                $original = $content;
                
                // Add trait import if not present
                if (!str_contains($content, 'use App\Traits\LogsErrors;')) {
                    $content = preg_replace(
                        '/^(use [^;]+;\s*)+/m',
                        "$0use App\Traits\LogsErrors;\n",
                        $content,
                        1
                    );
                }
                
                // Add trait to class if not present
                if (!str_contains($content, 'use LogsErrors;')) {
                    $content = preg_replace(
                        '/(class\s+\w+\s+extends\s+\w+\s*\{)/m',
                        "$1\n    use LogsErrors;\n",
                        $content,
                        1
                    );
                }
                
                // Replace old error logging pattern 1: "An error occurred with id"
                $pattern1 = '/(\$unique_id = floor\(time\(\) - 999999999\);)\s*' .
                           'Log::channel\(\'error_log\'\)->error\(\'An error occurred with id \' \. \$unique_id[^}]+}\]\);/s';
                           
                if (preg_match($pattern1, $content)) {
                    // This is complex, so we'll handle it differently
                }
                
                if ($original !== $content) {
                    File::put($file->getRealPath(), $content);
                    $updatedCount++;
                    $filesUpdated[] = $file->getRelativePathname();
                    $this->line("✓ Updated: {$file->getRelativePathname()}");
                }
            }
        }
        
        $this->info("\nCompleted! Updated {$updatedCount} controller(s).");
        
        if (!empty($filesUpdated)) {
            $this->info("\nFiles updated:");
            foreach ($filesUpdated as $file) {
                $this->line("  - {$file}");
            }
        }
        
        return Command::SUCCESS;
    }
}

