<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OffreRepository;
use App\Service\ServiceSecondaryDataBase;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EntrepriseRepository;
use App\Repository\PublicityRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(OffreRepository $offreRepository,RequestStack $requestStack,PublicityRepository $publicityRepository,entrepriseRepository $entrepriseRepository,SessionInterface $session, Request $request): Response
    {
        // Nombre d'offres par page (par défaut : 10)
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        $connected = false;

        if($Abonne)
        {
            $connected = true;
           // dd($Abonne);
        }
        $produitsParPage = $request->query->getInt('offresParPage', 10);
        $pageActuelle = max($request->query->getInt('page', 1), 1);

        // Nombre total d'offres
        $total = $offreRepository->countAllProducts();
        $nombreDePages = ceil($total / $produitsParPage);

        // Ajustement de la page actuelle pour éviter les débordements
        $pageActuelle = min($pageActuelle, $nombreDePages);
        $premiereEntree = ($pageActuelle - 1) * $produitsParPage;

        // Récupération des offres pour la page actuelle
        $offres = $offreRepository->afficherOffres($produitsParPage, $premiereEntree);
        
        // Rendu du template avec les données nécessaires
        return $this->render('home/index.html.twig', [
            'home_offre' => $offres,
            'nombreDePages' => $nombreDePages,
            'pageActuelle' => $pageActuelle,
            'produitsParPage' => $produitsParPage,
            'entreprise' => $entrepriseRepository->afficherEntrepriseAdmin(),
            'total' => $total,
            'publicite'=>$publicityRepository->findAll(),
            'premiereEntree' => $premiereEntree,
            'statut'=>$connected,
        ]);
    }
    
    #[Route('/secondary-db', name: 'app_homeAdmin')]
    public function second(ServiceSecondaryDataBase $secondaryDatabaseService): Response
    {
        $data = $secondaryDatabaseService->getDataFromSecondaryDb();

        return $this->json($data);
    }

}
