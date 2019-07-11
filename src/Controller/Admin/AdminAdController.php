<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AdRepository;
use App\Entity\Ad;
use App\Form\AdEditType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

class AdminAdController extends AbstractController
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
     * @Route("/admin/ads", name="admin_ads_index")
     */
    public function index(AdRepository $repo)
    {
        return $this->render('admin/ad/index.html.twig', [
            'ads' => $repo->findAll(),
        ]);
    }

    /**
     * Affiche le formulaire d'édition de l'admin
     *
     * @Route("/admin/ads/{slug}/{id}/edit", name="admin_ads_edit")
     * 
     * @param Ad $ad
     * @return Response
     */
    public function edit(Ad $ad, Request $request)
    {
        $form = $this->createForm(AdEditType::class, $ad);

        $form->handleRequest($request);

        $images = $ad->getImages();

        if($form->isSubmitted() && $form->isValid())
        {
            $this->manager->persist($ad);
            $this->manager->flush();

            $this->addFlash('success', "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée.");
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'images' => $images,
            'form' => $form->createView()
        ]);
    }

    /**
     * Supprime une annonce dans l'admin
     *
     * @Route("/admin/ads/{slug}/{id}/delete", name="admin_ads_delete")
     * 
     * @param Ad $ad
     * @param Request $request
     * @return Response
     */
    public function delete(Ad $ad, Request $request)
    {
        //On vérifie si le token est valide pour pouvoir delete (id du token, token)
        if($this->isCsrfTokenValid('delete'.$ad->getId(), $request->get('_token')))
        {
            //Si l'annonce possède des réservations, alors on empêche la suppression
            if(count($ad->getBookings()) > 0)
            {
                $this->addFlash('warning', 'L\'annonce '.$ad->getTitle().' ne peut pas être supprimée. Elle possède des réservations.');
            }else{
                $this->manager->remove($ad);
                $this->manager->flush();
    
                $this->addFlash('success', 'L\'annonce '.$ad->getTitle().' a bien été supprimée.');
            }
        }

        return $this->redirectToRoute('admin_ads_index');
    }
}
