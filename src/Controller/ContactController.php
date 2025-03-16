<?php

namespace App\Controller;

use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ServiceSecondaryDataBase;
use Symfony\Component\HttpFoundation\RequestStack;


class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(SettingsRepository $settingsRepository,ServiceSecondaryDataBase $serviceSecondaryDataBase,RequestStack $requestStack): Response
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
            'monSite'=> $settingsRepository->findByIdentifiant($_ENV['IDENTIFIANT_SITE']),

        ]);
    }
}
