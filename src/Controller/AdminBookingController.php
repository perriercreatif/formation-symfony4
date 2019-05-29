<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use App\Service\PaginationService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings{page<\d+>?1}", name="admin_booking_index")
     */
    public function index(BookingRepository $repo, $page, PaginationService $pagination){
        $pagination->setEntityClass(Booking::class)
            ->setPage($page);

        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/bookings/{id}/edit" , name="admin_booking_edit")
     * @param Booking $booking
     * @return Response
     */
    public function edit(Booking $booking, ObjectManager $manager, Request $request){
        $form = $this->createForm(AdminBookingType::class, $booking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setAmount(0);
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation n°<strong>{$booking->getId()}</strong> a bien été enregistrées !"
            );

            return $this->redirectToRoute("admin_booking_index");
        }
        return $this->render('admin/booking/edit.html.twig', [
            'form' => $form->createView(),
            'booking' => $booking
        ]);
    }

    /**
     * @Route("/admin/bookings/{id}/delete", name="admin_booking_delete")
     * @param Booking $comment
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Booking $booking, ObjectManager $manager){
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le commentaire <strong>{$booking->getId()}</strong> a bien été supprimée !"
        );

        return $this->redirectToRoute("admin_booking_index");
    }
}
