<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Entreprise;
use App\Repository\EntrepriseRepository;

class EntrepriseController extends AbstractController
{
    #[Route('/entreprise', name: 'entreprise_list')]
    public function index(EntrepriseRepository $entrepriseRepository): Response
    {
        $entreprise = $entrepriseRepository->afficherEntrepriseAdmin();

        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprise,
        ]);
    }

    #[Route('/entreprise/new', name: 'entreprise_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            // Récupération des données du formulaire
            $libele = $request->request->get('libele');
            $secteur = $request->request->get('secteur');
            $description = $request->request->get('Description');
            $situationGeographique = $request->request->get('situation_geographique');

            // Validation des données (vous pouvez ajouter des validations supplémentaires)
            if (!$libele || !$secteur || !$description || !$situationGeographique) {
                $this->addFlash('error', 'Tous les champs sont requis.');
                return $this->redirectToRoute('entreprise_new');
            }

            // Création de l'objet Entreprise
            $entreprise = new Entreprise();
            $entreprise->setLibele($libele);
            $entreprise->setSecteur($secteur);
            $entreprise->setDescription($description);
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
