<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\HandlesFileUploads;
use App\Models\User;
use App\Models\Company;

class CleanupOrphanedFiles extends Command
{
    use HandlesFileUploads;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:cleanup-orphaned {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove files that are no longer referenced in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('Running in dry-run mode - no files will be deleted');
        }

        $totalDeleted = 0;

        // Cleanup user avatars
        $totalDeleted += $this->cleanupUserAvatars($dryRun);

        // Cleanup company logos
        $totalDeleted += $this->cleanupCompanyLogos($dryRun);

        $this->info("Cleanup completed! Total files " . ($dryRun ? 'that would be' : '') . " deleted: {$totalDeleted}");

        return 0;
    }

    private function cleanupUserAvatars(bool $dryRun): int
    {
        $this->info('Cleaning up user avatars...');
        
        $activeAvatars = User::whereNotNull('avatar')->pluck('avatar')->map(function($path) {
            return basename($path);
        })->toArray();

        $deleted = 0;
        if ($dryRun) {
            $deleted = $this->countOrphanedFiles('user_avatar', $activeAvatars);
            $this->line("Would delete {$deleted} orphaned user avatar files");
        } else {
            $deleted = $this->cleanupOrphanedFiles('user_avatar', $activeAvatars);
            $this->line("Deleted {$deleted} orphaned user avatar files");
        }

        return $deleted;
    }

    private function cleanupCompanyLogos(bool $dryRun): int
    {
        $this->info('Cleaning up company logos...');
        
        $activeLogos = Company::whereNotNull('logo')->pluck('logo')->map(function($path) {
            return basename($path);
        })->toArray();

        $deleted = 0;
        if ($dryRun) {
            $deleted = $this->countOrphanedFiles('company_logo', $activeLogos);
            $this->line("Would delete {$deleted} orphaned company logo files");
        } else {
            $deleted = $this->cleanupOrphanedFiles('company_logo', $activeLogos);
            $this->line("Deleted {$deleted} orphaned company logo files");
        }

        return $deleted;
    }

    private function countOrphanedFiles(string $directory, array $activeFiles): int
    {
        $count = 0;
        $files = \Storage::disk('public')->allFiles($directory);
        
        foreach ($files as $file) {
            $filename = basename($file);
            
            // Pular thumbnails (serão contadas com suas imagens principais)
            if (str_starts_with($filename, 'thumb_')) {
                continue;
            }
            
            // Se o arquivo não está na lista de ativos, contar
            if (!in_array($filename, $activeFiles)) {
                $count++;
                $this->line("  - Would delete: {$file}");
            }
        }
        
        return $count;
    }
}
