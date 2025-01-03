<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\AbonneRepository;
use App\Entity\Blacklist;
use App\Repository\BlacklistRepository;
use App\Service\BlacklistService;
use App\Service\SmsService;
use App\Entity\Abonne;
use Doctrine\ORM\EntityManagerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/abonne/list', name: 'abonne_list')]
    public function AjouterAbonne(AbonneRepository $abonneRepository): Response
    {
        $abonnes = $abonneRepository->findAll();
        return $this->render('abonne/index.html.twig', [
            'abonnes' => $abonnes,
        ]);
    }


    #[Route('admin/abonne/{id}/supprime', name: 'adminSupprime_Abonne')]
    public function delete(int $id,AbonneRepository $abonneRepository,BlacklistRepository $blacklistRepository,EntityManagerInterface $em): Response 
    {
        $abonne = $abonneRepository->find($id);

        if (!$abonne) {
            $this->addFlash('error', 'Abonné introuvable.');
            return $this->redirectToRoute('abonne_list');
        }

        // Ajouter à la table Blacklist
        $blacklist = new Blacklist();
        $blacklist->setMSISDN($abonne->getMsisdn()); // Si MSISDN peut être considéré comme contact
        $blacklist->setDateAjout(new \DateTimeImmutable());
        $blacklist->setSpecialite($abonne->getSpecialite());
        $blacklist->setVille($abonne->getVille());

        $em->persist($blacklist);

        // Supprimer de la table Abonne
        $em->remove($abonne);
        $em->flush();

        $this->addFlash('success', 'Abonné déplacé vers la Blacklist avec succès.');

        return $this->redirectToRoute('abonne_list');
    }


    #[Route('/Admin/Abnne/nouveau', name: 'AdminAbonne_New')]
    public function newForAdmin(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $msisdn = $request->request->get('msisdn');
    
            // Vérifier si le numéro MSISDN existe déjà dans la base de données
            $existingAbonne = $em->getRepository(Abonne::class)->findOneBy(['msisdn' => $msisdn]);
    
            if ($existingAbonne) {
                $this->addFlash('error', 'Ce numéro de téléphone est déjà enregistré.');
                return $this->redirectToRoute('abonne_new');
            }
    
            // Si le MSISDN est unique, enregistrer l'abonné
            $abonne = new Abonne();
            $abonne->setMsisdn($msisdn);
            $abonne->setVille($request->request->get('Ville'));
            $abonne->setSpecialite($request->request->get('specialite'));
            //$abonne->setRoles(['ROLE_USER']);
            $abonne->setTentativeconnexion(0);
            $abonne->setPassword($passwordHasher->hashPassword($abonne, $request->request->get('password')));
            $abonne->setCreateAt(new \DateTimeImmutable());
    
            $em->persist($abonne);
            $em->flush();
    
            $this->addFlash('success', 'Abonné ajouté avec succès.');
            return $this->redirectToRoute('abonne_list');

        }
    
        return $this->render('abonne/new.html.twig');
    }

}
