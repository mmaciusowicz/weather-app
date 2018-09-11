<?php
namespace App\Tests\Service;

use App\Entity\WeatherRecord;
use App\Service\WeatherRecordManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WeatherRecordManagerTest extends KernelTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var WeatherRecordManager
     */
    private $weatherRecordManager;

    public function testCheckIfRecordExistsForDateExpectTrue() {
        $date = '2018-03-05';

        $date_time = \DateTime::createFromFormat('Y-m-d', $date);

        $temperature = 15;

        $chance_for_rain = 40;

        $weather_record = new WeatherRecord();

        $weather_record->setDate($date_time);

        $weather_record->setTemperature($temperature);

        $weather_record->setChanceForRain($chance_for_rain);

        $this->entityManager->persist($weather_record);

        $this->entityManager->flush();

        $this->assertTrue($this->weatherRecordManager->checkIfRecordExistsForDate($date));
    }

    public function testCheckIfRecordExistsForDateExpectFalse() {
        $this->assertFalse($this->weatherRecordManager->checkIfRecordExistsForDate('2018-03-05'));
    }

    public function testCreate() {
        $date = '2018-03-05';

        $date_time = \DateTime::createFromFormat ('Y-m-d', $date);

        $temperature = 15;

        $chance_for_rain = 40;

        $created_record = $this->weatherRecordManager->create($date, $temperature, $chance_for_rain);

        $this->assertTrue(is_object($created_record));

        $this->assertEquals($created_record->getDate(), $date_time);

        $this->assertEquals($created_record->getTemperature(), $temperature);

        $this->assertEquals($created_record->getChanceForRain(), $chance_for_rain);
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
        $this->weatherRecordManager = new WeatherRecordManager($this->entityManager);
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
