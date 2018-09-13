<?php
namespace App\Controller;

use App\Entity\WeatherRecord;
use App\Service\WeatherRecordManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WeatherRecordsListController extends AbstractController {

    /**
     * @var WeatherRecordManager
     */
    private $weatherRecordManager;

    public function __construct(WeatherRecordManager $weatherRecordManager) {
        $this->weatherRecordManager = $weatherRecordManager;
    }

    /**
     * @Route("/"))
     */
    public function index() {
        $entity_manager = $this->getDoctrine()->getManager();

        // TODO: Make max results configurable in yaml.
        $max_results = 7;

        $records = $this->weatherRecordManager->list(0, $max_results);

        $items = [];

        for ($i = 0; $i < count($records); $i++) {
            $weather_record = new WeatherRecord();

            $items[] = [
                'date' => $records[$i]['date']->format('Y-m-d'),
                'temperature' => $records[$i]['temperature'],
                'chance_for_rain' => $records[$i]['chance_for_rain'],
            ];
        }

        return $this->render('weather-record/list.html.twig', [
            'items' => $items,
        ]);
    }

}
