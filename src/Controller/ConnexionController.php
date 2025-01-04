<?php

namespace App\Controller;

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

class ConnexionController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('connexion/index.html.twig', [
            'controller_name' => 'ConnexionController',
        ]);
    }

    #[Route('/connexion', name: 'app_connexion')]
    public function login_first(Request $request, AuthenticationUtils $authenticationUtils, EntityManagerInterface $manager, SessionInterface $session, UserPasswordHasherInterface $passwordHasher, ServiceSecondaryDataBase $serviceSecondaryDataBase): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error) {
            $user = $manager->getRepository(Abonne::class)->findOneBy(['msisdn' => $lastUsername]);

            if ($user) {
                if ($user->isLocked()) {
                    $lockedUntil = $user->getLockedUntil();
                    if ($lockedUntil && $lockedUntil > new \DateTime()) {
                        $timeRemaining = $lockedUntil->getTimestamp() - (new \DateTime())->getTimestamp();
                        $minutesRemaining = ceil($timeRemaining / 60);
                        $this->addFlash('error', "Votre compte est verrouillé. Réessayez dans {$minutesRemaining} minutes.");
                        return $this->render('connexion/index.html.twig', ['last_username' => $lastUsername]);
                    } else {
                        // Déverrouillage automatique après expiration
                        $user->setIsLocked(false);
                        $user->setLockedUntil(null);
                        $user->setTentativeconnexion(0);
                        $manager->flush();
                    }
                }

                $user->setTentativeconnexion($user->getTentativeconnexion() + 1);

                if ($user->getTentativeconnexion() >= 3) {
                    $user->setIsLocked(true);
                    $user->setLockedUntil((new \DateTime())->modify('+15 minutes'));
                    $this->addFlash('error', 'Compte verrouillé après 3 tentatives échouées. Réessayez dans 15 minutes.');
                } else {
                    $this->addFlash('error', 'Identifiants incorrects.');
                }

                $manager->flush();
            } else {
                $this->addFlash('error', 'Identifiants incorrects.');
            }
        }

        if ($request->isMethod('POST')) {
            $msisdn = $request->request->get('msisdn');
            $password = $request->request->get('password');

            $user = $manager->getRepository(Abonne::class)->findOneBy(['msisdn' => $msisdn]);

            if (!$user) {
                $this->addFlash('error', 'Vous n\'êtes pas abonné,abonnez-vous !.');
                return $this->redirectToRoute('app_home', ['last_username' => $msisdn]);
            }

            if ($passwordHasher->isPasswordValid($user, $password)) {
                $user->setTentativeconnexion(0);
                $user->setIsLocked(false);
                $user->setLockedUntil(null);
                $manager->flush();

                $servicelient = $serviceSecondaryDataBase->getDataFromSecondaryDb();
                $adminRole = "ROLE_ADMIN";
                $identifiant ="242057080285"; //$servicelient['identifiant'] ?? null;
                $d = $user->getRoles();
                
                //dd($admin);
                $sessionData = [
                    'idAbonne' => $user->getId(),
                    'msisdn' => $user->getmsisdn(),
                    'Roles' => $d[0],
                ];

                /*if($adminRole===$d[0])
                {
                    dd($sessionData);
                }*/

                if ($adminRole === $d[0] && $user->getMsisdn() === $identifiant) {
                                       
                    /*$sessionData['identifiant'] = $servicelient['identifiant'];
                    $sessionData['client'] = $servicelient['client'];
                    $sessionData['email'] = $servicelient['email'];
                    $sessionData['secteur_activite'] = $servicelient['secteur_activite'];
                    $sessionData['situationGeographique'] = $servicelient['situationGeographique'];*/
                    $redirectTo = $this->redirectToRoute('app_admin');
                    $cookieDuration = 3600;
                } else {
#                   $this->addFlash('success', 'Vous êtes connecté.');
                    $redirectTo = $this->redirectToRoute('app_home');
                    $cookieDuration = 604800;
                }

                $session->set('Abonne', $sessionData);

                $cookie = new Cookie(
                    'Abonne',
                    json_encode(['idAbonne' => $user->getId(), 'Roles' => $user->getRoles()]),
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
                $this->addFlash('error', 'Informations incorrectes.');
                return $this->render('connexion/index.html.twig', ['last_username' => $msisdn]);
            }
        }

        return $this->render('connexion/index.html.twig', ['last_username' => $lastUsername]);
    }

    #[Route('abonne/connexion',name : 'connexion.abonne')]
    public function connexion()
    {
        return $this->render('connexion/Cusindex.html.twig');
    }

    #[Route( '/logout', 'app_logout')]
    public function logout(): void
    {
        
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

