<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\AbonneRepository;
use App\Entity\Blacklist;
use App\Repository\BlacklistRepository;
use App\Repository\OffreRepository;
use App\Entity\Offre;
use App\Repository\EntrepriseRepository;
use App\Entity\Entreprise;
use App\Service\BlacklistService;
use App\Service\SmsService;
use App\Entity\Abonne;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;



class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(AbonneRepository $abonneRepository,EntrepriseRepository $entrepriseRepository,OffreRepository $offreRepository): Response
    {
        $NombreAbonne = $abonneRepository->countAllAbonnes();
        $NombreEnterprise =  $entrepriseRepository->countAllEnterprises();
        $offres = $offreRepository->countAllProductsAdmin();
        
        return $this->render('admin/index.html.twig', [
            'Abonnes' => $NombreAbonne,
            'entreprises'=>$NombreEnterprise,
            'offres'=>$offres,
        ]);
    }

    #[Route('/abonne/list', name: 'abonne_list')]
    public function AjouterAbonne(AbonneRepository $abonneRepository, RequestStack $requestStack, Request $request): Response
    {
        //$abonnes = $abonneRepository->findAll();
        $AbonneParPage = $request->query->getInt('AbonneParPage', 10);
        $pageActuelle = max($request->query->getInt('page', 1), 1);

        $total = $abonneRepository->countAllAbonnes();
        $nombreDePages = ceil($total / $AbonneParPage);

        $pageActuelle = min($pageActuelle, $nombreDePages);
        $premiereEntree = ($pageActuelle - 1) * $AbonneParPage;

        $abonnes = $abonneRepository->afficherAbonnes($AbonneParPage, $premiereEntree);


        return $this->render('abonne/index.html.twig', [
            'abonnes' => $abonnes,
            'nombreDePages' => $nombreDePages,
            'premiereEntree' => $premiereEntree,                
            'pageActuelle' => $pageActuelle,
            'AbonneParPage' => $AbonneParPage,
            'total'=>$total,
        ]);
    }


    #[Route('admin/abonne/{id}/supprime', name: 'adminSupprime_Abonne')]
    public function delete(int $id,AbonneRepository $abonneRepository,BlacklistRepository $blacklistRepository,EntityManagerInterface $em): Response 
    {
        $abonne = $abonneRepository->find($id);

        if (!$abonne) {
            $this->addFlash('error', 'Abonné introuvable.');
            return $this->redirectToRoute('abonne_list');
        }

        // Ajouter à la table Blacklist
        $blacklist = new Blacklist();
        $blacklist->setMSISDN($abonne->getMsisdn()); // Si MSISDN peut être considéré comme contact
        $blacklist->setDateAjout(new \DateTimeImmutable());
        $blacklist->setSpecialite($abonne->getSpecialite());
        $blacklist->setVille($abonne->getVille());

        $em->persist($blacklist);

        // Supprimer de la table Abonne
        $em->remove($abonne);
        $em->flush();

        $this->addFlash('success', 'Abonné déplacé vers la Blacklist avec succès.');

        return $this->redirectToRoute('abonne_list');
    }


    #[Route('/Admin/Abnne/nouveau', name: 'AdminAbonne_New')]
    public function newForAdmin(Request $request,BlacklistService $blacklistService,SmsService $smsService, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $cout = 250;

        if ($request->isMethod('POST')) {
            $msisdn = $request->request->get('msisdn');
    
            // Vérifier si le numéro MSISDN existe déjà dans la base de données
            $existingAbonne = $em->getRepository(Abonne::class)->findOneBy(['msisdn' => $msisdn]);
    
            if ($existingAbonne) {
                $this->addFlash('error', 'Ce numéro de téléphone est déjà enregistré.');
                return $this->redirectToRoute('abonne_new');
            }
    
            // Si le MSISDN est unique, enregistrer l'abonné
            $abonne = new Abonne();
            $abonne->setMsisdn($msisdn);
            $abonne->setVille($request->request->get('Ville'));
            $abonne->setSpecialite($request->request->get('specialite'));
            //$abonne->setRoles(['ROLE_USER']);
            $abonne->setTentativeconnexion(0);
            $abonne->setPassword($passwordHasher->hashPassword($abonne, $request->request->get('password')));
            $abonne->setCreateAt(new \DateTimeImmutable());
            $abonne->setModePaiement($request->request->get('mode_debit'));
            $modePaiement = $request->request->get('mode_debit');

            $blacklistService->deleteByMsisdn($msisdn);
            
            $em->persist($abonne);
            $em->flush();
    
            // 📲 Envoi de SMS de bienvenue
            if($modePaiement = 'AM')
            {
                $message = "Bienvenue sur notre plateforme d'alerte emploi, le coût de souscription est de {$cout} Frs par AM.";

            }else{
                $message="Bienvenue sur notre plateforme d'alerte emploi, le coût de souscription est de {$cout} Frs par credit.";
            }
            
            $smsService->sendSms($msisdn, '', $message);
            
            //$success= $this->$sms->sendSms($msisdn,'',$message); // Correct method call
            $smsService->sendSms($msisdn,'',$message);

            if($abonne->getRoles()==['ROLE_ADMIN'])
            {
                $this->addFlash('success', 'Abonné ajouté avec succès.');
                return $this->redirectToRoute('abonne_list');
            }else
            {
                $url = $this->generateUrl('connexion.abonne'); // Génère l'URL pour la route 'connexion.abonne'

                $this->addFlash('success', 'Abonné ajouté avec succès');
                return $this->redirectToRoute('AdminAbonne_New');
            }
        }
    
        return $this->render('abonne/new.html.twig');
    }

    #[Route('admin/voir/offre', name: 'admin-voir.offre')]
    public function AffichageAdmin(OffreRepository $offreRepository, Request $request): Response
    {
        $produitsParPage = $request->query->getInt('offresParPage', 10);
        $pageActuelle = max($request->query->getInt('page', 1), 1);

        $total = $offreRepository->countAllProductsAdmin();
        
        $nombreDePages = ceil($total / $produitsParPage);

        $pageActuelle = min($pageActuelle, $nombreDePages);
        $premiereEntree = ($pageActuelle - 1) * $produitsParPage;

        $offres = $offreRepository->afficherOffresAdmin($produitsParPage, $premiereEntree);
        $Offrestotal = $offreRepository->countAllProductsAdmin();
        
        //dd($offres);

        return $this->render('offres/voir_admin.html.twig', [
            'offres' => $offres,
            'nombreDePages' => $nombreDePages,
            'premiereEntree' => $premiereEntree,                
            'pageActuelle' => $pageActuelle,
            'produitsParPage' => $produitsParPage,
            'total' => $Offrestotal,
        ]);
    }


    #[Route('admin/formOffre', name: 'creer-offre')]
    public function redirection(EntrepriseRepository $entrepriseRepository): Response
    {
        $entreprises = $entrepriseRepository->afficherEntrepriseAdmin();

        return $this->render('admin/new_offers.html.twig', [
            'entreprises' => $entreprises,
        ]);
    }


    #[Route('admin/parametre', name: 'parametres')]
    public function parametre()
    {
        return $this->render('admin/parametres.html.twig');
    }

    #[Route('admin/entreprise/ajouter', name: 'ajouter-entreprise')]
    public function creerEntreprise(EntrepriseRepository $entrepriseRepository): Response
    {
        $entreprises = $entrepriseRepository->afficherEntrepriseAdmin();

        return $this->render('formulaire/new_entreprise.html.twig', [
            'entreprises' => $entreprises,
        ]);
    }

    #[Route('admin/entreprise/{id}/supprime', name: 'adminSupprime_entreprise')]
    public function deleteenterprise(int $id,EntrepriseRepository $entrepriseRepository,BlacklistRepository $blacklistRepository,EntityManagerInterface $em): Response 
    {
        $entreprise = $entrepriseRepository->find($id);

        if (!$entreprise) {
            $this->addFlash('error', 'entreprise introuvable.');
            return $this->redirectToRoute('abonne_list');
        }

        // Supprimer de la table Abonne
        $em->remove($entreprise);
        $em->flush();

        $this->addFlash('success', 'Entreprise supprimée avec succès.');

        return $this->redirectToRoute('entreprise_list');
    }

    #[Route('admin/changer-statut/{statut}-{id}', name:'changerStatut', requirements:['id'=>'\d+'])]
    public function changerStatut(int $id, int $statut, OffreRepository $offreRepository, EntityManagerInterface $em)
    {
        $offre = $offreRepository->find($id);

        if ($offre) {

            $offre->setStatutOffre($statut);
            
            if ($statut == 0) {
                // Ajout de 7 jours à la date de mise en ligne
                if($offre->getDateExpirationAt() < new \DateTimeImmutable())
                {
                    $dateExpiration = clone $offre->getDateMiseEnLigneAt();
                    $dateExpiration->modify('+7 days');
                    $offre->setDateExpirationAt($dateExpiration);                   
                }
            }
    
            $em->flush();
    
            $this->addFlash('success', 'Le statut a été changé avec succès.');
            return $this->redirectToRoute('admin-voir.offre');
        }
    
        $this->addFlash('error', 'Une erreur est survenue.');
        return $this->redirectToRoute('app_home');
    }

}
