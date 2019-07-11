<?php

namespace Damien\RecaptchaBundle\Type;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Damien\RecaptchaBundle\Constraints\Recaptcha;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RecaptchaSubmitType extends AbstractType
{
    /**
     * @var string
     */
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //ce champs n'est pas relié à une information dans notre objet qui représente les données
        $resolver->setDefaults([
            'mapped' => false,
            'constraints' => new Recaptcha()
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        //redéfinition de la var label à false pour pas afficher le label
        $view->vars['label'] = false;

        $view->vars['key'] = $this->key;

        //on créé un nouvelle var button qui récupère le label qui est passé en option
        $view->vars['button'] = $options['label'];
    }
    
    //Donne un préfix à notre block qui sera utilisé par les vues
    public function getBlockPrefix()
    {
        return 'recaptcha_submit';
    }

    //Apparence par défaut du champs
    public function getParent()
    {
        return TextType::class;
    }
}