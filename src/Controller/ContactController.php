<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OffreRepository;
use Symfony\Component\HttpFoundation\RequestStack;


class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(OffreRepository $offreRepository,RequestStack $requestStack): Response
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
        return $this->render('contact/index.html.twig', [
            'statut'=>$connected,
            'idAbonne'=>$idUser,
        ]);
    }
}
