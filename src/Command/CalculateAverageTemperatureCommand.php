<?php
namespace App\Command;

use App\Service\WeatherRecordManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for calculating an average temperature for specified number of days.
 */
class CalculateAverageTemperatureCommand extends Command
{

    /**
     * @var WeatherRecordManager
     */
    private $weatherRecordManager;

    public function __construct(WeatherRecordManager $weatherRecordManager) {
        $this->weatherRecordManager = $weatherRecordManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:calculate-average-temperature')
            ->setDescription('Calculates an average temperature for specified number of days.')
            ->setHelp('Calculates an average temperature for specified number of days, counting backwards ' .
            'from last available record.')
            ->addArgument('number_of_days', InputArgument::REQUIRED, 'Number of days to calculate the average for.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }

}
