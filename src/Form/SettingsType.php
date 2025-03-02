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

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifiant', TextType::class, [
                'label' => 'Identifiant'
            ])
            ->add('nomPlateforme', TextType::class, [
                'label' => 'Nom de la plateforme'
            ])
            ->add('utilisateurPlateforme', TextType::class, [
                'label' => 'Utilisateur de la plateforme'
            ])
            ->add('logoEntete', FileType::class, [
                'label' => 'Logo entête',
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/*']
            ])
            ->add('logoNavBar', FileType::class, [
                'label' => 'Logo barre de navigation',
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/*']
            ])
            ->add('motCle', TextType::class, [
                'label' => 'Mot Clé'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('imageAccueil', FileType::class, [
                'label' => 'Image de la page d\'accueil',
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/*']
            ])
            ->add('titre', TextType::class, [
                'label' => 'Titre de la page d\'accueil'
            ])
            ->add('sousTitre', TextType::class, [
                'label' => 'Sous-titre de la page d\'accueil'
            ])
            ->add('titreBande', TextType::class, [
                'label' => 'Titre de la bande d\'action'
            ])
            ->add('sousTitreBande', TextType::class, [
                'label' => 'Sous-titre de la bande d\'action'
            ])
            ->add('titreStat', TextType::class, [
                'label' => 'Titre Stat'
            ])
            ->add('sousTitreStat', TextType::class, [
                'label' => 'Sous Titre Stat'
            ])
            ->add('abonnements', NumberType::class, [
                'label' => 'Abonnements'
            ])
            ->add('offresPostulees', NumberType::class, [
                'label' => 'Offres Postulées'
            ])
            ->add('emploisPourvus', NumberType::class, [
                'label' => 'Emplois Pourvus'
            ])
            ->add('entreprise', NumberType::class, [
                'label' => 'Entreprise'
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
                'label' => 'LinkedIn'
            ])
            ->add('addresse', TextType::class, [
                'label' => 'Adresse'
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('secteurActivite', TextType::class, [
                'label' => 'Secteur Activité'
            ])
            ->add('situationGeographique', TextType::class, [
                'label' => 'Situation Géographique'
            ])
            ->add('textFooter', TextareaType::class, [
                'label' => 'Texte Footer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}
