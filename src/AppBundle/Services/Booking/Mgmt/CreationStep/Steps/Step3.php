<?php

namespace AppBundle\Services\Booking\Mgmt\CreationStep\Steps;


use AppBundle\Services\Booking\Mgmt\CreationStep\Step;
use AppBundle\Form\SpaceType;
use Doctrine\Common\Util\Debug;
use AppBundle\Entity\Booking\Booking;

class Step3 extends Step
{

    public function process()
    {
        $space = $this->space;
        $booking = $this->booking;
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        //update price booking
        $price = $this->getPriceBooking($booking->getDateFrom()->format('Y-m-d'),$booking->getDateTo()->format('Y-m-d'),$booking->getSpace()->getPrice()->getDaily(),1);
        $price= round($price,1);
        //booking api
        $stripe = array(
            'secret_key'      => 'sk_test_clj9PCMa3jidJqgOTFl67d0n',
            'publishable_key' => 'pk_test_qBL4eoPC9zHxzA1kCXjm7kNZ'
        );
        //submit from step 2
        if($request->isMethod('post') && $request->get('fromStep2') !=null){
            if (!isset($_POST['stripeToken'])) throw new Exception("The Stripe Token was not generated correctly");

            $stripeToken = $request->get('stripeToken');
        }
        //submit from step to completed booking
        if($request->isMethod('post') && $request->get('fromStep3') !=null){
            \Stripe\Stripe::setApiKey($stripe['secret_key']);
            try {
                if (!isset($_POST['stripeToken'])) throw new Exception("The Stripe Token was not generated correctly");

                $charge = \Stripe\Charge::create(array(
                    'source'     => $_POST['stripeToken'],
                    'amount'   => $price*100,
                    'currency' => 'usd'
                ));
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if (!isset($error)) {
                //payment Success
                $booking->setStatus(Booking::STATUS_SUCCESS);
                $booking->setTotalPrice($price);
                $em->persist($booking);
                $em->flush();
                return $this->redirectToRoute('app_user_booking_create',[
                    'space' => $space->getId(),
                    'booking' => $booking->getId(),
                    'step' => 4
                ]);
            }
        }
        return $this->render('AppBundle:User/Booking/Steps:detail.html.twig', array(
            'space'=>$space,
            'booking'=>$booking,
            'stripeToken'=>$stripeToken,
            'price'=>$price
        ));
    }

}
