<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rating', IntegerType::class, [
                    'label' => "Votre note",
                    'attr' => [
                        'placeholder' => "Note sur 5",
                        'min' => 0,
                        'max' => 5,
                        'step' => 1
                    ]
            ])
            ->add('content', TextareaType::class, [
                'label' => "Votre avis",
                'attr' => [
                    'placeholder' => "Ce séjour vous a-t-il plus ? Faites nous part de votre témoignage ..."
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
