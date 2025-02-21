<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\DocumentRepository;
use App\Repository\PublicityRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class DocumentController extends AbstractController
{
    #[Route("/download/{id}", name:"document_download", methods:['GET'])]
    public function download(int $id, DocumentRepository $documentRepository): Response
    {
        // Récupérer le document depuis la base de données
        $document = $documentRepository->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Document non trouvé.');
        }

        // Récupérer le chemin du fichier
        $filePath = $document->getFilePath();

        // Vérifier si le fichier existe sur le disque
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Fichier introuvable sur le serveur.');
        }

        // Retourner le fichier en réponse pour téléchargement
        return $this->file($filePath, $document->getName());
    }

    #[Route('abonne/bibliotheque', name: 'abo-voir.bibliotheque')]
    public function Affichage(DocumentRepository $DocumentRepository, RequestStack $requestStack,PublicityRepository $publicityRepository, Request $request): Response
    {
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');


        if($Abonne)
        {
            $idUser = $Abonne['idAbonne'];
            $statut = true;
  
            $DocumentsParPage = $request->query->getInt('DocumentsParPage', 10);
            $pageActuelle = max($request->query->getInt('page', 1), 1);

            $total = $DocumentRepository->countAllDocuments();
            $nombreDePages = ceil($total / $DocumentsParPage);

            $pageActuelle = min($pageActuelle, $nombreDePages);
            $premiereEntree = ($pageActuelle - 1) * $DocumentsParPage;

            $templateDoc = $DocumentRepository->afficherDocuments($DocumentsParPage, $premiereEntree);

            //dd($templateDoc);

            return $this->render('document/index.html.twig', [
                'templateDoc' => $templateDoc,
                'nombreDePages' => $nombreDePages,
                'pageActuelle' => $pageActuelle,
                'DocumentsParPage' => $DocumentsParPage,
                'total' => $total,
                'publicite' => $publicityRepository->findAll(),
                'premiereEntree' => $premiereEntree,
                'idAbonne'=>$idUser,
                'statut'=>$statut,
            ]);
        }else
        {
            $this->addFlash('error', 'Veuillez vous identifier');
            return $this->render('connexion/Cusindex.html.twig');
        }
    }
}
