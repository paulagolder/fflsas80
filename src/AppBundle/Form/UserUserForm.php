<?php

// src/Forms/UserUserForm.php
namespace AppBundle\Form;

use AppBundle\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
class UserUserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class,['label' => '.username',  ]);
        $builder->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => '.password',),
                'second_options' => array('label' => 'repeat.password' ,),'empty_data' => 'Default value'));
        $builder->add('membership', TextType::class,['label' => '.membership']);
         $builder->add('locale', ChoiceType::class, [
           'choices'  => [
             'FR' => 'fr',
             'EN' => 'en',
           ],]);
        $builder->add('email', TextType::class,['label' => '.email']);
        $builder->get('plainPassword')->setRequired(false);
        $builder->get('membership')->setDisabled(true);
        $builder->get('username')->setDisabled(true);
        $builder->get('email')->setDisabled(true);
    }

    
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
