<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Abonne;
use App\Service\ServiceSecondaryDataBase;
use Symfony\Component\HttpFoundation\RequestStack;
use Psr\Log\LoggerInterface;

class ConnexionController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('connexion/index.html.twig', [
            'controller_name' => 'ConnexionController',
        ]);
    }

    #[Route('/connexion', 'app_connexion')]
    public function login_first(Request $request,LoggerInterface $logger,AuthenticationUtils $authenticationUtils,EntityManagerInterface $manager,SessionInterface $session,UserPasswordHasherInterface $passwordHasher,ServiceSecondaryDataBase $serviceSecondaryDataBase,SettingsRepository $settingsRepository): Response
    {
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($request->isMethod('POST')) {
            $msisdn = $request->request->get('msisdn');
            $password = $request->request->get('password');
            $origin = $request->request->get('origin');

            // Recherche de l'utilisateur
            $user = $manager->getRepository(Abonne::class)->findOneBy(['msisdn' => $msisdn]);

            if (!$user) {
                $this->addFlash('error', 'Vous n\'êtes pas abonné, abonnez-vous !');
                $logger->error("Erreur d'authentification : Utilisateur inexistant.");
                return $this->redirectToRoute('app_home', ['last_username' => $msisdn]);
            }

            // 🔹 Vérification du rôle (Admin) avant toute autre action
            $roles = $user->getRoles();
            $adminRole = "ROLE_ADMIN";

            if (in_array($adminRole, $roles, true) && $origin === "main") {
                $this->addFlash('success', 'Accès administrateur requis.');
                $logger->info("Information d'authentification : Accès admin requis.");
                return $this->render('connexion/index.html.twig', ['admin' => true]);
            }

            // 🔹 Vérification du verrouillage du compte
            if ($user->isLocked()) {
                $lockedUntil = $user->getLockedUntil();
                if ($lockedUntil && $lockedUntil > new \DateTime()) {
                    $minutesRemaining = ceil(($lockedUntil->getTimestamp() - time()) / 60);
                    $this->addFlash('error', "Votre compte est verrouillé. Réessayez dans {$minutesRemaining} minutes.");
                    $logger->error("Erreur d'authentification : Compte verrouillé.");
                    return $this->render('connexion/index.html.twig', ['last_username' => $msisdn,'admin'=>in_array($adminRole, $roles, true),]);
                } else {
                    // Déverrouillage automatique après expiration
                    $user->setIsLocked(false);
                    $user->setLockedUntil(null);
                    $user->setTentativeconnexion(0);
                    $manager->flush();
                }
            }

            // 🔹 Vérification du mot de passe
            if ($passwordHasher->isPasswordValid($user, $password)) {
                // Réinitialisation des tentatives et connexion
                $user->setTentativeconnexion(0);
                $user->setIsLocked(false);
                $user->setLockedUntil(null);
                $manager->flush();

                $servicelient = $settingsRepository->findByIdentifiant($_ENV['IDENTIFIANT_SITE']);
                $identifiant = $servicelient ? $servicelient->getIdentifiant() : null;

                $sessionData = [
                    'idAbonne' => $user->getId(),
                    'msisdn' => $user->getmsisdn(),
                    'Roles' => $roles[0],
                    'identifiant' => $identifiant,
                ];
                $session->set('Abonne', $sessionData);

                // Définition de la redirection
                if (in_array($adminRole, $roles, true)) {
                    $redirectTo = $this->redirectToRoute('app_admin');
                    $cookieDuration = 3600;
                } else {
                    $redirectTo = $this->redirectToRoute('app_home');
                    $cookieDuration = 604800;
                }

                // Création du cookie
                $cookie = new Cookie(
                    'Abonne',
                    json_encode(['idAbonne' => $user->getId(), 'Roles' => $roles]),
                    time() + $cookieDuration,
                    '/',
                    null,
                    true,
                    true,
                    false,
                    Cookie::SAMESITE_STRICT
                );

                $redirectTo->headers->setCookie($cookie);
                return $redirectTo;
            } else {
                // 🔹 Gestion des tentatives en cas de mot de passe incorrect
                $user->setTentativeconnexion($user->getTentativeconnexion() + 1);

                if ($user->getTentativeconnexion() >= 3) {
                    $user->setIsLocked(true);
                    $user->setLockedUntil((new \DateTime())->modify('+15 minutes'));
                    $this->addFlash('error', 'Compte verrouillé après 3 tentatives échouées. Réessayez dans 15 minutes.');
                    $logger->error("Erreur d'authentification : Compte verrouillé après 3 échecs.");
                } else {
                    $this->addFlash('error', 'Mot de passe incorrect.');
                    $logger->error("Erreur d'authentification : Mot de passe incorrect.");
                }

                $manager->flush();

                return $this->render('connexion/index.html.twig', [
                    'last_username' => $msisdn,
                    'admin' => in_array($adminRole, $roles, true),
                ]);
            }
        }

        return $this->render('connexion/Cusindex.html.twig', ['last_username' => $lastUsername]);
    }



    #[Route('abonne/connexion','connexion.abonne')]
    public function connexion(SettingsRepository $settingsRepository,RequestStack $requestStack,AuthenticationUtils $authenticationUtils)
    {
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        if(!$Abonne)
        {
            return $this->render('connexion/Cusindex.html.twig',[
                'monSite'=> $settingsRepository->findByIdentifiant($_ENV['IDENTIFIANT_SITE']),

            ]);         
        }
        return $this->render('offres/error404.html.twig');
    }


    #[Route( '/logout', 'app_logout')]
    public function logout(): void
    {
        
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

