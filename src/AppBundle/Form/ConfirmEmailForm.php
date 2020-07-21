<?php

// src/Forms/ConfirmEmailForm.php
namespace AppBundle\Form;

use  AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class ConfirmEmailForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class,['label' => '.username',]);
        $builder->add('email', TextType::class,['label' => '.email',]);
        $builder->add('newregistrationcode', IntegerType::class,['label' => '.newregistrationcode',]);
        $builder->add('interet', TextType::class,['label' => '.interet',]);
        $builder->get('username')-> setDisabled(true);
        $builder->get('email')-> setDisabled(true);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
