<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', ['label' => 'User Name'])
            ->add('password', 'password', ['label' => 'Password'])
            ->add('confirm', 'password', ['mapped' => false, 'label' => 'Re-type password'])
            ->add('homepage', 'text', ['label' => 'Homepage'])
            ->add('save', 'submit', ['label' => 'Register']);
    }

    public function getName()
    {
        return 'registration';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'EnsLunchBundle\Entity\Person',
            ]
        );
    }

}