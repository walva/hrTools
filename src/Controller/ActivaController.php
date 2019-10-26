<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ActivaController extends AbstractController
{
    /**
     * @Route("/activa", name="activa")
     */
    public function index(Request $request)
    {
        $form = $this->createFormBuilder([
            'date'  => new \DateTime(),
            'month' => 36,
        ])
            ->add('date', DateType::class, [
                'html5'  => true,
                'widget' => 'single_text',
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
            $date = $form->get('date')->getData();
            $month = $form->get('month')->getData();
            $endDate = clone $date;
            $endDate->add(new \DateInterval("P{$month}M"));
            $result = [
                'computed' => $endDate
            ];
        }

        return $this->render('activa/index.html.twig', [
            'controller_name' => 'ActivaController',
            'form'            => $form->createView(),
            'result'          => $result,
        ]);
    }
}
