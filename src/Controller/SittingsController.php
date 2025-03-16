<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\MettiersType;
use App\Form\VilleType;
use App\Repository\MettierRepository;
use App\Repository\SettingsRepository;
use App\Repository\VilleRepository;
use Proxies\__CG__\App\Entity\Mettier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\SettingsType;
use App\Entity\Settings;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RequestStack;

class SittingsController extends AbstractController
{
    #[Route('admin/parametre', name: 'parametres')]
    public function index(Request $request,SettingsRepository $settingsRepository,RequestStack $requestStack,SessionInterface $session, EntityManagerInterface $entityManager,#[Autowire('%uploads_directory%')] string $uploads_directory): Response
    {
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        //$identifiant = $Abonne['identifiant']?? 'null';
        $identifiant =	$_ENV['IDENTIFIANT_SITE'];

        // Récupération des paramètres existants
        if($Abonne['Roles'] =="ROLE_ADMIN" && $_ENV['IDENTIFIANT_SITE'] ==$Abonne['identifiant'])
        {
            $parametre = $settingsRepository->findOneBy(['identifiant' => $identifiant]);
            
            // Si les paramètres existent, on les utilise pour pré-remplir le formulaire
            $settings = $parametre ?? new Settings();
            
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

                    /*if (!$imageAccueilFile) {
                        $errorMessage = 'Ajoutez une image d\'accueil';
                        $this->addFlash('error', $errorMessage);
                        return $this->redirectToRoute('parametres'); // Redirect to a specific route after displaying the error
                    }*/

                    // Sauvegarde des paramètres
                    $entityManager->persist($settings);
                    $entityManager->flush();

                    $this->addFlash('success', 'Les paramètres ont été mis à jour avec succès.');

                    return $this->redirectToRoute('parametres',[
                        //'parametre'=>$parametre,
                    ]);
                } else {
                    // Récupérer et formater les erreurs du formulaire

                    $errorMessage = 'Une erreur est survenue';

                    $this->addFlash('error', $errorMessage);
                }
            }

            //dd($parametre);
            return $this->render('admin/parametres.html.twig', [
                'form' => $form->createView(),
                //'parametre'=>$parametre,
            ]);

        }
        //dd($parametre);
        return $this->render('offres/error404.html.twig');

    }

    #[Route('admin/Ville-parametre', name: 'parametres-ville')]
    public function villeForm(Request $request,VilleRepository $villeRepository,RequestStack $requestStack,SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        // Récupération des paramètres existants
        if($Abonne['Roles'] =="ROLE_ADMIN" && $_ENV['IDENTIFIANT_SITE'] ==$Abonne['identifiant'])
        {

            $settings = new Ville();

            $form = $this->createForm(VilleType::class, $settings);
            $form->handleRequest($request);
            $settings->setCreatedAt(new \DateTimeImmutable);
            
            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($settings);
                $entityManager->flush();
            
                $this->addFlash('success', 'Les paramètres ont été mis à jour avec succès.');
                return $this->redirectToRoute('parametres-vue-ville');
            }
        
            return $this->render('sittings/villeFrom.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        return $this->render('offres/error404.html.twig');

    }

    #[Route('admin/Ville-vue', name: 'parametres-vue-ville')]
    public function villeVue(Request $request,VilleRepository $villeRepository,RequestStack $requestStack,SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        // Récupération des paramètres existants
        if($Abonne['Roles'] =="ROLE_ADMIN" && $_ENV['IDENTIFIANT_SITE'] ==$Abonne['identifiant'])
        {        
            $ville = $villeRepository->findAll();

            return $this->render('sittings/villeVue.html.twig', [
                'villes' => $ville,
            ]);
        }
        return $this->render('offres/error404.html.twig');       
    }


    #[Route('admin/mettier-parametre', name: 'parametres-mettier')]
    public function mettierForm(Request $request,RequestStack $requestStack,SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        // Récupération des paramètres existants
        if($Abonne['Roles'] =="ROLE_ADMIN" && $_ENV['IDENTIFIANT_SITE'] ==$Abonne['identifiant'])
        {        
          
            $settings = new Mettier();

            $form = $this->createForm(MettiersType::class, $settings);
            $form->handleRequest($request);
            $settings->setCreatedAt(new \DateTimeImmutable);
            
            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($settings);
                $entityManager->flush();
            
                $this->addFlash('success', 'Les paramètres ont été mis à jour avec succès.');
                return $this->redirectToRoute('parametres-vue-mettier');
            }
        
            return $this->render('sittings/mettiersFrom.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        return $this->render('offres/error404.html.twig');          
    }

    #[Route('admin/mettier-vue', name: 'parametres-vue-mettier')]
    public function mettierVue(RequestStack $requestStack,SessionInterface $session,MettierRepository $mettierRepository): Response
    {
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        // Récupération des paramètres existants
        if($Abonne['Roles'] =="ROLE_ADMIN" && $_ENV['IDENTIFIANT_SITE'] ==$Abonne['identifiant'])
        { 
            $mettiers = $mettierRepository->findAll();

            return $this->render('sittings/mettiersVue.html copy.twig', [
                'mettiers' => $mettiers,
            ]);
        }
        return $this->render('offres/error404.html.twig');  
    }
}
