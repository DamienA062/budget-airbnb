<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Controller qui gère tout ce qui touche aux comptes des utilisateurs 
 * ->login, inscription, édition du profil, édition du mot de passe
 */
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

        return $this->render('account/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Affichage et traitement du form qui édite le profil
     * 
     * @Route("/account/profile", name="account_profile")
     *
     * @return Response
     */
    public function profile(Request $request)
    {
        //Récupération de l'utilisateur en cours
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);
        
        $form->handleRequest($request);
        

        if($form->isSubmitted() && $form->isValid())
        {
            $this->manager->flush();

            $this->addFlash('success', 'Vos modifications ont bien été prises en compte');

            return $this->redirectToRoute('account_profile');
        }

        return $this->render('account/profile.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * Affichage et traitement du form qui édite le mot de passe
     *
     * @Route("/account/password", name="account_password")
     * 
     * @return Response
     */
    public function updatePassword(Request $request)
    {
        $passwordUpdate = new PasswordUpdate();

        //Récupération de l'utilisateur en cours
        $user = $this->getUser();

        $pwdForm = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $pwdForm->handleRequest($request);

        if($pwdForm->isSubmitted() && $pwdForm->isValid())
        {
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash()))
            {
                //Aucun assert pour vérifier que l'ancien pwd soit le même que celui entré
                //Du coup on personnalise l'erreur
                $pwdForm->get('oldPassword')->addError(new FormError("Mot de passe incorrect."));
            }else{
                //On set le nouveau pwd en le hashant
                $user->setHash($this->encoder->encodePassword($user, $passwordUpdate->getNewPassword()));

                $this->manager->persist($user);
                $this->manager->flush();

                $this->addFlash('success', 'Votre mot de passe a bien été modifié');

                return $this->redirectToRoute('homepage');
            }
        }
        return $this->render('account/password.html.twig', [
            'pwdForm' => $pwdForm->createView()
        ]);
    }
    
    /**
     * Affiche notre propre profil
     * 
     * @Route("/account", name="account_index")
     *
     * @return Response
     */
    public function showOwnProfil()
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser() //récupère la session en cours
        ]);
    }
}
