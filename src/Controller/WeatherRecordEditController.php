<?php
namespace App\Controller;

use App\Entity\WeatherRecord;
use App\Service\WeatherRecordManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WeatherRecordEditController extends AbstractController {

    /**
     * @var WeatherRecordManager
     */
    private $weatherRecordManager;

    public function __construct(WeatherRecordManager $weatherRecordManager) {
        $this->weatherRecordManager = $weatherRecordManager;
    }

    /**
     * @Route("/weather-records/edit", methods={"GET", "HEAD"})
     */
    public function index() {
        $weather_record = new WeatherRecord();

        $choices = [];

        $records = $this->weatherRecordManager->list(0);

        foreach ($records as $key => $record) {
            $choices[$record['date']->format('Y-m-d')] = $record['id'];
        }

        $form = $this->createFormBuilder($weather_record)
        ->add('id', ChoiceType::class, [
            'choices' => $choices,
        ])
        ->add('temperature', NumberType::class, [
            'attr' => [
                'pattern' => '(-)*[0-9]+',
            '   title' => 'Numeric temperature value with no decimal places',
            ],
        ])
        ->add('save', SubmitType::class, array('label' => 'Update record'))
        ->getForm();

        return $this->render('weather-record/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/weather-records/edit", methods={"POST"})
     */
    public function edit() {
        $request = Request::createFromGlobals();

        $form_data = $request->request->get('form');

        $entity_manager = $this->getDoctrine()->getManager();

        $weather_record = $entity_manager->getRepository(WeatherRecord::class)->find($form_data['id']);

        $weather_record->setTemperature($form_data['temperature']);

        $entity_manager->persist($weather_record);

        $entity_manager->flush();

        return new RedirectResponse("/weather-records/edit");
    }
}