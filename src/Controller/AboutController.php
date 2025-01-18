<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OffreRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\ServiceSecondaryDataBase;

class AboutController extends AbstractController
{
    #[Route('/about', name: 'app_about')]
    public function index(ServiceSecondaryDataBase $serviceSecondaryDataBase,RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $monSite = $serviceSecondaryDataBase->getDataFromSecondaryDb();
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
            'site'=>$monSite,
        ]);
    }
}
