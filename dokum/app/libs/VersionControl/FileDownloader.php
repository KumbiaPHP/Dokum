<?php

namespace Dokum\libs\VersionControl;

/**
 * Class FileDownloader
 * 
 * This class is responsible for downloading files from a given URL using cURL.
 * It handles the initialization of a cURL session, setting of headers, and execution of the request.
 * 
 * @package Dokum\libs\VersionControl
 */
class FileDownloader
{
    /**
     * @var \CurlHandle The cURL handle for the HTTP request.
     */
    private $curlHandle;

    /**
     * Constructor for FileDownloader.
     *
     * Initializes a cURL session with the given URL and headers.
     *
     * @param string $url The URL to download the file from.
     * @param array $headers An array of HTTP headers to send with the request.
     */
    public function __construct(string $url, array $headers)
    {
        // Initialize cURL session
        $this->curlHandle = curl_init($url);
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->curlHandle, CURLOPT_HEADER, true);
    }

    /**
     * Executes the cURL request, detects errors, and downloads the file(s).
     *
     * @param string $savePath The directory path where the file should be saved
     * @return array An array containing 'fullPath', 'contentType', and 'filename'
     * @throws \Exception If a cURL error occurs, if the response indicates an error, or if unable to save the file
     */
    public function execute(string $savePath): array
    {
        // Execute cURL session and get the response
        $response = curl_exec($this->curlHandle);

        if (curl_errno($this->curlHandle)) {
            throw new \Exception("cURL Error: " . curl_error($this->curlHandle));
        }

        $httpCode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($this->curlHandle, CURLINFO_CONTENT_TYPE);

        // Separate headers and body
        $headerSize = curl_getinfo($this->curlHandle, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        curl_close($this->curlHandle);

        // Check for HTTP errors
        if ($httpCode >= 400) {
            throw new \Exception("HTTP Error: " . $httpCode . " - " . $this->getErrorMessage($body, $contentType));
        }

        // Extract filename from headers or generate one
        $filename = $this->extractFilename($headers, $contentType);

        // Save the file
        $fullPath = $this->saveFile($savePath, $filename, $body);

        return [
            'fullPath' => $fullPath,
            'contentType' => $contentType,
            'filename' => $filename
        ];
    }

    /**
     * Extracts error message from the response body.
     *
     * @param string $body The response body
     * @param string $contentType The content type of the response
     * @return string The error message
     */
    private function getErrorMessage(string $body, string $contentType): string
    {
        if (strpos($contentType, 'application/json') !== false) {
            $jsonBody = json_decode($body, true);
            return $jsonBody['message'] ?? $jsonBody['error'] ?? 'Unknown error';
        }
        return substr($body, 0, 100); // Return first 100 characters of non-JSON error
    }

    /**
     * Extracts filename from headers or generates one based on content type.
     *
     * @param string $headers The response headers
     * @param string $contentType The content type of the response
     * @return string The filename
     */
    private function extractFilename(string $headers, string $contentType): string
    {
        if (preg_match('/Content-Disposition:.*filename=["\'](.*)["\']/', $headers, $matches)) {
            return $matches[1];
        }

        // Generate filename based on content type if not provided
        $extension = $this->getExtensionFromContentType($contentType);
        return 'download_' . time() . '.' . $extension;
    }

    /**
     * Gets file extension based on content type.
     *
     * @param string $contentType The content type
     * @return string The file extension
     */
    private function getExtensionFromContentType(string $contentType): string
    {
        $map = [
            'application/zip' => 'zip',
            'application/x-tar' => 'tar',
            'application/gzip' => 'tar.gz'
        ];

        $parts = explode(';', $contentType);
        $mimeType = trim($parts[0]);

        return $map[$mimeType] ?? 'bin'; // Default to binary if unknown
    }

    /**
     * Saves the downloaded content to a file.
     *
     * @param string $savePath The directory path where the file should be saved
     * @param string $filename The filename to save as
     * @param string $content The content to save
     * @return string The full path of the saved file
     * @throws \Exception If unable to save the file
     */
    private function saveFile(string $savePath, string $filename, string $content): string
    {
        $fullPath = rtrim($savePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
        if (file_put_contents($fullPath, $content) === false) {
            throw new \Exception("Unable to save file: " . $fullPath);
        }
        return $fullPath;
    }
}