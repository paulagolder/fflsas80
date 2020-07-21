<?php

// src/Forms/ContentFormType.php
namespace AppBundle\Form;

use AppBundle\Entity\Content;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;




class ContentForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder    ->add('subjectid', TextType::class, array('attr' => array('style' => 'width: 40px'),));  
       $builder    ->add('language', ChoiceType::class, array(
        'choices'  => array(
         'fr' => 'fr',
         'en' => 'en',
        '*' => '*', ),
            ));
       $builder->add('tags', CheckboxType::class, [
    'label'    => 'Include in news?',
    'required' => false,
]);
        $builder->add('title', TextType::class, array('attr' => array('style' => 'width: 400px'),));
        #$builder->add('text', CKEditorType::class, array( 'config'=>array('config_name'=> 'my_config',),));
        #   $builder->add('text', CKEditorType::class, array( 'config'=>array('config_name'=> 'my_config',),'attr' => array('style' => 'width: 400px ;height:400px;'),));
       $builder->add('text', TextareaType::class, array('attr' => array('style' => 'width: 400px ;height:400px;'),));
   }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Content::class,
        ));
    }
    
    
    
}
