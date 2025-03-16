<?php

namespace App\Controller;

use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OffreRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\EntrepriseRepository;
use App\Repository\PublicityRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Entreprise;
use App\Entity\Offre;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Service\ServiceSecondaryDataBase;

class OffresController extends AbstractController
{
    #[Route('/offres/{slug}-{id}', name: 'app_offres', requirements: ['id' => '\d+', 'slug' => '[a-zA-Z0-9/-]+'])]
    public function index(SettingsRepository $settingsRepository,OffreRepository $offreRepository,ServiceSecondaryDataBase $serviceSecondaryDataBase,RequestStack $requestStack, EntrepriseRepository $entrepriseRepository, Request $request, int $id, string $slug, SessionInterface $session): Response 
    {
        $job = $offreRepository->find($id);
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

        if (!$job) {
            throw $this->createNotFoundException('Offre pas disponible.');
        }

        if ($job->getSlug() !== $slug) {
            return $this->redirectToRoute('app_offres', [
                'id' => $job->getId(),
                'slug' => $job->getSlug(),
                'statut'=>$connected,
                'idAbonne'=>$idUser,
            ], 301);
        }

        $entreprise = $entrepriseRepository->find($job->getEntreprise()->getId());

        return $this->render('offres/index.html.twig', [
            'job' => $job,
            'entreprise' => $entreprise,
            'statut'=>$connected,
            'idAbonne'=>$idUser,
            'monSite'=> $settingsRepository->findByIdentifiant($_ENV['IDENTIFIANT_SITE']),

        ]);
    }

    
    #[Route('/ajoute-offre', name: 'nouvelle.offre', methods: ['POST'])]
    public function ajouter_offre(Request $request,EntityManagerInterface $entityManager,EntrepriseRepository $entrepriseRepository,#[Autowire('%uploads_directory%')] string $uploads_directory): Response 
    {
        if (!$request->isMethod('POST')) {
            return $this->redirectToRoute('creer-offre');
        }
    
        $data = array_map('trim', $request->request->all());
        $offre = new Offre();
        
        $offre->setTitre($data['titre']);
        $offre->setSecteur($data['secteur']);
        $offre->setTypeContrat($data['type_contrat']);
        $offre->setLieu($data['lieu']);
        $offre->setDescription($data['description']);
        $offre->setSlug($data['slug']);
        $offre->setSalaire($data['salaire'] ?? null);
        $offre->setLienEmployeur($data['LienEmployeur'] ?? null);
        $offre->setTempsTaff($data['tempsTaff'] ?? null);
        $offre->setNiveauRequis($data['niveauRequis'] ?? null);
        $offre->setAutreDetails($data['AutreDetails'] ?? null);
        $offre->setGenre($data['genre'] ?? null);
        $offre->setExperience($data['experience'] ?? null);
        $offre->setReponsabilities(array_filter(array_map('trim', explode(',', $data['responsibilities'] ?? ''))));
        $offre->setCompetences(array_filter(array_map('trim', explode(',', $data['competences'] ?? ''))));
    
        $offre->setDateMiseEnLigneAt(new \DateTimeImmutable());
    
        try 
        {
            $offre->setDateExpirationAt(new \DateTimeImmutable($data['date_expiration_at']));
        } catch (\Exception $e) {
            $this->addFlash('error', 'Date d\'expiration invalide.');
            return $this->redirectToRoute('creer-offre');
        }
    
        $statut = 0;
        $offre->setStatutOffre(!empty($data[$statut]));
    
        $entreprise = $entrepriseRepository->find($data['entreprise'] ?? null);
        if (!$entreprise) {
            $this->addFlash('error', 'Entreprise non trouvée.');
            return $this->redirectToRoute('creer-offre');
        }
    
        $offre->setEntreprise($entreprise);
    
        $illustrationFile = $request->files->get('illustrationImage');
        if (!$illustrationFile) {
            $this->addFlash('error', 'Aucune image envoyée.');
            return $this->redirectToRoute('creer-offre');
        }
        //dd($illustrationFile);

        if ($illustrationFile && $illustrationFile->isValid()) {
            if (!in_array($illustrationFile->getClientMimeType(), ['image/png', 'image/jpeg', 'image/jpg'])) {
                $this->addFlash('error', 'Seuls les fichiers PNG et JPG sont autorisés.');
                return $this->redirectToRoute('creer-offre');
            }
        
            if ($illustrationFile->getSize() > 5 * 1024 * 1024) {
                $this->addFlash('error', 'La taille du fichier ne doit pas dépasser 5 Mo.');
                return $this->redirectToRoute('creer-offre');
            }
        
            $newFilename = uniqid() . '.' . $illustrationFile->guessExtension();
            try {
                $illustrationFile->move($uploads_directory, $newFilename);
                $offre->setIllustrationImage($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Échec du téléchargement de l\'image.');
                return $this->redirectToRoute('creer-offre');
            }
        } else {
            $this->addFlash('error', 'L\'image est obligatoire.');
            return $this->redirectToRoute('creer-offre');
        }
    
        $entityManager->persist($offre);
        $entityManager->flush();
    
        $this->addFlash('success', 'Offre ajoutée avec succès!');
        return $this->redirectToRoute('admin-voir.offre');
    }


    #[Route('abonne/voir-offre', name: 'abo-voir.offre')]
    public function Affichage(SettingsRepository $settingsRepository,OffreRepository $offreRepository, RequestStack $requestStack,PublicityRepository $publicityRepository, EntrepriseRepository $entrepriseRepository, Request $request): Response
    {
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');


        if($Abonne)
        {
            $idUser = $Abonne['idAbonne'];
            $statut = true;
  
            $produitsParPage = $request->query->getInt('offresParPage', 10);
            $pageActuelle = max($request->query->getInt('page', 1), 1);

            $total = $offreRepository->countAllProducts();
            $nombreDePages = ceil($total / $produitsParPage);

            $pageActuelle = min($pageActuelle, $nombreDePages);
            $premiereEntree = ($pageActuelle - 1) * $produitsParPage;

            $offres = $offreRepository->afficherOffres($produitsParPage, $premiereEntree);

            return $this->render('offres/voir_abonne.html.twig', [
                'home_offre' => $offres,
                'nombreDePages' => $nombreDePages,
                'premiereEntree' => $premiereEntree,                
                'pageActuelle' => $pageActuelle,
                'produitsParPage' => $produitsParPage,
                'entreprise' => $entrepriseRepository->afficherEntrepriseAdmin(),
                'total' => $total,
                'publicite' => $publicityRepository->findAll(),
                'idAbonne'=>$idUser,
                'monSite'=> $settingsRepository->findByIdentifiant($_ENV['IDENTIFIANT_SITE']),
                'statut'=>$statut,
            ]);
        }else
        {
            $this->addFlash('error', 'Veuillez vous identifier');
            return $this->render('connexion/Cusindex.html.twig');
        }
    }
    

    #[Route('/search-offres', name: 'search_offres', methods: ['GET'])] // Route pour la recherche AJAX
    public function searchOffres(OffreRepository $offreRepository, Request $request): JsonResponse
    {
        $searchTerm = $request->query->get('q', ''); // Récupère le terme de recherche ou une chaîne vide par défaut

        if (empty($searchTerm)) {
            return new JsonResponse([]); // Retourne un tableau vide si la recherche est vide
        }

        $offres = $offreRepository->findByTitle($searchTerm);

        // Transforme les résultats en un tableau associatif pour le JSON
        $data = [];
        foreach ($offres as $offre) {
            $data[] = [
                'id' => $offre->getId(),
                'slug' => $offre->getSlug(),
                'titre' => $offre->getTitre(),
                'entreprise' => [
                    'libele' => $offre->getEntreprise()->getLibele(),
                    'logo' => $offre->getEntreprise()->getLogo(),
                ],
                'lieu' => $offre->getLieu(),
                'tempsTaff' => $offre->getTempsTaff(),
            ];
        }

        return new JsonResponse($data);
    }
}
