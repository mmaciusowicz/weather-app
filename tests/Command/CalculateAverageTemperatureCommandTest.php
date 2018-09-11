<?php

use App\Entity\WeatherRecord;
use App\Service\WeatherRecordManager;
use App\Command\CalculateAverageTemperatureCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CalculateAverageTemperatureCommandTest extends KernelTestCase {
    public function testExecute() {
        // Add test records to the database.
        $weather_record_1 = new WeatherRecord();

        $weather_record_1->setDate(\DateTime::createFromFormat('Y-m-d', '2018-09-09'));

        $weather_record_1->setTemperature(15);

        $weather_record_1->setChanceForRain(40);

        $this->entityManager->persist($weather_record_1);

        $weather_record_2 = new WeatherRecord();

        $weather_record_2->setDate(\DateTime::createFromFormat('Y-m-d', '2018-09-10'));

        $weather_record_2->setTemperature(12);

        $weather_record_2->setChanceForRain(65);

        $this->entityManager->persist($weather_record_2);

        $weather_record_3 = new WeatherRecord();

        $weather_record_3->setDate(\DateTime::createFromFormat('Y-m-d', '2018-09-11'));

        $weather_record_3->setTemperature(-2);

        $weather_record_3->setChanceForRain(9);

        $this->entityManager->persist($weather_record_3);
        
        // Test the command.
        $kernel = static::createKernel();

        $kernel->boot();

        $application = new Application($kernel);

        $application->add(new CalculateAverageTemperatureCommand($this->weatherRecordManager));

        $command = $application->find('app:calculate-average-temperature');

        $command_tester = new CommandTester($command);

        $command_tester->execute(array(
            'command'  => $command->getName(),
            'number_of_days' => 3,
        ));

        $this->assertContains("12", $command_tester->getDisplay());

        $command_tester->execute(array(
            'command'  => $command->getName(),
            'number_of_days' => 2,
        ));

        $this->assertContains("10", $command_tester->getDisplay());

        $command_tester->execute(array(
            'command'  => $command->getName(),
            'number_of_days' => 1,
        ));

        $this->assertContains("-2", $command_tester->getDisplay());
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $container = $kernel->getContainer();

        // Define entityManager.
        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        // Ensure no records are in database before testing.
        $this->entityManager
            ->createQuery('DELETE FROM \App\Entity\WeatherRecord')
            ->execute();

        // Define weatherRecordManager.
        $this->weatherRecordManager = new WeatherRecordManager($container
            ->get('doctrine')
            ->getManager());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();

        $this->entityManager = null;

        $this->weatherRecordManager = null;
    }

}
