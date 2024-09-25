<?php

namespace Dokum\models;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class MarkdownFileManager
 * 
 * This class provides utility methods for managing Markdown files and related operations.
 * It includes functionality for validating Markdown and image files in a directory,
 * as well as recursively removing directories.
 */
class MarkdownFileManager
{
    /**
     * Validates that all files in the given directory are markdown (.md) files or image files.
     *
     * @param string $dir The directory to validate
     * @return bool Returns true if all files are markdown or images, false otherwise
     */
    public static function validateMarkdownAndImageFiles($dir)
    {
        $validExtensions = ['md', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isFile() && !in_array(strtolower($file->getExtension()), $validExtensions)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Recursively removes a directory and its contents.
     *
     * @param string $dir The directory to remove
     */
    public static function removeDirectory($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        self::removeDirectory($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }
}
