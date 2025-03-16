<?php

namespace App\Controller;

use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OffreRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\ServiceSecondaryDataBase;

class AboutController extends AbstractController
{
    #[Route('/about', name: 'app_about')]
    public function index(SettingsRepository $settingsRepository,ServiceSecondaryDataBase $serviceSecondaryDataBase,RequestStack $requestStack): Response
    {
        
        $session = $requestStack->getSession();
        $monSite = $settingsRepository->findByIdentifiant($_ENV['IDENTIFIANT_SITE']);
        $Abonne = $session->get('Abonne');
        $connected = false;
        $idUser = '';

        if($Abonne)
        {
            $connected = true;
            $idUser = $Abonne['idAbonne'];
           // dd($Abonne);
        }

        return $this->render('about/index.html.twig', [
            'statut'=>$connected,
            'idAbonne'=>$idUser,
            'monSite'=>$monSite,
        ]);
    }
}
