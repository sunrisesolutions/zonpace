<?php

namespace AppBundle\Services\Core;

use AppBundle\Entity\Booking\Booking;
use AppBundle\Entity\Space\Space;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Validator\Constraints\DateTime;

class ControllerService extends Controller
{

    //core---------------------------------
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RequestStack
     */
    public function getRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }

    public function pagingBuilder($request, $queryBuilder)
    {
        $limit = $this->container->getParameter('pagination_limit');
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $page = $request->get('page', 1);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($page);
        return $pagerfanta;
    }

    public function getLocationRatingSpace($space)
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:Booking\Booking')->getLocationRatingSpace($space);
    }

    public function getCommunicationRatingSpace($space)
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:Booking\Booking')->getCommunicationRatingSpace($space);
    }

    public function getRatingSpace($space)
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:Booking\Booking')->getRatingSpace($space);
    }

    public function getTotalReviewSpace($space)
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:Booking\Booking')->getTotalReviewSpace($space);
    }

    public function getTotalEarningSpace($space)
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:Booking\Booking')->getTotalEarningSpace($space);
    }

    public function getTotalBookingSpace($space)
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:Booking\Booking')->getTotalBookingSpace($space);
    }

    /*
     * format :Y-m-d
     */
    public function getListDate($from, $to)
    {
        $begin = new \DateTime($from);
        $end = new \DateTime($to);
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($begin, $interval, $end);

        $listDate = [];
        foreach ($daterange as $date) {
            $listDate[$date->format('Y')][$date->format('m')][$date->format('Y-m-d')] = $date->format('Y-m-d');
        }
        return $listDate;
    }

    public function generateDataBookings($bookings)
    {
        $data = [];
        foreach ($bookings as $booking) {
            if (new \DateTime() < $booking->getDateFrom()) {
                $data['incomming'][$booking->getId()] = $this->getListDate($booking->getDateFrom()->format('Y-m-d'), $booking->getDateTo()->format('Y-m-d'));
            } else {
                $data['passed'][$booking->getId()] = $this->getListDate($booking->getDateFrom()->format('Y-m-d'), $booking->getDateTo()->format('Y-m-d'));
            }
        }
        //for pass to client {} instead []
        $data = count($data) == 0 ? new \stdClass() : $data;
        return json_encode($data);
    }

    public function getImageSpace(Space $space, $width, $height)
    {
        $googleApiKey = $this->getParameter('google_api_key');
        $shape = json_decode($space->getShape());
        if (isset($shape[0])) {
            $lat = 0;
            $lng = 0;
            $listPoint = [];
            $dataShape = $shape[0]->geometry[0];
            foreach ($dataShape as $item) {
                $listPoint[] = $item[0] . ',' . $item[1];
                $lat += $item[0];
                $lng += $item[1];
            }
            //add more the first point to completed the shape
            $listPoint[] = $dataShape[0][0] . ',' . $dataShape[0][1];
            $listPoint = implode('|', $listPoint);
            //this is avg center of map
            $lat = $lat / count($dataShape);
            $lng = $lng / count($dataShape);
            $center = $lat . ',' . $lng;
            //width height
            $widthHeight = $width . 'x' . $height;
            $url = 'https://maps.googleapis.com/maps/api/staticmap?center=' . $center . '&zoom=17&size=' . $widthHeight . '&maptype=roadmap&path=color:red|fillcolor:red|weight:0|' . $listPoint . '&key=' . $googleApiKey;
            $html = '<img src="' . $url . '">';
            return $html;
        } else {
            return '';
            //implement after
        }
    }
    public function getStatusBooking(Booking $booking){
        if($booking->getStatus() == Booking::STATUS_PENDING){
            return 'Pending';
        }
        if($booking->getStatus() == Booking::STATUS_CANCELLED){
            return 'Cancelled';
        }
        if($booking->getStatus() == Booking::STATUS_SUCCESS){
            $toDay = new \DateTime();
            if(($booking->getDateFrom()->getTimestamp() <= $toDay->getTimestamp() && $toDay->getTimestamp() <= $booking->getDateTo()->getTimestamp()) || $toDay->getTimestamp() < $booking->getDateFrom()->getTimestamp()){
                Return 'Active';
            }
            if( $toDay->getTimestamp() > $booking->getDateTo()->getTimestamp()){
                Return 'Completed';
            }
        }
    }


}
