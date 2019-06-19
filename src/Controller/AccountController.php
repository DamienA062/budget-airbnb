<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /** 
     * @var UserPasswordEncoderInterface
    */
    private $encoder;

    /** 
     * @var ObjectManager
     */
    private $manager;

    public function __construct(UserPasswordEncoderInterface $encoder, ObjectManager $manager)
    {
        $this->encoder = $encoder;
        $this->manager = $manager;
    }

    /**
     * Affichage + management des erreurs du form connexion
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername =  $authenticationUtils->getLastUsername();
        
        return $this->render('account/login.html.twig', [
            'error' => $error,
            'lastUsername' => $lastUsername
        ]);
    }

    /**
     * Affichage form inscription, vérifie le form et crypte le pwd
     * 
     * @Route("/register", name="account_register")
     *
     * @return Response
     */
    public function register(Request $request)
    {
        $user = new User();
        
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //On crypte le pwd du user pendant l'inscription avec encodePassword(entité, password à crypter)
            $user->setHash($this->encoder->encodePassword($user, $user->getHash()));

            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', "Votre compte a bien été créé");

            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/signup.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
