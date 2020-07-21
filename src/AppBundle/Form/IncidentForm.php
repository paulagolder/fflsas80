<?php

// src/Forms/PersonFormType.php
namespace AppBundle\Form;

//use  AppBundle\Entity\person;
//use  AppBundle\Entity\event;
use  AppBundle\Entity\Incident;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
#use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
#use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
#use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class IncidentForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
            
        $builder->add('personid', TextType::class);
        $builder->add('eventid', TextType::class);
        $builder->add('itypeid', TextType::class);
        $builder->add('locid', TextType::class);
        $builder->add('sequence', TextType::class);   
        $builder->add('namerecorded', TextType::class);
        $builder->add('rank', TextType::class);
        $builder->add('role', TextType::class);   
        $builder->add('sdate', TextType::class); 
        $builder->add('edate', TextType::class);
        $builder->add('comment', TextType::class);
        $builder->get('sdate')->setRequired(false);
        $builder->get('edate')->setRequired(false);
        $builder->get('rank')->setRequired(false);
        $builder->get('role')->setRequired(false);
        $builder->get('locid')->setRequired(false);
        $builder->get('sequence')->setRequired(false);
        $builder->get('namerecorded')->setRequired(false);
        $builder->get('comment')->setRequired(false);
        $builder->get('personid')->setDisabled(true);
        $builder->get('eventid')->setDisabled(true);
        
         
    }

    
    
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Incident::class,
        ));
    }
}
