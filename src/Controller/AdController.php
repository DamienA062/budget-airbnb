<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ContructAW\ContructAWController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /** 
     * @var AdRepository
     */
    private $repository;

    /** 
     * @var ObjectManager
     */
    private $manager;

    public function __construct(ObjectManager $manager, AdRepository $repo)
    {
        $this->repository = $repo;
        $this->manager = $manager;
    }
    
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index()
    {
        $ads = $this->repository->orderByDesc();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }
    
    /**
     * Crée une annonce via formulaire et la save dans la DB
     * 
     * @Route("/ads/new", name="ads_create")
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $ad = new Ad();

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /*foreach($ad->getImages() as $image)
            {
                $image->setAd($ad);

                $this->manager->persist($image); 
            }*/

            $this->manager->persist($ad);
            $this->manager->flush();

            $this->addFlash('success', 'Votre annonce a bien été ajoutée');

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug(),
                'id' => $ad->getId()
            ]);
        }
        return $this->render('ad/new.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * Affiche le formulaire d'édition
     * 
     * @Route("/ads/{slug}/{id}/edit", name="ads_edit")
     *
     * @return Response
     */
    public function edit(Ad $ad, Request $request)
    {
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /*foreach($ad->getImages() as $image)
            {
                $image->setAd($ad);

                $this->manager->persist($image); 
            }*/
            $this->addFlash('success', 'Votre annonce a bien été mise à jour');

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug(),
                'id' => $ad->getId()
            ]);
        }

        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }

    /**
     * Affiche une annonce en utilisant le param converter
     * Symfony va faire automatiquement le lien entre le slug et l'annonce
     *
     * @Route("/ads/{slug}/{id}", name="ads_show")
     * 
     * @return Response
     */
    public function show(Ad $ad)
    {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
            'slug' => $ad->getSlug(),
            'id' => $ad->getId()
        ]);
    }
}
