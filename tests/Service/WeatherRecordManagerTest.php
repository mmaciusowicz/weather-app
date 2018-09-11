<?php
namespace App\Tests\Service;

use App\Entity\WeatherRecord;
use App\Service\WeatherRecordManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CalculatorTest extends KernelTestCase {
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function testCheckIfRecordExistsForDateExpectTrue() {
        $date = \DateTime::createFromFormat('Y-m-d', '2018-03-05');

        $temperature = 15;

        $chance_for_rain = 40;

        $weather_record = new WeatherRecord();

        $weather_record->setDate($date);

        $weather_record->setTemperature($temperature);

        $weather_record->setChanceForRain($chance_for_rain);

        $this->entityManager->persist($weather_record);

        $this->entityManager->flush();

        $weather_record_manager = new WeatherRecordManager();

        $this->assertTrue($weather_record_manager->checkIfRecordExistsForDate($date));
    }

    public function testCheckIfRecordExistsForDateExpectFalse() {
        $date = \DateTime::createFromFormat ('Y-m-d', '2018-03-05');

        $weather_record_manager = new WeatherRecordManager();

        $this->assertFalse($weather_record_manager->checkIfRecordExistsForDate($date));
    }

    public function testCreate() {
        $date = \DateTime::createFromFormat('Y-m-d', '2018-03-05');

        $temperature = 15;

        $chance_for_rain = 40;

        $weather_record_manager = new WeatherRecordManager();

        $created_record = $weather_record_manager->create($date, $temperature, $chance_for_rain);

        $this->assertTrue(is_object($created_record));

        $this->assertEquals($created_record->date, $date);

        $this->assertEquals($created_record->temperature, $temperature);

        $this->assertEquals($created_record->temperature, $chance_for_rain);
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Ensure no records are in database before testing.
        $this->entityManager
            ->createQuery('DELETE FROM \App\Entity\WeatherRecord')
            ->execute();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();

        $this->entityManager = null;
    }
}
