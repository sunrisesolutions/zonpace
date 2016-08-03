<?php
namespace AppBundle\Form;

use AppBundle\Entity\Booking\Booking;
use AppBundle\Entity\Space\Location;
use AppBundle\Entity\Space\State;
use AppBundle\Form\Transformer\DateTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\DependencyInjection\Container;

class BookingType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateFrom', DateType::class, ['format' => 'MM/dd/yyyy', 'widget' => 'single_text'])
            ->add('dateTo', DateType::class, ['format' => 'MM/dd/yyyy', 'widget' => 'single_text'])
            ->add('bookingType', ChoiceType::class,[
                'choices'=>[
                    'Daily' =>Booking::BOOKING_TYPE_DAILY,
                    'Weekly' =>Booking::BOOKING_TYPE_WEEKLY,
                    'Monthly' =>Booking::BOOKING_TYPE_MONTHLY,
                ],
                'expanded'=>true,
                'multiple'=>false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Booking\Booking',
            'dateBooking'=>null
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'app_booking';
    }
}