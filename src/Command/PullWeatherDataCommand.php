<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for pulling weather data from a remote source.
 */
class PullWeatherDataCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('app:pull-weather-data')
            ->setDescription('Pulls weather data from a remote source.')
            ->setHelp('Pulls weather data from a remote source and inserts a record ' .
            'into the local weather_record table.')
            ->addArgument('source_url', InputArgument::REQUIRED, 'Address of the weather data source, in format http://example.com/foo.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source_url = $input->getArgument('source_url');

        $source_data = $this->retrieveDataFromSource($source_url);
    }

    protected function retrieveDataFromSource($source_url) {
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
