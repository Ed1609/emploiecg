<?php
// src/Form/SettingsType.php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\HttpFoundation\Request;
class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder,array $options)
    {
        $builder
            ->add('identifiant', TextType::class, [
                'label' => 'Identifiant',
                'disabled' => true,
                //'data'=>$Abonne['identifiant']?? 'null'
            ])
            ->add('nomPlateforme', TextType::class, [
                'label' => 'Nom de la plateforme',
                'disabled' => true
                //'data'=>$Abonne['nomPlateforme']?? 'ElemboTech'
            ])
            ->add('utilisateurPlateforme', TextType::class, [
                'label' => 'Utilisateur de la plateforme',
                'disabled' => true
            ])
            ->add('logoEntete', FileType::class, [
                'label' => 'Logo entête',
                'mapped' => false,
                'required' => false,
                'disabled' => true,
                'attr' => ['accept' => 'image/*']
            ])
            ->add('logoNavBar', FileType::class, [
                'label' => 'Logo barre de navigation',
                'mapped' => false,
                'required' => false,
                'disabled' => true,
                'attr' => ['accept' => 'image/*'],
            ])

            ->add('motCle', TextType::class, [
                'label' => 'Mot Clé'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'A propos de votre site'
            ])
            ->add('imageAccueil', FileType::class, [
                'label' => 'Image de la page d\'accueil',
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/*']
            ])
            ->add('titre', TextType::class, [
                'label' => 'Phrase principale de la page d\'accueil'
            ])
            ->add('sousTitre', TextType::class, [
                'label' => 'Sous-titre de la phrase de la page d\'accueil'
            ])
            ->add('titreBande', TextType::class, [
                'label' => 'Titre de la bande d\'action'
            ])
            ->add('sousTitreBande', TextType::class, [
                'label' => 'Sous-titre de la bande d\'action'
            ])
            ->add('titreStat', TextType::class, [
                'label' => 'Phrase pour les Stats'
            ])
            ->add('sousTitreStat', TextType::class, [
                'label' => 'Sous-titre de la phrase des Stat'
            ])
            ->add('abonnements', NumberType::class, [
                'label' => 'Nombre d\'Abonnements sur le site'
            ])
            ->add('offresPostulees', NumberType::class, [
                'label' => 'Nombre d\'Offres Postulées'
            ])
            ->add('emploisPourvus', NumberType::class, [
                'label' => 'Nombre d\'Emplois Pourvus'
            ])
            ->add('entreprise', NumberType::class, [
                'label' => 'Nombre d\'Entreprises partenaires'
            ])
            ->add('lienFacebook', UrlType::class, [
                'label' => 'Lien Facebook'
            ])
            ->add('lienTwitter', UrlType::class, [
                'label' => 'Lien Twitter'
            ])
            ->add('lienInstagram', UrlType::class, [
                'label' => 'Lien Instagram'
            ])
            ->add('linkedIn', UrlType::class, [
                'label' => 'Lien LinkedIn'
            ])
            ->add('addresse', TextType::class, [
                'label' => 'Votre Adresse physique'
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Numero de téléphone du site'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email'
            ])
            ->add('secteurActivite', TextType::class, [
                'label' => 'Secteur Activité de la plateforme'
            ])
            ->add('situationGeographique', TextType::class, [
                'label' => 'Situation Géographique'
            ])
            ->add('textFooter', TextareaType::class, [
                'label' => 'Texte pied de la page'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}
