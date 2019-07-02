<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdEditType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre de l'annonce"))
            ->add('price', MoneyType::class, $this->getConfiguration("Prix du bien pour la nuit"))
            ->add('content', TextareaType::class, [
                'attr' => [
                    'placeholder' => "Description détaillée de votre bien ...",
                    'class' => "ta-ad-form"
                ]
            ])
            ->add('rooms', IntegerType::class, $this->getConfiguration("Nombre de chambres"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
            'translation_domain' => 'forms'
        ]);
    }
}
