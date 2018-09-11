<?php
namespace App\Controller;

use App\Entity\WeatherRecord;
use App\Service\WeatherRecordManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
     * @Route("/", methods={"GET","HEAD"}))
     */
    public function index() {
        $entity_manager = $this->getDoctrine()->getManager();

        // TODO: Make max results configurable in yaml.
        $max_results = 7;

        $records = $this->weatherRecordManager->list(0, $max_results);

        $items = [];

        for ($i = 0; $i < count($records); $i++) {
            $weather_record = new WeatherRecord();

            $form = $this->createFormBuilder($weather_record)
                ->add('id', HiddenType::class, [
                    'data' => $records[$i]['id'],
                ])
                ->add('temperature', NumberType::class, [
                    'attr' => [
                        'pattern' => '(-)*[0-9]+',
                        'title' => 'Numeric temperature value with no decimal places',
                    ],
                    'data' => $records[$i]['temperature'],
                ])
                ->add('chance_for_rain', NumberType::class, [
                    'attr' => [
                        'pattern' => '[0-9]+',
                        'title' => 'Chance of rain (0-100)',
                    ],
                    'data' => $records[$i]['chance_for_rain'],
                    'disabled' => true,
                ])
                ->add('save', SubmitType::class, array('label' => 'Update record'))
                ->getForm();

            $items[] = [
                'date' => $records[$i]['date']->format('Y-m-d'),
                'form' => $form->createView(),
            ];
        }

        return $this->render('weather-record/list.html.twig', [
            'items' => $items,
        ]);
    }

    /**
     * @Route("/", methods={"POST"}))
     */
    public function updateRecord() {
        $request = Request::createFromGlobals();

        $form_data = $request->request->get('form');

        $entity_manager = $this->getDoctrine()->getManager();

        $weather_record = $entity_manager->getRepository(WeatherRecord::class)->find($form_data['id']);

        $weather_record->setTemperature($form_data['temperature']);

        $entity_manager->persist($weather_record);

        $entity_manager->flush();

        return new RedirectResponse("/");
    }

}
