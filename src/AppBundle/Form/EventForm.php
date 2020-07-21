<?php

// src/Forms/PersonFormType.php
namespace AppBundle\Form;

use  AppBundle\Entity\event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
#use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
#use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
#use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class EventForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
            
        $builder   ->add('label', TextType::class,['label' => '.label',]);
        $builder   ->add('parent', TextType::class,['label' => '.parent',]);
        $builder   ->add('locid', TextType::class,['label' => '.locid',]);
        $builder   ->add('startdate', TextType::class,['label' => '.startdate',]);
        $builder   ->add('enddate', TextType::class,['label' => '.enddate',]);
        $builder->get('startdate')->setRequired(false);
        $builder->get('enddate')->setRequired(false);
        $builder->get('locid')->setRequired(false);
    
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Event::class,
        ));
    }
}
