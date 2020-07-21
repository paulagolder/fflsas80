<?php

// src/Forms/TextForm.php
namespace AppBundle\Form;

use  AppBundle\Entity\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use FOS\CKEditorBundle\Form\Type\CKEditorType;

class TextForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        #$builder    ->add('title', TextareaType::class);   
       # $builder->add('comment', TextareaType::class);
       $builder->add('comment', CKEditorType::class, array( 'config'=>array('config_name'=> 'my_config',),));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Text::class,
        ));
    }
}
