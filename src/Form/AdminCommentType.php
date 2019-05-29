<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminCommentType extends ApplicationType {

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add(
                'content',
                TextareaType::class,
                $this->getConfiguration("Contenu du commentaire",
                "Modifier / corriger le contenu du commentaire !", ["required" => false])
            );
    }

    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
