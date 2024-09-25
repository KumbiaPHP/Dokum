<?php

namespace Dokum\libs\ArchiveExtractor;

abstract class ArchiveExtractor
{
    /**
     * Get the appropriate archive extractor based on the file path.
     *
     * @param string $filePath The path to the archive file
     * @return ArchiveExtractorInterface
     * @throws \InvalidArgumentException If the file type is not supported
     */
    public static function getExtractorByPath(string $filePath): ArchiveExtractorInterface
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        return match($extension) {
            'zip'       => new ZipArchiveExtractor(),
            'gz', 'tar' => new TarArchiveExtractor(),
            default => throw new \InvalidArgumentException("Unsupported file type: $extension"),
        };
    }

    /**
     * Get the appropriate archive extractor based on the file content.
     *
     * @param string $fileContent The content of the archive file
     * @return ArchiveExtractorInterface
     * @throws \InvalidArgumentException If the file type is not supported
     */
    public static function getExtractorByContent(string $fileContent): ArchiveExtractorInterface
    {
        $mimeType = self::getMimeType($fileContent);

        return match($mimeType) {
            'application/zip'     => new ZipArchiveExtractor(),
            'application/x-gzip',
            'application/x-tar'   => new TarArchiveExtractor(),
            default => throw new \InvalidArgumentException("Unsupported MIME type: $mimeType"),
        };
    }

    /**
     * Get the MIME type of the file content.
     *
     * @param string $fileContent The content of the file
     * @return string The MIME type
     */
    private static function getMimeType(string $fileContent): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->buffer($fileContent);
    }
}
