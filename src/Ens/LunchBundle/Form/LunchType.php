<?php

namespace Ens\LunchBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LunchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type')
            ->add('day_of_week')
            ->add('count')
            ->add('description')
            ->add('created_at')
            ->add('updated_at')
            ->add('category', 'entity', array(
                'label' => 'Вид блюда',
                'required' => true,
                'class' => 'Ens\\LunchBundle\\Entity\\Category'))
            ->add('day', 'entity', array(
                'label' => 'День Недели',
                'required' => true,
//                'group_by' => false,
                'class' => 'Ens\\LunchBundle\\Entity\\Day'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ens\LunchBundle\Entity\Lunch'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ens_lunchbundle_lunch';
    }
}
