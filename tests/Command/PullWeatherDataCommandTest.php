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
        $parse_source_data = self::getReflectionMethod('App\Command\PullWeatherDataCommand', 'parseSourceData');

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
        $parse_source_data = self::getReflectionMethod('App\Command\PullWeatherDataCommand', 'parseSourceData');

        $parse_source_data->invokeArgs(new PullWeatherDataCommand(), ['not a valid json string']);
    }

    /**
     * @expectedException Error
     */
    public function testFailingCheckRequiredProperties() {
        $check_required_properties = self::getReflectionMethod('App\Command\PullWeatherDataCommand', 'checkRequiredProperties');

        $data = new \stdClass();

        $check_required_properties->invokeArgs(new PullWeatherDataCommand(), [$data]);
    }

    private static function getReflectionMethod($class_with_namespace, $method_name) {
        $reflection = new \ReflectionClass($class_with_namespace);

        $method = $reflection->getMethod($method_name);

        $method->setAccessible(true);

        return $method;
    }
}
