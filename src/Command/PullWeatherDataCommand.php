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
            ->addArgument('source', InputArgument::REQUIRED, 'Address of the weather data source, in format http://example.com/foo.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
