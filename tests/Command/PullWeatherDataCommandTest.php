<?php
namespace App\Tests\Command;

use App\Command\PullWeatherDataCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\Error;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class PullWeatherDataCommandTest extends KernelTestCase {
    /**
     * @expectedException Error
     */
    public function testFailingRetrieveDataFromSourceInvalidUrlSyntax() {
        $kernel = static::createKernel();

        $kernel->boot();

        $application = new Application($kernel);

        $command = $application->find('app:pull-weather-data');

        $commandTester = new CommandTester($command);

        $commandTester->execute(array(
            'command'  => $command->getName(),
            'source_url' => 'example.com',
        ));
    }

    /**
     * @expectedException Error
     */
    public function testFailingRetrieveDataFromSourceNonexistentUrl() {
        $kernel = static::createKernel();

        $kernel->boot();

        $application = new Application($kernel);

        $command = $application->find('app:pull-weather-data');

        $commandTester = new CommandTester($command);

        $commandTester->execute(array(
            'command'  => $command->getName(),
            'source_url' => 'http://localhost/this/does/not/exist/really-it-does-not',
        ));
    }

    public function testParseSourceData() {
        $reflection = new \ReflectionClass('App\Command\PullWeatherDataCommand');

        $parse_source_data = $reflection->getMethod('parseSourceData');

        $parse_source_data->setAccessible(true);

        $parsed_source_data = $parse_source_data->invokeArgs(new PullWeatherDataCommand(), ['{"date": "2016-09-16", "temperature": 19, "chance_for_rain": 84}']);

        $expected = new \stdClass();

        $expected->date = '2016-09-16';

        $expected->temperature = 19;

        $expected->chance_for_rain = 84;

        $this->assertEquals($parsed_source_data, $expected);
    }

    /**
     * @expectedException Error
     */
    public function testFailingParseSourceData() {
        $reflection = new \ReflectionClass('App\Command\PullWeatherDataCommand');

        $parse_source_data = $reflection->getMethod('parseSourceData');

        $parse_source_data->setAccessible(true);

        $parse_source_data->invokeArgs(new PullWeatherDataCommand(), ['not a valid json string']);
    }
}
