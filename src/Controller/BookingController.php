<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
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
     * @Route("/ads/{slug}/{id}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function book(Ad $ad, Request $request)
    {
        //fonction qui permet de récup l'user qui est co
        $user = $this->getUser();

        $booking = new Booking();

        $booking->setBooker($user)
                ->setAd($ad);

        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
                if(!$booking->isBookableDates())
                {
                    $this->addFlash('warning', 'Les dates choisies ont déjà été prises !');
                }else{
                    $this->manager->persist($booking);
                    
                    $this->manager->flush();
        
                    return $this->redirectToRoute('booking_show', [
                        'id' => $booking->getId(),
                        'withAlert' => true
                        ]);
                }
        }
        
        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Affiche la page d'une réservation
     * 
     * @Route("/booking/{id}", name="booking_show")
     *
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking)
    {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking
        ]);
    }
}
