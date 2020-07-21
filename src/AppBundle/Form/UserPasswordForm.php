<?php

// src/Forms/UserPasswordForm.php
namespace AppBundle\Form;

use  AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserPasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class,array('label' => '.username'));
        $builder->add('email', TextType::class,['label' => '.email',]);
        $builder->add('plainPassword', RepeatedType::class, array(
                'type' => TextType::class,
                'first_options'  => array('label' => '.password'),
                'second_options' => array('label' => 'repeat.password'),));
        $builder->add('interet', TextType::class,['label' => '.interet',]);
        #$builder->add('newregistrationcode', NumberType::class,['label' => '.newregistrationcode',]);
        $builder->get('username')-> setDisabled(true);
        $builder->get('email')-> setDisabled(true);
        $builder->get('plainPassword')->setRequired(false);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
