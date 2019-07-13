<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Service\Pagination;
use App\Form\AdminBookingType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
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
     * @Route("/admin/booking/{page<\d+>?1}", name="admin_booking_index")
     */
    public function index($page = 1, Pagination $pagination)
    {
        $pagination->setEntityClass(Booking::class)
                    ->setCurrentPage($page);

        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Edite une réservation
     *
     * @Route("/admin/booking/{id}/edit", name="admin_booking_edit")
     * 
     * @param Booking $booking
     * @param Request $request
     * @return Response
     */
    public function edit(Booking $booking, Request $request)
    {
        $form = $this->createForm(AdminBookingType::class, $booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //Recalcul du montant après une édition
            //On lui donne 0 pour qu'il rentre dans le If de la fonction setAmount
            $booking->setAmount(0);
            $id = $booking->getId();

            $this->manager->flush();

            $this->addFlash('success', "La réservation n°$id a bien été modifiée");

            return $this->redirectToRoute('admin_booking_index');
        }

        return $this->render('admin/booking/edit.html.twig', [
            'form' => $form->createView(),
            'booking' => $booking
        ]);
    }

    /**
     * Supprime une réservation
     *
     * @Route("/admin/booking/{id}/delete", name="admin_booking_delete")
     * 
     * @param Booking $booking
     * @param Request $request
     * @return Response
     */
    public function delete(Booking $booking, Request $request)
    {
        //On vérifie si le token est valide pour pouvoir delete (id du token, token)
        if($this->isCsrfTokenValid('delete'.$booking->getId(), $request->get('_token')))
        {
            $id = $booking->getId();

            $this->manager->remove($booking);
            $this->manager->flush();

            $this->addFlash('success', 'La réservation n° '.$id.' a bien été supprimé.');
            
        }

        return $this->redirectToRoute('admin_booking_index');
    }
}
