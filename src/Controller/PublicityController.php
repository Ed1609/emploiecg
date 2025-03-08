<?php

namespace App\Controller;

use App\Form\PublicityType;
use App\Entity\Publicity;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PublicityController extends AbstractController
{
    #[Route('/publicity', name: 'app_publicity')]
    public function index(): Response
    {
        return $this->render('publicity/index.html.twig', [
            'controller_name' => 'PublicityController',
        ]);
    }

    #[Route('admin/puplicity/new',name: 'publicity.new')]
    public function new(Request $request,EntityManagerInterface $entityManager,#[Autowire('%uploads_directory%')] string $uploads_directory): Response
    {
        $publicity = new Publicity();
        $form = $this->createForm(PublicityType::class, $publicity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagepub = $form->get('imagePub')->getData();
            $logoEnteteFileName = uniqid().'.'.$imagepub->guessExtension();
            $imagepub ->move($uploads_directory, $logoEnteteFileName);
            $entityManager->persist($publicity);
            $entityManager->flush();

            return $this->redirectToRoute('publicity_index'); // Redirigez vers la page de liste
        }

        return $this->render('publicity/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
