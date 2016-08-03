<?php

namespace AppBundle\Services\Booking\Mgmt\CreationStep\Steps;

use AppBundle\Form\BookingType;
use AppBundle\Form\SpaceType;
use AppBundle\Services\Booking\Mgmt\CreationStep\Step;
use Application\Sonata\MediaBundle\Entity\Media;

class Step1 extends Step
{

    public function process()
    {
        $space = $this->space;
        $booking = $this->booking;
        $request = $this->getRequest();

        $form = $this->createForm(BookingType::class,$booking);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $booking->setSpace($space);
            $em->persist($booking);
            $em->flush();
            return $this->redirectToRoute('app_user_booking_create',[
                'space' => $space->getId(),
                'booking' => $booking->getId(),
                'step' => 2
            ]);
        }

        return $this->render('AppBundle:User/Booking/Steps:detail.html.twig', array(
            'space'=>$space,
            'form'=>$form->createView()
        ));
    }

}