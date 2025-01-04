<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NavbarController extends AbstractController
{
    #[Route('/navbar', name: 'app_navbar')]
    public function index(RequestStack $requestStack,SessionInterface $session): Response
    {
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        $connected = false;

        $idUser = '';

        if($Abonne)
        {
            $connected = true;
            $idUser = $Abonne['idAbonne'];
           // dd($Abonne);
        }
        return $this->render('_navbar.html.twig', [
            'statut'=>$connected,
            'idAbonne'=>$idUser,

        ]);
    }
}
