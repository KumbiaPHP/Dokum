<?php

namespace Dokum\libs\ArchiveExtractor;

interface ArchiveExtractorInterface
{
    /**
     * Extracts the contents of an archive.
     *
     * @param string $tempDir The temporary directory to extract the archive contents to
     * @param string $archivePath The path to the archive file
     
     * @return void
     * @throws ArchiveExtractionException If extraction fails
     */
    public function extract(string $tempDir, string $archivePath): void;
}
