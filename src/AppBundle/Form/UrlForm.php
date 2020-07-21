<?php

// src/Forms/LinkrefFormType.php
namespace AppBundle\Form;


use  AppBundle\Entity\Url;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
#use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
#use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
#use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UrlForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
            
       
        $builder->add('id', TextType::class,['label' => '.id']);
        $builder->add('url', TextType::class,['empty_data' => '', 'attr' => array('style' => 'width: 40em'),'label' => '.url']);
        $builder->add('label', TextType::class,['empty_data' => '', 'attr' => array('style' => 'width: 40em') ,'label' => '.label' ]);
        $builder->add('tags', TextType::class,['label' => '.tags']);

        $builder->get('tags')->setRequired(false);
        $builder->get('id')->setDisabled(true);
       
                
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Url::class,
        ));
    }
}
