<?php

namespace Dokum\libs\VersionControl;

use Dokum\libs\VersionControl\Adapters\Github as AdaptersGithub;
use Dokum\libs\VersionControl\Adapters\Gitlab as AdaptersGitlab;
use Dokum\libs\VersionControl\Exceptions\UnrecognizedSourceException;
use Exception;

/**
 * The VersionControlFactory class is responsible for creating instances
 * of version control adapter classes based on the given repository URL.
 */
class VersionControlFactory
{
    /**
     * Detects the source (GitHub or GitLab) and returns the appropriate class instance.
     *
     * @param string $url The repository URL
     *
     * @return VersionControlInterface The appropriate class instance
     * @throws Exception If the source is not recognized
     * @throws UnrecognizedSourceException If the adapter for service does not found
     */
    public static function createFromUrl(string $url): VersionControlInterface
    {
        if (str_contains($url, 'github.com')) {
            return new AdaptersGithub();
        } elseif (str_contains($url, 'gitlab.com')) {
            return new AdaptersGitlab();
        } else {
            throw new UnrecognizedSourceException($url);
        }
    }
}
