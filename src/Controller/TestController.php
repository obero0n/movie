<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Movie;
use App\Entity\Evaluation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class TestController extends AbstractController
{


    /**
     * fonction fète pr tester ds trucs
     * @Route("/test", name="test")
     */
    public function test()
    {
        $ms = $this->getDoctrine()->getRepository(Movie::class)->findAll();
        //fonction qui essé de calc moyen note flm mais prblm
        for ($i=0; $i < count($ms) ; $i) {
          $notes = $ms[$i]->getEvaluations()->getGrade();
        }
        return $this->render('test/index.html.twig', [
          "ms" => $ms
        ]);
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $ms = $this->getDoctrine()->getRepository(Movie::class)->findAll();
        return $this->render('test/index.html.twig', [
          "ms" => $ms
        ]);
    }

    /**
     * @Route("/single/{id}", name="single")
     */
    public function show(Movie $movie)
    {

      $evals = $this->getDoctrine()->getRepository(Evaluation::class)->getBestEval($movie);

dump($evals);

        return $this->render('test/single.html.twig', [
          "movie" => $movie,
          "Besteval" => $evals
        ]);
    }

    /**
     * @Route("/evaluation/{id}", name="evaluation")
     * @IsGranted("ROLE_USER")
     */
    public function rate(Movie $movie, Request $request)
    {
        $eval = new Evaluation();

        $form = $this->createFormBuilder($eval)
            ->add('comment' , TextType::class)
            ->add('grade' , IntegerType::class)
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $eval->setMovie($movie);
          $eval->setUser($this->getUser());
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->persist($eval);
          $entityManager->flush();
        }

        return $this->render('test/evaluation.html.twig', [
          "b" => $movie,
          "form" => $form->createView()
        ]);
    }
}
