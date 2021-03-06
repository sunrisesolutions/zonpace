<?php

namespace AppBundle\Services\Space\Mgmt\CreationStep\Steps;


use AppBundle\Services\Space\Mgmt\CreationStep\Step;
use AppBundle\Form\SpaceType;
use Doctrine\Common\Util\Debug;

class Step2 extends Step
{

    public function process()
    {
        $form = $this->createForm(SpaceType::class,$this->space,['dateBooking'=>$this->space->getDateBooking(),'step'=>2]);
        $form->handleRequest($this->getRequest());
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($this->space);
            $entityManager->flush();
            return $this->redirectToRoute('app_user_space_create',['space' => $this->space->getId(),'step' => 3]);
        }else{
//            Debug::dump($form->getErrors());
        }
        return $this->render('AppBundle:User/Space/Steps:step2.html.twig', array(
            'space'=>$this->space,
            'form' => $form->createView(),
        ));
    }

}
