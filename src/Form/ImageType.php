<?php

namespace App\Form;

use App\Entity\Image;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends ApplicationType{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add(
                'url',
                UrlType::class,
                $this->getConfiguration("Url", "URL de l'image !")
            )
            ->add(
                'caption',
                TextType::class,
                $this->getConfiguration("Caption", "Titre de l'image !")
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
