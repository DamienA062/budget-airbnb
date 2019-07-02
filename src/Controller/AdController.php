<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Form\AdEditType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller qui gère tout ce qui touche aux annonces
 * ->affichage, édition, création, suppression
 * 
 * @Route("/ads")
 */
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
     * Affiche toutes les annonces
     * 
     * @Route("/", name="ads_index")
     * 
     * @return Response
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
     * @Route("/new", name="ads_create")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $ad = new Ad();

        $author = $this->getUser();
        
        $form = $this->createForm(AdType::class, $ad);
        $ad->setAuthor($author);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            dump($ad);
            foreach($ad->getImages() as $image)
            {
                $image->setAd($ad);
                dump($image);
                $this->manager->persist($image); 
            }
            

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
     * @Route("/{slug}/{id}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Vous ne pouvez pas faire faire cela.")
     *
     * @return Response
     */
    public function edit(Ad $ad, Request $request)
    {
        $form = $this->createForm(AdEditType::class, $ad);

        $form->handleRequest($request);

        $test = $ad->getImages();

        if($form->isSubmitted() && $form->isValid())
        {
            foreach($ad->getImages() as $image)
            {
                $image->setAd($ad);

                $this->manager->persist($image); 
            }
            
            $this->manager->flush();

            $this->addFlash('success', 'Votre annonce a bien été mise à jour');

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug(),
                'id' => $ad->getId()
            ]);
        }

        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad,
            'images' => $test
        ]);
    }

    /**
     * Affiche une annonce en utilisant le param converter
     * Symfony va faire automatiquement le lien entre le slug et l'annonce
     *
     * @Route("/{slug}/{id}", name="ads_show")
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

    /**
     * Supprime un article
     * 
     * @Route("/{slug}/{id}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Vous ne pouvez pas faire cela.")
     *
     * @return Response
     */
    public function delete(Ad $ad, Request $request)
    {
        //On vérifie si le token est valide pour pouvoir delete (id du token, token)
        if($this->isCsrfTokenValid('delete'.$ad->getId(), $request->get('_token')))
        {
            $this->manager->remove($ad);
            $this->manager->flush();

            $this->addFlash('success', 'Votre annonce '.$ad->getTitle().' a bien été supprimée.');
        }
        
        return $this->redirectToRoute("ads_index");
    }
}
