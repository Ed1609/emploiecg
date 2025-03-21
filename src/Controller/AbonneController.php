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
use App\Controller\ConnexionController;
use App\Service\BlacklistService;
use App\Service\SmsService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\SecurityBundle\Security;


class AbonneController extends AbstractController
{

    #[Route('/abonne/new', name: 'abonne_new')]
    public function new(RequestStack $requestStack,Request $request,BlacklistService $blacklistService,SmsService $smsService ,UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $session = $requestStack->getSession();
        $admin = false;
        if($session)
        {
            $Abonne = $session->get('Abonne');
            if($Abonne)
            {
                if($Abonne['Roles']=='ROLE_ADMIN')
                {
                    $admin = true;
                }
            }
        }

        $cout = $_ENV['PRIX_DU_SERVICE'];

        if ($request->isMethod('POST')) {
            $msisdn = $request->request->get('msisdn');
            // Vérifier si le numéro MSISDN existe déjà dans la base de données
            $existingAbonne = $em->getRepository(Abonne::class)->findOneBy(['msisdn' => $msisdn]);
    
            if ($existingAbonne) {
                $this->addFlash('error', 'Ce numéro de téléphone est déjà enregistré.');
                return $this->redirectToRoute('app_home');
            }

            //dd($request->request->all());
            // Si le MSISDN est unique, enregistrer l'abonné
            $abonne = new Abonne();
            $abonne->setMsisdn($msisdn);
            $abonne->setVille($request->request->get('Ville'));
            $abonne->setSpecialite($request->request->get('specialite'));
            //$abonne->setRoles(['ROLE_USER']);
            $abonne->setTentativeconnexion(0);
            $abonne->setPassword($passwordHasher->hashPassword($abonne, $request->request->get('password')));
            $abonne->setCreateAt(new \DateTimeImmutable());
            $abonne->setModePaiement($request->request->get('mode_debit'));
            $modePaiement = $request->request->get('mode_debit');

            $blacklistService->deleteByMsisdn($msisdn);
            // Gestion du mot de passe administrateur
            $passAdmin = $request->request->get('passAdmin') ?? null;

            if ($passAdmin) {
                $abonne->setPassAdmin($passwordHasher->hashPassword($abonne, $passAdmin));
            }
        
            // Définition du rôle
            $role = $request->request->get('role') ?? 'ROLE_USER'; // Valeur par défaut
            $abonne->setRoles([$role]);

            $em->persist($abonne);
            $em->flush();
    
            // 📲 Envoi de SMS de bienvenue
            if($modePaiement = 'AM')
            {
                $message = "Bienvenue sur notre plateforme d'alerte emploi, le coût de souscription est de {$cout} Frs par {$modePaiement}.";

            }else{
                $message="Bienvenue sur notre plateforme d'alerte emploi, le coût de souscription est de {$cout} Frs par {$modePaiement}.";
            }
            
            $smsService->sendSms($msisdn, '', $message);
            
            //$success= $this->$sms->sendSms($msisdn,'',$message); // Correct method call
            $smsService->sendSms($msisdn,'',$message);

            if($admin)
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

        $this->addFlash('warning', 'Inscrivez-vous !');
        return $this->redirectToRoute('app_home');
    }
    

    #[Route('abonne/desabonnement', name: 'Abonne_unsubscribe', methods: ['POST'])]
    public function unsubscribe(RequestStack $requestStack, Request $request, SessionInterface $session, AbonneRepository $abonneRepository, BlacklistRepository $blacklistRepository, EntityManagerInterface $em): Response {
        // Récupérer l'utilisateur connecté
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        $idAbonne = $Abonne['idAbonne'];
        $user = $abonneRepository->find($idAbonne);

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour vous désabonner.');
            return $this->redirectToRoute('app_login');
        }
    
        // Vérification du token CSRF
        if (!$this->isCsrfTokenValid('unsubscribe', $request->request->get('_token'))) {
            $this->addFlash('error', 'Bad request.');
            return $this->redirectToRoute('app_home');
        }
    
        // Ajouter l'utilisateur à la table Blacklist
        $blacklist = new Blacklist();
        $blacklist->setMSISDN($user->getMsisdn());
        $blacklist->setDateAjout(new \DateTimeImmutable());
        $blacklist->setSpecialite($user->getSpecialite());
        $blacklist->setVille($user->getVille());
    
        $em->persist($blacklist);
    
        // Supprimer l'utilisateur de la table Abonne
        $em->remove($user);
        $em->flush();
    
        // Invalider la session après la suppression
        $session->invalidate();
    
        $this->addFlash('success', 'Votre compte a été supprimé avec succès.');
        return $this->redirectToRoute('app_home');
    }
    
}
