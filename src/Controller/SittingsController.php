<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\SettingsType;
use App\Entity\Settings;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormError;

class SittingsController extends AbstractController
{
    #[Route('admin/parametre', name: 'parametres')]
    public function index(Request $request, EntityManagerInterface $entityManager,#[Autowire('%uploads_directory%')] string $uploads_directory): Response
    {
        $settings = new Settings();
        $form = $this->createForm(SettingsType::class, $settings);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Traitement des fichiers téléchargés (images)
                $logoEnteteFile = $form->get('logoEntete')->getData();
                if ($logoEnteteFile) {
                    $logoEnteteFileName = uniqid().'.'.$logoEnteteFile->guessExtension();
                    $logoEnteteFile->move($uploads_directory, $logoEnteteFileName);
                    $settings->setLogoEntete($logoEnteteFileName);
                }

                $logoNavBarFile = $form->get('logoNavBar')->getData();
                if ($logoNavBarFile) {
                    $logoNavBarFileName = uniqid().'.'.$logoNavBarFile->guessExtension();
                    $logoNavBarFile->move($uploads_directory, $logoNavBarFileName);
                    $settings->setLogoNavBar($logoNavBarFileName);
                }

                $imageAccueilFile = $form->get('imageAccueil')->getData();
                if ($imageAccueilFile) {
                    $imageAccueilFileName = uniqid().'.'.$imageAccueilFile->guessExtension();
                    $imageAccueilFile->move($uploads_directory, $imageAccueilFileName);
                    $settings->setImageAccueil($imageAccueilFileName);
                }

                if (!$imageAccueilFile) {
                    $errorMessage = 'Ajoutez une image d\'accueil';
                    $this->addFlash('error', $errorMessage);
                    return $this->redirectToRoute('parametres'); // Redirect to a specific route after displaying the error
                }

                // Sauvegarde des paramètres
                $entityManager->persist($settings);
                $entityManager->flush();

                $this->addFlash('success', 'Les paramètres ont été mis à jour avec succès.');

                return $this->redirectToRoute('parametres');
            } else {
                // Récupérer et formater les erreurs du formulaire

                $errorMessage = 'Une erreur est survenue';

                $this->addFlash('error', $errorMessage);
            }
        }

        return $this->render('admin/parametres.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
