<?php
namespace App\Command;

use App\Service\WeatherRecordManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for pulling weather data from a remote source.
 */
class PullWeatherDataCommand extends Command
{

    /**
     * @var WeatherRecordManager
     */
    private $weatherRecordManager;

    public function __construct(WeatherRecordManager $weatherRecordManager) {
        $this->weatherRecordManager = $weatherRecordManager;

        parent::__construct();
    }
    
    /**
     * Enforce that properties of the weather data object retrieved from source are correct.
     *
     * @param object $data Parsed data retrieved from source.
     */
    protected function enforceProperties($data) {
        if (!isset($data->date) || !is_string($data->date)) {
            throw new \Error('Incorrect date value in weather data.');
        }

        if (!isset($data->temperature) || !is_numeric($data->temperature)) {
            throw new \Error('Incorrect temperature value in weather data.');
        }

        if (!isset($data->chance_for_rain) || !is_numeric($data->chance_for_rain)) {
            throw new \Error('Incorrect chance for rain value in weather data.');
        }

        if (count(array_diff(array_keys(get_object_vars($data)), ['date', 'temperature', 'chance_for_rain'])) > 0) {
            throw new \Error('Unexpected properties found in weather data.');
        }
    }

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
        // TODO: Optionally add ability to change time zones.
        $date_yesterday = (new \DateTime('now -1 day'))->format("Y-m-d");

        // Check if record exists. If so, skip the rest of the operation.
        if ($this->weatherRecordManager->checkIfRecordExistsForDate($date_yesterday)) {
            $output->writeln('Record for date ' . $date_yesterday . ' already exists, check later.');

            return;
        }

        // Get data from source.
        $source_url = $input->getArgument('source_url');

        // Parse source data.
        $data = $this->parseSourceData($this->retrieveDataFromSource($source_url));

        // Ensure data has correct properties.
        $this->enforceProperties($data);

        // Add a record to the database.
        $weather_record = $this->weatherRecordManager->create($data->date, $data->temperature, $data->chance_for_rain);

        // Write to output.
        $output->writeln('Created a record for date ' . $weather_record->getDate()->format('Y-m-d') . '.');
    }

    /**
     * Parse source data from string to object.
     * 
     * @param string $source_data JSON string containing weather data.
     * 
     * @return object Returns an object containing weather data.
     */
    protected function parseSourceData($source_data) {
        $parsed_data = json_decode($source_data);

        if ($parsed_data === NULL) {
            throw new \Error('Source data is not valid JSON.');
        }

        return $parsed_data;
    }

    /**
     * Retrieve data from source.
     *
     * @param string $source_url URL from which data is to be retrieved.
     *
     * @return string String containing weather data.
     */
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
