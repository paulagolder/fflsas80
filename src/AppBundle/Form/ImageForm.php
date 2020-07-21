<?php

// src/Forms/ImageForm.php

namespace AppBundle\Form;

use  AppBundle\Entity\Image;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ImageForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('name', TextType::class,['label' => '.imagename',]);
        $builder ->add('path', TextType::class,['label' => '.path',]);
        $builder ->add('format', TextType::class,['label' => '.format',]);
        $builder->add('access', ChoiceType::class, ['label' => '.access',
        'choices'  => ['Public' => 0,'Admin' => 1, 'Private' => 2, ],]);
        $builder ->add('copyright', TextType::class,['label' => '.copyright',]);
        $builder->add('imagefile', FileType::class, array('label' => 'image.file','required'=>false,  'data_class' => null));
        $builder->get('path')->setRequired(false);
        $builder->get('copyright')->setRequired(false);
        $builder->get('format')->setRequired(false);
        $builder->get('access')->setRequired(false);
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Image::class,
        ));
    }
}
