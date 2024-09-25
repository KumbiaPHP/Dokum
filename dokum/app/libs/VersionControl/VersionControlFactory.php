<?php

namespace Dokum\libs\VersionControl;

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
        $host = strtolower(parse_url($url, PHP_URL_HOST));

        return match($host) {
            'github.com' => new Adapters\Github(),
            'gitlab.com' => new Adapters\Gitlab(),
            default => throw new Exceptions\UnrecognizedSourceException($url),
        };
    }
}
