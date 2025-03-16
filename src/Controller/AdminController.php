<?php

namespace App\Controller;

use App\Repository\MettierRepository;
use App\Repository\PublicityRepository;
use App\Repository\SettingsRepository;
use App\Repository\VilleRepository;
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
use Symfony\Component\HttpFoundation\Session\SessionInterface;



class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(RequestStack $requestStack,SettingsRepository $settingsRepository,SessionInterface $session,AbonneRepository $abonneRepository,EntrepriseRepository $entrepriseRepository,OffreRepository $offreRepository,PublicityRepository $publicityRepository): Response
    {
        $nomPlateforme = $_ENV['IDENTIFIANT_SITE'];
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        if($Abonne['identifiant']==$nomPlateforme && $Abonne['Roles']=="ROLE_ADMIN")
        {
            $NombreAbonne = $abonneRepository->countAllAbonnes();
            $NombreEnterprise =  $entrepriseRepository->countAllEnterprises();
            $offres = $offreRepository->countAllProductsAdmin();
            $stats = $abonneRepository->getAbonneStats();
            $pub = $publicityRepository->countAllPublicity();
            $actualisation = $publicityRepository->updatePublicitys();
            
            return $this->render('admin/index.html.twig', [
                'actualisation' =>$actualisation,
                'Abonnes' => $NombreAbonne,
                'entreprises'=>$NombreEnterprise,
                'offres'=>$offres,
                'total_abonnes' => $stats['total_abonnes'],
                'stats_par_ville' => $stats['stats_par_ville'],
                'publicity'=> $pub,           
            ]);            

        }

        return $this->render('offres/error404.html.twig'); 

    }

    #[Route('/abonne/list', name: 'abonne_list')]
    public function AjouterAbonne(SessionInterface $session,AbonneRepository $abonneRepository, RequestStack $requestStack, Request $request): Response
    {
        $nomPlateforme = $_ENV['IDENTIFIANT_SITE'];
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        if($Abonne['identifiant']==$nomPlateforme && $Abonne['Roles']=="ROLE_ADMIN")
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
        return $this->render('offres/error404.html.twig'); 
    }


    #[Route('admin/abonne/{id}/supprime', name: 'adminSupprime_Abonne')]
    public function delete(int $id,RequestStack $requestStack,SessionInterface $session,AbonneRepository $abonneRepository,BlacklistRepository $blacklistRepository,EntityManagerInterface $em): Response 
    {
        $nomPlateforme = $_ENV['IDENTIFIANT_SITE'];
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        if($Abonne['identifiant']==$nomPlateforme && $Abonne['Roles']=="ROLE_ADMIN")
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
        return $this->render('offres/error404.html.twig'); 
    }


    #[Route('/Admin/Abnne/nouveau', name: 'AdminAbonne_New')]
    public function newForAdmin(RequestStack $requestStack,SessionInterface $session,Request $request,MettierRepository $mettierRepository,VilleRepository $villeRepository,BlacklistService $blacklistService,SmsService $smsService, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $nomPlateforme = $_ENV['IDENTIFIANT_SITE'];
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        if($Abonne['identifiant']==$nomPlateforme && $Abonne['Roles']=="ROLE_ADMIN")
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
        
            return $this->render('abonne/new.html.twig',[
                'villes'=>$villeRepository->findAll() ?? null,
                'Mettiers'=>$mettierRepository->findAll()?? null,
            ]);
        }
        return $this->render('offres/error404.html.twig'); 
    }

    #[Route('admin/voir/offre', name: 'admin-voir.offre')]
    public function AffichageAdmin(RequestStack $requestStack,SessionInterface $session,OffreRepository $offreRepository, Request $request): Response
    {
        $nomPlateforme = $_ENV['IDENTIFIANT_SITE'];
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        
        if($Abonne['identifiant']==$nomPlateforme && $Abonne['Roles']=="ROLE_ADMIN")
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
        return $this->render('offres/error404.html.twig'); 
    }


    #[Route('admin/formOffre', name: 'creer-offre')]
    public function redirection(RequestStack $requestStack,SessionInterface $session,EntrepriseRepository $entrepriseRepository,VilleRepository $villeRepository,MettierRepository $mettierRepository): Response
    {
        $nomPlateforme = $_ENV['IDENTIFIANT_SITE'];
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        if($Abonne['identifiant']==$nomPlateforme && $Abonne['Roles']=="ROLE_ADMIN")
        { 
            $entreprises = $entrepriseRepository->afficherEntrepriseAdmin();

            return $this->render('admin/new_offers.html.twig', [
                'entreprises' => $entreprises,
                'villes'=>$villeRepository->findAll(),
                'Mettiers'=>$mettierRepository->findAll(),
            ]);
        }
        return $this->render('offres/error404.html.twig'); 
    }


    #[Route('admin/parametre', name: 'parametres')]
    public function parametre(RequestStack $requestStack,SessionInterface $session)
    {
        $nomPlateforme = $_ENV['IDENTIFIANT_SITE'];
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        if($Abonne['identifiant']==$nomPlateforme && $Abonne['Roles']=="ROLE_ADMIN")
        { 
            return $this->render('admin/parametres.html.twig');
        }
        return $this->render('offres/error404.html.twig'); 
    }

    #[Route('admin/entreprise/ajouter', name: 'ajouter-entreprise')]
    public function creerEntreprise(RequestStack $requestStack,SessionInterface $session,EntrepriseRepository $entrepriseRepository): Response
    {
        $nomPlateforme = $_ENV['IDENTIFIANT_SITE'];
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        if($Abonne['identifiant']==$nomPlateforme && $Abonne['Roles']=="ROLE_ADMIN")
        {
            $entreprises = $entrepriseRepository->afficherEntrepriseAdmin();

            return $this->render('formulaire/new_entreprise.html.twig', [
                'entreprises' => $entreprises,
            ]);
        }
        return $this->render('offres/error404.html.twig');
    }

    #[Route('admin/parametre/autre', name: 'parametre-autres')]
    public function parametreAutres(RequestStack $requestStack,SessionInterface $session): Response
    {
        $nomPlateforme = $_ENV['IDENTIFIANT_SITE'];
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        if($Abonne['identifiant']==$nomPlateforme && $Abonne['Roles']=="ROLE_ADMIN")
        {
            return $this->render('admin/autresParametres.html.twig');
        }
        return $this->render('offres/error404.html.twig');
    }

    #[Route('admin/entreprise/{id}/supprime', name: 'adminSupprime_entreprise')]
    public function deleteenterprise(RequestStack $requestStack,SessionInterface $session,int $id,EntrepriseRepository $entrepriseRepository,BlacklistRepository $blacklistRepository,EntityManagerInterface $em): Response 
    {
        $nomPlateforme = $_ENV['IDENTIFIANT_SITE'];
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        if($Abonne['identifiant']==$nomPlateforme && $Abonne['Roles']=="ROLE_ADMIN")
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

        return $this->render('offres/error404.html.twig');

    }

    #[Route('admin/changer-statut/{statut}-{id}', name:'changerStatut', requirements:['id'=>'\d+'])]
    public function changerStatut(RequestStack $requestStack,SessionInterface $session,int $id, int $statut, OffreRepository $offreRepository, EntityManagerInterface $em)
    {
        $nomPlateforme = $_ENV['IDENTIFIANT_SITE'];
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        if($Abonne['identifiant']==$nomPlateforme && $Abonne['Roles']=="ROLE_ADMIN")
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
        return $this->render('offres/error404.html.twig');
    }

 
    
    #[Route('admin/pub/change-statut/{statut}-{id}', name:'statuspub', requirements:['id'=>'\d+'])]
    public function changerStatutpub(RequestStack $requestStack,SessionInterface $session,int $id, int $statut, PublicityRepository $pubRepository, EntityManagerInterface $em)
    {
        $nomPlateforme = $_ENV['IDENTIFIANT_SITE'];
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        if($Abonne['identifiant']==$nomPlateforme && $Abonne['Roles']=="ROLE_ADMIN")
        {
          
            $pub = $pubRepository->find($id);

            if ($pub) {

                $pub->setStatus($statut);

                if ($statut == 1) {
                    // Ajout de 7 jours à la date de mise en ligne
                    if($pub->getDateExpirationAt() < new \DateTimeImmutable())
                    {
                        $dateExpiration = clone $pub->getDateMiseEnLigneAt();
                        $dateExpiration->modify('+7 days');
                        $pub->setDateExpirationAt($dateExpiration);                   
                    }
                }
            
                $em->flush();
            
                $this->addFlash('success', 'Le statut a été changé avec succès.');
                return $this->redirectToRoute('app_publicity');
            }
            $this->addFlash('error', 'Une erreur est survenue.');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('offres/error404.html.twig');
    }


    #[Route('admin/abonne/{id}/supprime-offre', name: 'adminSupprime_Offre')]
    public function deleteOffre(RequestStack $requestStack,SessionInterface $session,int $id,OffreRepository $offreRepository,EntityManagerInterface $em): Response 
    {
        $nomPlateforme = $_ENV['IDENTIFIANT_SITE'];
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        if($Abonne['identifiant']==$nomPlateforme && $Abonne['Roles']=="ROLE_ADMIN")
        {
            $offre = $offreRepository->find($id);

            if (!$offre) {
                $this->addFlash('error', 'Offre introuvable.');
                return $this->redirectToRoute('admin-voir.offre');
            }
            // Supprimer de la table Abonne
            $em->remove($offre);
            $em->flush();
    
            $this->addFlash('success', 'Offres supprimé avec succès.');
    
            return $this->redirectToRoute('admin-voir.offre');
        }
        return $this->render('offres/error404.html.twig');
    }


    #[Route('admin/abonne/{id}/supprime-pub', name: 'adminSupprime_pub')]
    public function deletePub(RequestStack $requestStack,SessionInterface $session,int $id,PublicityRepository $publicityRepository,EntityManagerInterface $em): Response 
    {
        $nomPlateforme = $_ENV['IDENTIFIANT_SITE'];
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        if($Abonne['identifiant']==$nomPlateforme && $Abonne['Roles']=="ROLE_ADMIN")        
        {
            $pub = $publicityRepository->find($id);

            if (!$pub) {
                $this->addFlash('error', 'Pub introuvable.');
                return $this->redirectToRoute('app_publicity');
            }
    
            // Supprimer de la table Abonne
            $em->remove($pub);
            $em->flush();
    
            $this->addFlash('success', 'Pub supprimée avec succès.');
    
            return $this->redirectToRoute('app_publicity');
        }
        return $this->render('offres/error404.html.twig');
    }

    #[Route('/liste/ville-{ville}',name:'Abo.ville')]
    function voirVille(RequestStack $requestStack,SessionInterface $session,String $ville,AbonneRepository $abonneRepository,Request $request)
    {
        $nomPlateforme = $_ENV['IDENTIFIANT_SITE'];
        $session = $requestStack->getSession();
        $Abonne = $session->get('Abonne');
        if($Abonne['identifiant']==$nomPlateforme && $Abonne['Roles']=="ROLE_ADMIN") 
        {
            $AbonneParPage = $request->query->getInt('AbonneParPage', 10);
            $pageActuelle = max($request->query->getInt('page', 1), 1);
    
            $total = $abonneRepository->countAllAbonnes();
            $nombreDePages = ceil($total / $AbonneParPage);
    
            $pageActuelle = min($pageActuelle, $nombreDePages);
            $premiereEntree = ($pageActuelle - 1) * $AbonneParPage;
    
            return $this->render('abonne/index.html.twig',[
                'abonnes'=>$abonneRepository->userParVille($ville,$AbonneParPage, $premiereEntree),
                'nombreDePages' => $nombreDePages,
                'premiereEntree' => $premiereEntree,                
                'pageActuelle' => $pageActuelle,
                'AbonneParPage' => $AbonneParPage,
                'total'=>$total,
    
            ]);
        }
        return $this->render('offres/error404.html.twig');
    }

    #[Route('admin/abonne/{id}/supprime-ville', name: 'adminSupprime_ville')]
    public function deleteVile(int $id,SessionInterface $session,VilleRepository $villeRepository,EntityManagerInterface $em): Response 
    {
        $ville = $villeRepository->find($id);

        if (!$ville) {
            $this->addFlash('error', 'Pub introuvable.');
            return $this->redirectToRoute('parametres-vue-ville');
        }

        // Supprimer de la table Abonne
        $em->remove($ville);
        $em->flush();

        $this->addFlash('success', 'Ville supprimée avec succès.');

        return $this->redirectToRoute('parametres-vue-ville');
    }

    #[Route('admin/abonne/{id}/supprime-mettier', name: 'adminSupprime_mettier')]
    public function deleteMettier(int $id,SessionInterface $session,MettierRepository $mettierRepository,EntityManagerInterface $em): Response 
    {
        $mettier = $mettierRepository->find($id);

        if (!$mettier) {
            $this->addFlash('error', 'Pub introuvable.');
            return $this->redirectToRoute('parametres-vue-mettier');
        }

        // Supprimer de la table Abonne
        $em->remove($mettier);
        $em->flush();

        $this->addFlash('success', 'mettier supprimée avec succès.');

        return $this->redirectToRoute('parametres-vue-mettier');
    }
}
