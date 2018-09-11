<?php
namespace App\Service;

use App\Entity\WeatherRecord;
use Doctrine\ORM\EntityManagerInterface;

class WeatherRecordManager {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * Calculate an average temperature for specified number of days.
     *
     * @param int $number_of_days Number of days to calculate the average for.
     */
    public function calculateAverageTemperature($number_of_days) {
        $connection = $this->entityManager->getConnection();

        $query = 'SELECT AVG(temperatures)
            FROM (
                SELECT temperature
                AS temperatures 
                FROM weather_record
                ORDER BY date DESC
                LIMIT 0, :limit
            ) AS avg';

        $statement = $connection->prepare($query);

        $statement->bindValue('limit', $number_of_days, \PDO::PARAM_INT);
    
        $statement->execute();

        $result = $statement->fetch();

        return $result;
    }

    /**
     * Create a new weather record in the database.
     *
     * @param string $date Date of the record in format Y-m-d.
     *
     * @return boolean True if record exists, false if it doesn't.
     */
    public function checkIfRecordExistsForDate(string $date) {
        $result = $this->entityManager->createQueryBuilder('weather_record')
            ->select('count(weather_record.id)')
            ->from('\App\Entity\WeatherRecord', 'weather_record')
            ->where('weather_record.date = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getSingleScalarResult();

        if ($result === "1") {
            return true;
        }

        return false;
    }

    /**
     * Create a new weather record in the database.
     *
     * @param string $date Date of the record in format Y-m-d.
     * @param int $temperature Temperature.
     * @param int $chance_for_rain Chance of rain (0-100).
     *
     * @return WeatherRecord Created weather record.
     */
    public function create(string $date, $temperature, $chance_for_rain) {
        $date_time = \DateTime::createFromFormat('Y-m-d', $date);

        $weather_record = new WeatherRecord();

        $weather_record->setDate($date_time);

        $weather_record->setTemperature($temperature);

        $weather_record->setChanceForRain($chance_for_rain);

        $this->entityManager->persist($weather_record);

        $this->entityManager->flush();

        return $weather_record;
    }

    /**
     * List weather records.
     *
     * @param int $offset Number of records to skip.
     * @param int $limit Number of records to return.
     *
     * @return array List of weather records.
     */
    public function list($offset, $limit) {
        $records = $this->entityManager->createQueryBuilder('weather_record')
            ->select('weather_record.id', 'weather_record.date', 'weather_record.temperature', 'weather_record.chance_for_rain')
            ->from('\App\Entity\WeatherRecord', 'weather_record')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('weather_record.date', 'DESC')
            ->getQuery()
            ->execute();

        return $records;
    }

}
