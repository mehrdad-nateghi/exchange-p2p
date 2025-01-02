<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckLanguageFiles extends Command
{
    protected $signature = 'lang:check {file? : Specific file to check (optional)}';
    protected $description = 'Check language files for missing keys across different languages';

    public function handle()
    {
        $langPath = lang_path();
        $languages = array_diff(scandir($langPath), ['.', '..']);
        $fileToCheck = $this->argument('file') ?? null;

        $allKeys = [];
        $missingKeys = [];
        $files = [];

        // First, collect all files and keys
        foreach ($languages as $lang) {
            if (!is_dir($langPath . '/' . $lang)) continue;

            if ($fileToCheck) {
                if (File::exists($langPath . '/' . $lang . '/' . $fileToCheck)) {
                    $files[] = $fileToCheck;
                }
            } else {
                $langFiles = array_diff(scandir($langPath . '/' . $lang), ['.', '..']);
                $files = array_unique(array_merge($files, $langFiles));
            }
        }

        foreach ($files as $file) {
            $this->info("\n📝 Checking file: " . $file);

            // Create headers for the table
            $tableRows = [];
            $tableHeaders = ['Key', 'Missing In Languages'];

            foreach ($languages as $lang) {
                if (!is_dir($langPath . '/' . $lang)) continue;

                $filePath = $langPath . '/' . $lang . '/' . $file;
                if (!File::exists($filePath)) {
                    $this->warn("⚠️ File '$file' doesn't exist in '$lang' language");
                    continue;
                }

                $keys = $this->flattenArray(require $filePath);
                $allKeys[$file] = array_unique(array_merge($allKeys[$file] ?? [], array_keys($keys)));

                foreach ($allKeys[$file] as $key) {
                    if (!isset($keys[$key])) {
                        $missingKeys[$file][$key][] = $lang;
                    }
                }
            }

            if (isset($missingKeys[$file])) {
                foreach ($missingKeys[$file] as $key => $missingInLangs) {
                    $tableRows[] = [
                        $key,
                        implode(', ', $missingInLangs)
                    ];
                }

                $this->table($tableHeaders, $tableRows);
            } else {
                $this->info("✅ All keys are consistent across languages for file: $file");
            }
        }
    }

    private function flattenArray(array $array, $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                // If array value is empty, add it as a key
                if (empty($value)) {
                    $result[$prefix . $key] = '';
                } else {
                    $result = array_merge($result, $this->flattenArray($value, $prefix . $key . '.'));
                }
            } else {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }
}
