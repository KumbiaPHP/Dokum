<?php

use Dokum\libs\VersionControl\VersionControlFactory;
use Dokum\models\BranchDownloader;

class SourceController extends AppController
{
    /**
     * Executes before any action in the controller.
     * 
     * This method starts a new session or resumes an existing one.
     *
     * @return void
     */
    final protected function before_filter()
    {
        session_start();
    }

    /**
     * Retrieves and sets the sources from the configuration.
     *
     * This method fetches the sources from the application's configuration
     * and assigns them to the 'sources' property of the controller. This
     * data can then be used in the corresponding view to display the list
     * of available sources.
     *
     * @return void
     * @throws KumbiaException If there's an error accessing the configuration
     */
    public function index(): void
    {
        $this->sources = Config::get('sources');
    }

    /**
     * Downloads all sources defined in the configuration.
     *
     * This method iterates through all sources and their tags, attempting to download
     * each one using the appropriate version control system.
     *
     * @return void
     */
    public function downloadAll(): void
    {
        // Get all sources from the configuration
        $sources = Config::get('sources');

        foreach ($sources as $sourceName => $sourceConfig) {
            // Initialize the appropriate VersionControl class based on the URL
            $versionControl = VersionControlFactory::createFromUrl($sourceConfig['url']);

            foreach ($sourceConfig['tags'] as $tag) {
                try {
                    $branchDownloader = new BranchDownloader($versionControl);
                    $branchDownloader->execute($sourceConfig['url'], $tag, $sourceName, $sourceConfig['token'] ?? null);

                    Flash::valid("Successfully downloaded: {$sourceName} - {$sourceConfig['url']}:{$tag}");
                } catch (Exception $e) {
                    Flash::error("Error downloading {$sourceName} - {$tag}: " . $e->getMessage());
                }
            }
        }

        Redirect::toAction('index');
    }

    /**
     * Downloads a specific source and its tags.
     *
     * This method attempts to download all tags for a given source using
     * the appropriate version control system.
     *
     * @param string $sourceName The name of the source to download
     * @return void
     */
    public function download(string $sourceName): void
    {
        $sources = Config::get('sources');

        if (!isset($sources[$sourceName])) {
            Flash::error("Source '{$sourceName}' not found.");
            Redirect::toAction('index');
            return;
        }

        $sourceConfig = $sources[$sourceName];
        $versionControl = VersionControlFactory::createFromUrl($sourceConfig['url']);

        foreach ($sourceConfig['tags'] as $tag) {
            try {
                $branchDownloader = new BranchDownloader($versionControl);
                $branchDownloader->execute($sourceConfig['url'], $tag, $sourceName, $sourceConfig['token'] ?? null);                

                Flash::valid("Successfully downloaded: {$sourceName} - {$sourceConfig['url']}:{$tag}");
            } catch (Exception $e) {
                Flash::error("Error downloading {$sourceName} - {$tag}: " . $e->getMessage());
            }
        }

        Redirect::toAction('index');
    }
}
