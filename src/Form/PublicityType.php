<?php

namespace App\Form;

use App\Entity\Publicity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class PublicityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imagePub', FileType::class, [
                'label' => 'Image de la publicité',
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/*']
            ])
            ->add('lienOrientation', UrlType::class, [
                'label' => 'Lien d\'orientation',
            ])
            ->add('libele', TextType::class, [
                'label' => 'Libellé',
            ])
            ->add('demandeur', TextType::class, [
                'label' => 'Demandeur',
            ])
            ->add('contact', TextType::class, [
                'label' => 'Contact',
            ])
            ->add('textPub', TextareaType::class, [
                'label' => 'Texte de la publicité',
                'required' => false,
            ])
            ->add('dateExpirationAt', dateType::class, [
                'label' => 'Date d\'expiration',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publicity::class,
        ]);
    }
}