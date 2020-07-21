<?php

// src/Forms/PersonFormType.php
namespace AppBundle\Form;

use  AppBundle\Entity\person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class PersonForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
            
        $builder ->add('surname', TextType::class ,['label' => '.surname',]);
        $builder ->add('forename', TextType::class,['label' => '.forename',]);
        $builder ->add('alias', TextType::class,['required' => false,'label' => '.alias',]);
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Person::class,
        ));
    }
}
