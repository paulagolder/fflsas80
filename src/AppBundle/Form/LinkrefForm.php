<?php

// src/Forms/LinkrefFormType.php
namespace AppBundle\Form;


use  AppBundle\Entity\Linkref;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
#use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
#use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
#use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class LinkrefForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
            
        $builder->add('objecttype', TextType::class);
        $builder->add('objid', TextType::class);
        $builder->add('label', TextType::class);
        $builder->add('path', TextType::class);
        $builder->add('doctype', TextType::class);
        $builder->get('objecttype')->setDisabled(true);
        $builder->get('objid')->setDisabled(true);
        $builder->get('path')->setRequired(false);
        $builder->get('doctype')->setRequired(false);
                
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Linkref::class,
        ));
    }
}
