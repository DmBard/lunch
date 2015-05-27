<?php

namespace Ens\LunchBundle\Form;

use Ens\LunchBundle\Entity\Lunch;
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
//        $builder
//            ->add('day', 'choice', array('choices' => Lunch::getListDays()))
//            ->add('categories', 'choice', array('choices' => Lunch::getListCategories()))
//            ->add('count', 'integer')
//            ->add('active')
//            ->add('description', 'textarea')
//            ->add('created_at')
//            ->add('updated_at');
            $builder->add('file', 'file', array('label' => 'File', 'required' => false));
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
