<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Entity\Entreprise;
use App\Repository\EntrepriseRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class EntrepriseController extends AbstractController
{
    #[Route('/entreprise', name: 'entreprise_list')]
    public function index(EntrepriseRepository $entrepriseRepository,Request $request): Response
    {
        $nombreEntreprises = $entrepriseRepository->countAllEnterprises();

        $EntrepriseParPage = $request->query->getInt('EntrepriseParPage', 10);
        $pageActuelle = max($request->query->getInt('page', 1), 1);

        $total = $nombreEntreprises;
        $nombreDePages = ceil($total / $EntrepriseParPage);

        $pageActuelle = min($pageActuelle, $nombreDePages);
        $premiereEntree = ($pageActuelle - 1) * $EntrepriseParPage;

        $entreprise = $entrepriseRepository->afficherEntreprise($EntrepriseParPage, $premiereEntree);


        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprise,
            'NbreEnterprise'=>$nombreEntreprises,
            'nombreDePages' => $nombreDePages,
            'premiereEntree' => $premiereEntree,                
            'pageActuelle' => $pageActuelle,
            'EntrepriseParPage' => $EntrepriseParPage,
            'total'=>$total,
        ]);
    }

    #[Route('/entreprise/new', name: 'entreprise_new', methods: ['GET', 'POST'])]
    public function new(Request $request,EntrepriseRepository $entrepriseRepository, EntityManagerInterface $entityManager,#[Autowire('%uploads_directory%')] string $uploads_directory): Response
    {
        if ($request->isMethod('POST')) {
            // Récupération des données du formulaire
            $libele = $request->request->get('libele');
            $secteur = $request->request->get('secteur');
            $description = $request->request->get('description');
            $situationGeographique = $request->request->get('situation_geographique');

            $society = $entrepriseRepository->findOneBy(['libele' => $libele]);

            if ($society) {
                // Message flash d'erreur
                $this->addFlash(
                    'error', 
                    "<h4 class='fw-bold text-danger'>Une société du même nom existe déjà.</h4> <br>
                    <strong>Nom :</strong> {$society->getLibele()} <br>
                    <strong>Secteur :</strong> {$society->getSecteur()} <br>
                    <strong>Description :</strong> {$society->getDescription()} <br>
                    <strong>Situation Géographique :</strong> {$society->getSituationGeographique()}"
                );            
                // Affichage des détails de l'entreprise existante
                return $this->redirectToRoute('entreprise_new');
            }         
            // Validation des données (vous pouvez ajouter des validations supplémentaires)
            if (!$libele || !$secteur || !$description || !$situationGeographique) {
                $this->addFlash('error', 'Tous les champs sont requis.');
                return $this->redirectToRoute('entreprise_new');
            }
            $logo = $request->files->get('logo');
            //dd($logo->isValid());

            if ($logo && $logo->isValid()) {
                if (!in_array($logo->getClientMimeType(), ['image/png', 'image/jpeg', 'image/jpg'])) {
                    $this->addFlash('error', 'Seuls les fichiers PNG et JPG sont autorisés.');
                    return $this->redirectToRoute('entreprise_new');
                }
            
                if ($logo->getSize() > 5 * 1024 * 1024) {
                    $this->addFlash('error', 'La taille du fichier ne doit pas dépasser 5 Mo.');
                    return $this->redirectToRoute('entreprise_new');
                }
            
                $newFilename = uniqid() . '.' . $logo->guessExtension();
                try {
                    $logo->move($uploads_directory, $newFilename);
                    
                } catch (FileException $e) {
                    $this->addFlash('error', 'Échec du téléchargement de l\'image.');
                    return $this->redirectToRoute('entreprise_new');
                }
            } else {
                $this->addFlash('error', 'L\'image est obligatoire.');
                return $this->redirectToRoute('entreprise_new');
            }           
            // Création de l'objet Entreprise
            $entreprise = new Entreprise();
            $entreprise-> setLogo($newFilename);
            $entreprise->setLibele($libele);
            $entreprise->setSecteur($secteur);
            $entreprise->setdescription($description);
            $entreprise->setSituationGeographique($situationGeographique);

            // Sauvegarde dans la base de données
            $entityManager->persist($entreprise);
            $entityManager->flush();

            // Message de succès
            $this->addFlash('success', 'Entreprise ajoutée avec succès.');

            // Redirection (par exemple, vers une liste des entreprises)
            return $this->redirectToRoute('entreprise_list');
        }

        // Affichage du formulaire
        return $this->render('formulaire/new_entreprise.html.twig');
    }
}
