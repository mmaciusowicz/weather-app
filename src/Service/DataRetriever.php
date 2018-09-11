<?php
namespace App\Service;

class DataRetriever {

    /**
     * Retrieve data from source.
     *
     * @param string $source_url URL from which data is to be retrieved.
     *
     * @return string String containing weather data.
     */
    public function retrieve($source_url) {
        if (filter_var($source_url, FILTER_VALIDATE_URL) === FALSE) {
            throw new \Error('Invalid source url');
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $source_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Execute request.
        $source_data = curl_exec($ch);

        // Check for errors.
        if ($errno = curl_errno($ch)) {
            throw new \Error(curl_strerror($errno));
        }

        // Check response code.
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response_code >= 400) {
            throw new \Error('Request to source failed with code: ' . $response_code);
        }

        curl_close($ch);

        return $source_data;
    }

}