<?php

namespace App\Notification;

use App\Entity\Contact;
use Twig\Environment;

class ContactNotification
{
    /** 
     * @var \Swift_Mailer
    */
    private $mailer;

    /** 
     * @var Environment
    */
    private $renderer;

    //Pour générer un mail au format HTML on utilise Environment
    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }
    
    public function notify(Contact $contact)
    {
        //On génère un message (sujet de l'email, nom du bien concerné)
        $message = (new \Swift_Message('symfonyBNB'))
            ->setFrom('noreply@symfonyBNB.fr')
            ->setTo('contact@symfonyBNB.fr')
            ->setReplyTo($contact->getEmail())
            ->setBody($this->renderer->render('emails/mail_template.html.twig', [
                'contact' => $contact
            ]), 'text/html');

        $this->mailer->send($message);
    }
}