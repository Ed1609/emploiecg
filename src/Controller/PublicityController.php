<?php

namespace App\Controller;

use App\Form\PublicityType;
use App\Entity\Publicity;
use App\Repository\PublicityRepository;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PublicityController extends AbstractController
{
    #[Route('admin/publicity', name: 'app_publicity')]
    public function index(PublicityRepository $publicityRepository, Request $request): Response
    {

        $pubParPage = $request->query->getInt('offresParPage', 10);
        $pageActuelle = max($request->query->getInt('page', 1), 1);

        $total = $publicityRepository->countAllPublicity();
        $nombreDePages = ceil($total / $pubParPage);

        $pageActuelle = min($pageActuelle, $nombreDePages);
        $premiereEntree = ($pageActuelle - 1) * $pubParPage;

        $pubs = $publicityRepository->afficherPub($pubParPage, $premiereEntree);
        //dd($pubs);

        return $this->render('publicity/voirpub.html.twig', [
            'publicity' => $pubs,
            'nombreDePages' => $nombreDePages,
            'premiereEntree' => $premiereEntree,                
            'pageActuelle' => $pageActuelle,
            'pubParPage' => $pubParPage,
            'total' => $total,
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
            $pubFileName = uniqid().'.'.$imagepub->guessExtension();
            $imagepub ->move($uploads_directory, $pubFileName);
            $publicity ->setImagePub($pubFileName);
            $entityManager->persist($publicity);
            $entityManager->flush();

            return $this->redirectToRoute('publicity.new'); // Redirigez vers la page de liste
        }

        return $this->render('publicity/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
