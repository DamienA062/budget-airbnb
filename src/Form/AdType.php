<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdType extends AbstractType
{
    /**
     * DRY function pour avoir le placeholder d'un champs
     *
     * @param string $placeholder
     * @return Array
     */
    private function getConfiguration($placeholder)
    {
        return ['attr' => ['placeholder' => $placeholder]];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre de l'annonce"))
            ->add('price', MoneyType::class, $this->getConfiguration("Prix du bien pour la nuit"))
            ->add('content', TextareaType::class, $this->getConfiguration("Description ..."))
            ->add('imageFile', FileType::class, $this->getConfiguration("Parcourir ..."))
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
