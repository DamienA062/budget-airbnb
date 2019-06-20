<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'attr' => [
                    'class' => 'read-only'
                ],
                'disabled' => true
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'class' => 'read-only'
                ],
                'disabled' => true
            ])
            ->add('email')
            ->add('profilPicture')
            ->add('introduction')
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 't-a-upload'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
