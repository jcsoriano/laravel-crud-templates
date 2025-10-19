<?php

namespace JCSoriano\LaravelCrudTemplates;

use Binafy\LaravelStub\LaravelStub as BaseLaravelStub;
use Closure;
use Illuminate\Support\Facades\File;
use RuntimeException;

class LaravelStub extends BaseLaravelStub
{
    /**
     * Generate stub file.
     */
    public function generate(bool $force = false): bool
    {
        // Check path is valid
        if (! File::exists($this->from)) {
            throw new RuntimeException("The {$this->from} stub file does not exist, please enter a valid path.");
        }

        // Check destination path is valid
        if (! File::isDirectory($this->to)) {
            throw new RuntimeException('The given folder path is not valid.');
        }

        // Get file content
        $content = File::get($this->from);

        // Replace variables
        foreach ($this->replaces as $search => $value) {
            $content = str_replace("{{ $search }}", $value, $content);
        }

        // Process conditions
        if (count($this->conditions) !== 0) {
            foreach ($this->conditions as $condition => $value) {
                if ($value instanceof Closure) {
                    $value = $value();
                }

                if ($value) {
                    // Replace placeholders for conditions that are true
                    $content = preg_replace(
                        "/^[ \t]*{{ if $condition }}\s*\n(.*?)(?=^[ \t]*{{ endif }}\s*\n)/ms",
                        '$1',
                        $content
                    );
                } else {
                    // Remove the entire block for conditions that are false
                    $content = preg_replace(
                        "/^[ \t]*{{ if $condition }}\s*\n.*?^[ \t]*{{ endif }}\s*\n/ms",
                        '',
                        $content
                    );
                }
            }

            // Finally, clean up any remaining conditional tags and extra newlines
            // Remove any remaining conditional tags and their lines
            // $content = preg_replace("/^[ \t]*{{ if .*? }}\s*\n|^[ \t]*{{ endif .*? }}\s*\n/m", '', $content);
            $content = preg_replace("/^[ \t]*{{ if .*? }}\s*\n|^[ \t]*{{ endif }}\s*\n/m", "\n", $content);
            $content = preg_replace("/^[ \t]*\n/m", "\n", $content);
            $content = preg_replace("/\n\s*\n(\s*[)}\]])/", "\n$1", $content);
        }

        // Get correct path
        $path = $this->getPath();

        if ($this->moveStub) {
            File::move($this->from, $path); // Move the file
        } else {
            File::copy($this->from, $path); // Copy the file
        }

        // Put content and write on file
        File::put($path, $content);

        return true;
    }

    /**
     * Get final path.
     */
    private function getPath(): string
    {
        $path = "{$this->to}/{$this->name}";

        // Add extension
        if (! is_null($this->ext)) {
            $path .= ".$this->ext";
        }

        return $path;
    }
}
