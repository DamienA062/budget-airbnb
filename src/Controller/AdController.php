<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();

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
    public function create(Request $request, ObjectManager $manager)
    {
        $ad = new Ad();

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($ad);
            $manager->flush();

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
