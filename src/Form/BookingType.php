<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends ApplicationType
{
    /** 
     * @var FrenchToDateTimeTransformer
     */
    private $transformer;

    public function __construct(FrenchToDateTimeTransformer $frenchToDateTimeTransformer)
    {
        $this->transformer = $frenchToDateTimeTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Date d'arrivée"
                ]
            ])
            ->add('endDate', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Date de départ"
                ]
            ])
            ->add('comment', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => "Si vous avez des informations à nous fournir ... (optionnel)"
                ]
            ])
        ;

        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
