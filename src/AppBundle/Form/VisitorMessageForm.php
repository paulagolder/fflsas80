<?php

// src/Forms/userMessageForm.php
namespace AppBundle\Form;

use  AppBundle\Entity\message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class VisitorMessageForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder->add('fromname', TextType::class,['label' => '.fromname']);
         $builder->add('fromemail', TextType::class,['label' => '.fromemail']);
         $builder->add('toname', TextType::class,['label' => '.toname']);
         $builder->add('toemail', HiddenType::class,['label' => '.toemail']);
         $builder->add('subject', TextType::class,['label' => '.subject']);
         $builder->add('body', TextareaType::class,['label' => '.body']);
         $builder->get('toname')->setDisabled(true);
         $builder->get('toemail')->setDisabled(true);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Message::class,
        ));
    }
}
