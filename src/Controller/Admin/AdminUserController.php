<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Service\Pagination;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /** 
     * @var ObjectManager
     */
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }
    
    /**
     * @Route("/admin/user/{page<\d+>?1}", name="admin_user_index")
     */
    public function index($page = 1, Pagination $pagination)
    {
        $pagination->setEntityClass(User::class)
                    ->setLimit(10)
                    ->setCurrentPage($page);

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Formulaire d'édition d'un profil utilisateur
     *
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     * 
     * @param User $user
     * @return Response
     */
    public function edit(User $user, Request $request)
    {
        $form = $this->createForm(AdminUserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->manager->flush();

            $this->addFlash('success', "L'utilisateur n° {$user->getId()} a bien été modifié.");

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Affiche toutes les réservations d'un client
     *
     * @Route("/admin/user/{id}/booking", name="admin_user_bookings")
     * 
     * @param User $user
     * @return Response
     */
    public function showBookings(User $user)
    {
        return $this->render('admin/user/booking.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Supprime un utilisateur
     * 
     * @Route("/admin/user/{id}/delete", name="admin_user_delete")
     *
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function delete(User $user, Request $request)
    {
        //On vérifie si le token est valide pour pouvoir delete (id du token, token)
        if($this->isCsrfTokenValid('delete'.$user->getId(), $request->get('_token')))
        {
            $id = $user->getId();
            $fullName = $user->getFullName();

            if(count($user->getBookings()) == 0)
            {
                $this->manager->remove($user);
                $this->manager->flush();

                $this->addFlash('success', 'L\'utilisateur <strong>n° '.$id.' - '.$fullName.'</strong> a bien été supprimé.');
            }else{
                $this->addFlash('warning', 'Vous ne pouvez pas supprimer l\'utilisateur <strong>n° '.$id.' - '.$fullName.'</strong> car il possède des réservations.');
            }
            
            
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
