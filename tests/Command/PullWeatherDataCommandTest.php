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

    public function testExecute() {
        $date_yesterday = (new \DateTime('now -1 day'))->format("Y-m-d");

        $mock_data_retriever = $this->createMock(DataRetriever::class);

        $mock_data_retriever->expects($this->any())
            ->method('retrieve')
            ->willReturn('{"date": "' . $date_yesterday . '", "temperature": 19, "chance_for_rain": 84}');

        $kernel = static::createKernel();

        $kernel->boot();

        $application = new Application($kernel);

        $application->add(new PullWeatherDataCommand($mock_data_retriever, $this->weatherRecordManager));

        $command = $application->find('app:pull-weather-data');

        $command_tester = new CommandTester($command);

        $command_tester->execute(array(
            'command'  => $command->getName(),
            'source_url' => 'http://localhost/mock-source',
        ));

        $this->assertContains('Created a record for date ' . $date_yesterday . '.', $command_tester->getDisplay());

        $command_tester->execute(array(
            'command'  => $command->getName(),
            'source_url' => 'http://localhost/mock-source',
        ));

        $this->assertContains('Record for date ' . $date_yesterday . ' already exists, check later.', $command_tester->getDisplay());
    }

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

        // Ensure no records are in database before testing.
        $container
            ->get('doctrine')
            ->getManager()
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

        $this->weatherRecordManager = null;
    }

}
