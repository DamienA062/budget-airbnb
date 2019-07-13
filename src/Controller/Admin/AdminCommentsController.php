<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Service\Pagination;
use App\Form\AdminCommentType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentsController extends AbstractController
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
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_index")
     */
    public function index($page = 1, Pagination $pagination)
    {
        $pagination->setEntityClass(Comment::class)
                    ->setLimit(5)
                    ->setCurrentPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Formulaire d'édition d'un commentaire
     *
     * @Route("/admin/comments/{id}/edit", name="admin_comments_edit")
     * 
     * @param Comment $comment
     * @return Response
     */
    public function edit(Comment $comment, Request $request)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->manager->flush();

            $this->addFlash('success', "Le commentaire n° {$comment->getId()} a bien été modifié.");

            return $this->redirectToRoute('admin_comments_index');
        }

        return $this->render('admin/comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    /**
     * Supprime un commentaire
     * 
     * @Route("/admin/comments/{id}/delete", name="admin_comments_delete")
     *
     * @param Comment $comment
     * @param Request $request
     * @return Response
     */
    public function delete(Comment $comment, Request $request)
    {
        //On vérifie si le token est valide pour pouvoir delete (id du token, token)
        if($this->isCsrfTokenValid('delete'.$comment->getId(), $request->get('_token')))
        {
            $id = $comment->getId();
            
            $this->manager->remove($comment);
            $this->manager->flush();

            $this->addFlash('success', 'Le commentaire n° '.$id.' a bien été supprimé.');
            
        }

        return $this->redirectToRoute('admin_comments_index');
    }
}
