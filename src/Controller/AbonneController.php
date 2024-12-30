<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Abonne;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\AbonneRepository;
use App\Entity\Blacklist;
use App\Repository\BlacklistRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class AbonneController extends AbstractController
{
    #[Route('/abonne/list', name: 'abonne_list')]
    public function index(AbonneRepository $abonneRepository): Response
    {
        $abonnes = $abonneRepository->findAll();
        return $this->render('abonne/index.html.twig', [
            'abonnes' => $abonnes,
        ]);
    }

    #[Route('/abonne/new', name: 'abonne_new')]
    public function new(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
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
    
            if($abonne->getRoles()==['ROLE_ADMIN'])
            {
                $this->addFlash('success', 'Abonné ajouté avec succès.');
                return $this->redirectToRoute('abonne_list');
            }else
            {
                $url = $this->generateUrl('connexion.abonne'); // Génère l'URL pour la route 'connexion.abonne'

                $this->addFlash('success', '<a href="' . $url . '">Cliquez ici pour vous identifier !</a>');
                return $this->redirectToRoute('app_home');
            }
        }

        $this->addFlash('warning', 'Identifiez-vous !');
        return $this->redirectToRoute('connexion.abonne');
    }
    

    #[Route('admin/abonne/{id}/delete', name: 'adminAbonne_delete')]
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


    #[Route('/Admin/Abnne/new', name: 'AdminAbonne_New')]
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


    #[Route('abonne/{id}/delete', name: 'Abonne_delete')]
    public function deleteuser(int $id,AbonneRepository $abonneRepository,BlacklistRepository $blacklistRepository,EntityManagerInterface $em): Response 
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

        $this->addFlash('success', 'Compte supprimé.');

        return $this->redirectToRoute('abonne_list');
    }
}
