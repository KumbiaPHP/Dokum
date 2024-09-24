<?php

namespace Dokum\models;

use FilesystemIterator;
use Parsedown;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class ParseToHtml
 *
 * This class is responsible for converting Markdown files to HTML and organizing them in a specified directory structure.
 */
class ParseToHtml
{
    private Parsedown $parsedown;

    /**
     * Class constructor.
     *
     * @param string $tempDir Temporary directory path.
     * @param string $destDir Destination directory path.
     * @return void
     */
    public function __construct(
        private string $tempDir,
        private string $destDir
    )
    {
        $this->parsedown = new Parsedown();
        $this->tempDir = rtrim($tempDir, '/');
        $this->destDir = rtrim($destDir, '/');
    }

    /**
     * Executes the process of cleaning, converting, and renaming files in the repository.
     *
     * @param string $repositoryName The name of the repository to process.
     * @return void
     */
    public function execute(string $repositoryName): void
    {
        $cleanRepositoryName = $this->cleanFolderName($repositoryName);

        // Remove old content
        $this->removeOldContent();

        $firstFolder = scandir($this->tempDir)[2]; // Skip . and ..

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->tempDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $sourcePath = $file->getPathname();
            
            $relativePath = substr($sourcePath, strlen($this->tempDir) + 1);

            $destPath = $this->destDir . '/' . $relativePath;

            if ($file->isDir()) {
                if (!is_dir($destPath)) {
                    mkdir($destPath, 0755, true);
                }
            } elseif ($file->getExtension() === 'md') {
                $this->convertFile($sourcePath, $destPath);
            }
        }

         // Rename the destination folder to $cleanRepositoryName after processing
        $newDestDir = $this->destDir . '/' . $cleanRepositoryName;

        rename($this->destDir . '/' . $firstFolder, $newDestDir);
        $this->destDir = $newDestDir;
    }

    /**
     * Removes old content from the destination directory.
     *
     * This method deletes all files and subdirectories within the destination directory,
     * and then removes the destination directory itself.
     *
     * @return void
     */
    private function removeOldContent(): void
    {
        if (!is_dir($this->destDir)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->destDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }

        // Remove the destination directory itself
        rmdir($this->destDir);
    }

    /**
     * Converts a markdown file to HTML and saves it to the destination path.
     *
     * @param string $sourcePath Path to the source markdown file.
     * @param string $destPath Path to the destination file.
     * @return void
     */
    private function convertFile(string $sourcePath, string $destPath): void
    {
        $markdown = file_get_contents($sourcePath);
        $html = $this->parsedown->text($markdown);
        
        $destPath = preg_replace('/\.md$/', '.phtml', $destPath);
        $destDir = dirname($destPath);
        
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }
        
        file_put_contents($destPath, $html);
    }

    /**
     * Cleans the given folder name by removing any characters that are not
     * alphanumeric, dash, or underscore. If the name becomes empty after
     * cleaning, it defaults to 'default_folder'.
     *
     * @param string $name The folder name to clean.
     * @return string The cleaned folder name.
     */
    private function cleanFolderName(string $name): string
    {
        // Remove any characters that are not alphanumeric, dash, or underscore
        $cleaned = preg_replace('/[^a-zA-Z0-9-_]/', '', $name);
        
        // Ensure the name is not empty after cleaning
        if (empty($cleaned)) {
            $cleaned = 'default_folder';
        }
        
        return $cleaned;
    }
}
