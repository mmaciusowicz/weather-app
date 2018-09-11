<?php
namespace App\Tests\Command;

use App\Service\DataRetriever;
use App\Service\WeatherRecordManager;
use App\Command\PullWeatherDataCommand;
use Symfony\Bundle\FrameworkBundle\Test\Error;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PullWeatherDataCommandTest extends KernelTestCase {

    /**
     * @var WeatherRecordManager
     */
    private $weatherRecordManager;

    public function testParseSourceData() {
        $parse_source_data = self::getReflectionMethod('App\Command\PullWeatherDataCommand', 'parseSourceData');

        $parsed_source_data = $parse_source_data->invokeArgs(
            new PullWeatherDataCommand(new DataRetriever(), $this->weatherRecordManager),
            ['{"date": "2016-09-16", "temperature": 19, "chance_for_rain": 84}']
        );

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

        $parse_source_data->invokeArgs(
            new PullWeatherDataCommand(new DataRetriever(), $this->weatherRecordManager),
            ['not a valid json string']
        );
    }

    public function testEnforceProperties() {
        $check_required_properties = self::getReflectionMethod('App\Command\PullWeatherDataCommand', 'enforceProperties');

        $data = new \stdClass();

        $data->date = '2018-02-03';

        $data->temperature = 3;

        $data->chance_for_rain = 24;

        $check_required_properties->invokeArgs(new PullWeatherDataCommand(new DataRetriever(), $this->weatherRecordManager), [$data]);

        // If no exception is thrown, assert true.
        $this->assertTrue(true);
    }

    /**
     * @expectedException Error
     */
    public function testFailingEnforcePropertiesMissingDate() {
        $check_required_properties = self::getReflectionMethod('App\Command\PullWeatherDataCommand', 'enforceProperties');

        $data = new \stdClass();

        $data->temperature = 3;

        $data->chance_of_rain = 24;

        $check_required_properties->invokeArgs(
            new PullWeatherDataCommand(new DataRetriever(), $this->weatherRecordManager),
            [$data]
        );
    }

    /**
     * @expectedException Error
     */
    public function testFailingEnforcePropertiesMissingTemperature() {
        $check_required_properties = self::getReflectionMethod('App\Command\PullWeatherDataCommand', 'enforceProperties');

        $data = new \stdClass();

        $data->date = '2018-02-02';

        $data->chance_of_rain = 24;

        $check_required_properties->invokeArgs(
            new PullWeatherDataCommand(new DataRetriever(), $this->weatherRecordManager),
            [$data]
        );
    }

    /**
     * @expectedException Error
     */
    public function testFailingEnforcePropertiesMissingChanceOfRain() {
        $check_required_properties = self::getReflectionMethod('App\Command\PullWeatherDataCommand', 'enforceProperties');

        $data = new \stdClass();

        $data->date = '2018-02-02';

        $data->temperature = 3;

        $check_required_properties->invokeArgs(
            new PullWeatherDataCommand(new DataRetriever(), $this->weatherRecordManager),
            [$data]
        );
    }

    /**
     * @expectedException Error
     */
    public function testFailingEnforcePropertiesUnexpectedProperty() {
        $check_required_properties = self::getReflectionMethod('App\Command\PullWeatherDataCommand', 'enforceProperties');

        $data = new object();

        $data->date = '2018-02-02';

        $data->temperature = 3;

        $data->chance_of_rain = 24;

        $data->unexpected = 'hello';

        $check_required_properties->invokeArgs(
            new PullWeatherDataCommand(new DataRetriever(), $this->weatherRecordManager),
            [$data]
        );
    }

    private static function getReflectionMethod($class_with_namespace, $method_name) {
        $reflection = new \ReflectionClass($class_with_namespace);

        $method = $reflection->getMethod($method_name);

        $method->setAccessible(true);

        return $method;
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $container = $kernel->getContainer();

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

        $this->weatherRecordManager = null;
    }

}
