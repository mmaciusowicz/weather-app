<?php
namespace App\Service;

use App\Entity\WeatherRecord;

class WeatherRecordManager {
    /**
     * Create a new weather record in the database.
     *
     * @param DateTime $date Date of the record.
     *
     * @return boolean True if record exists, false if it doesn't.
     */
    public function checkIfRecordExistsForDate(\DateTime $date) {

    }

    /**
     * Create a new weather record in the database.
     *
     * @param DateTime $date Date of the record.
     * @param int $temperature Temperature.
     * @param int $chance_for_rain Chance of rain (0-100).
     *
     * @return WeatherRecord Created weather record.
     */
    public function create(\DateTime $date, $temperature, $chance_for_rain) {

    }
}
