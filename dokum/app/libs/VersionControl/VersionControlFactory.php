<?php

namespace Dokum\libs\VersionControl;

use Dokum\libs\VersionControl\Adapters\Github as AdaptersGithub;
use Dokum\libs\VersionControl\Adapters\Gitlab as AdaptersGitlab;
use Dokum\libs\VersionControl\Exceptions\UnrecognizedSourceException;

class VersionControlFactory
{
    /**
     * Detects the source (GitHub or GitLab) and returns the appropriate class instance.
     *
     * @param string $url The repository URL
     * @return VersionControlInterface The appropriate class instance
     * @throws Exception If the source is not recognized
     */
    public static function createFromUrl(string $url): VersionControlInterface
    {
        if (strpos($url, 'github.com') !== false) {
            return new AdaptersGithub();
        } elseif (strpos($url, 'gitlab.com') !== false) {
            return new AdaptersGitlab();
        } else {
            throw new UnrecognizedSourceException($url);
        }
    }
}
