<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends ApplicationType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add(
                'title',
                TextType::class,
                $this->getConfiguration("titre", "Tapez un titre !")
            )
            ->add(
                'slug',
                TextType::class,
                $this->getConfiguration("Chaine URL", "Adresse web (Automatique)", [
                    'required' => false
                ])
            )
            ->add(
                'coverImage',
                UrlType::class,
                $this->getConfiguration("Url de l'image principale", "Donnez une adresse de l'image principale")
            )
            ->add(
                'introduction',
                TextType::class,
                $this->getConfiguration("Introduction", "Donnez une description global de l'annonce")
            )
            ->add(
                'content',
                TextAreaType::class,
                $this->getConfiguration("Description détaillée", "Donnez une description détaillée")
            )
            ->add(
                'rooms',
                IntegerType::class,
                $this->getConfiguration("Nombre de chambres", "Donnez le nombre de chambres disponibles")
            )
            ->add(
                'price',
                MoneyType::class,
                $this->getConfiguration("Prix pour une nuit", "Indiquer le prix pour une nuit")
            )
            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
