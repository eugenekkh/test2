<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;


class FeedbackType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Имя',
                'constraints' => new NotBlank(),
            ))
            ->add('email', EmailType::class, array(
                'label' => 'Email',
                'constraints' => array(
                    new NotBlank(),
                    new Email(),
                )
            ))
            ->add('message', TextareaType::class, array(
                'label' => 'Сообщение',
                'constraints' => new NotBlank(),
            ))
            ->add('image', FileType::class, array(
                'required' => false,
                'label' => 'Изображение',
                'constraints' => array(
                    new Image()
                ),
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Model\Feedback',
        ));
    }
}
