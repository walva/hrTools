<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;

class DateCalculatorController extends AbstractController
{

    /**
     * @Route("/hours-to-decimal", name="hours_to_decimals")
     */
    public function hoursToDecimals(Request $request)
    {
        $form = $this->createFormBuilder([
            'date'    => new \DateTime(),
            'decimal' => 0,
            'hour'    => 0,
            'minutes' => 0,
        ])
            ->add('input', TimeType::class, [
                'with_seconds' => true,
                'widget' => 'single_text'
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        $result = null;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \DateTime $input */
            $input = $form->get('input')->getData();
            dump($input->format('H:i:s'));
            $result = $input->format('H');
            $result += $input->format('i') / 60;
            $result += $input->format('s') / (60*60);
            // 1.75 => 1h 45min
//            $hours    = floor($decimals);
//            $minutes  = ($decimals - $hours) * 60;
//            $secondes = abs(floor($minutes) - $minutes) * 60;
//            $minutes  = floor($minutes);
//            $result   = "$hours heures $minutes minutes et $secondes secondes";
        }

        return $this->render('date_calculator/decimals_convertor.html.twig', [
            'title'  => "Hour 👉 Decimals",
            'form'   => $form->createView(),
            'result' => $result,
        ]);
    }

    /**
     * @Route("/decimal-to-hours", name="decimal_to_hours")
     */
    public function decimalToHours(Request $request)
    {
        $form = $this->createFormBuilder([
            'date'    => new \DateTime(),
            'decimal' => 0,
            'hour'    => 0,
            'minutes' => 0,
        ])
            ->add('decimals', NumberType::class, [
                'scale' => 20,
                'help' => 'Les heures en décimal (1.5)',
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        $result = null;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \DateTime $date */
            $decimals = $form->get('decimals')->getData();
            // 1.75 => 1h 45min
            $hours    = floor($decimals);
            $minutes  = ($decimals - $hours) * 60;
            $secondes = abs(floor($minutes) - $minutes) * 60;
            $minutes  = floor($minutes);
            $result   = "$hours heures $minutes minutes et $secondes secondes";
        }

        return $this->render('date_calculator/decimals_convertor.html.twig', [
            'title'  => "Decimals 👉 Hours",
            'form'   => $form->createView(),
            'result' => $result,
        ]);
    }

    /**
     * @Route("/date/calulator", name="date_calculator")
     */
    public function date_calculator(Request $request)
    {
        $form = $this->createFormBuilder([
            'date'  => new \DateTime(),
            'day'   => 0,
            'week'  => 0,
            'month' => 0,
            'year'  => 0,
        ])
            ->add('date', DateType::class, [
                'html5'  => true,
                'widget' => 'single_text',
            ])
            ->add('day', NumberType::class, [
                'help' => 'Le nombre de jours à ajouter',
            ])
            ->add('week', NumberType::class, [
                'help' => 'Le nombre de semaines à ajouter',
            ])
            ->add('month', NumberType::class, [
                'help' => 'Le nombre de mois à ajouter',
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        $result = null;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \DateTime $date */
            $date    = $form->get('date')->getData();
            $day     = $form->get('day')->getData();
            $week    = $form->get('week')->getData();
            $month   = $form->get('month')->getData();
            $endDate = clone $date;
            $endDate->add(new \DateInterval("P{$day}D"));
            $endDate->add(new \DateInterval("P{$week}W"));
            $endDate->add(new \DateInterval("P{$month}M"));
            $result = [
                'computed' => $endDate,
            ];
        }

        return $this->render('form.html.twig', [
            'title'  => "Date calculator",
            'form'   => $form->createView(),
            'result' => $result,
        ]);
    }
}
