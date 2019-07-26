<?php

// src/Form/AuthorType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'required' => true,
                'first_options' => ['label' => 'Email'],
                'second_options' => ['label' => 'Repeat email'],
            ])
            ->add('firstname', TextType::class, array('required' => true))
            ->add('lastname', TextType::class, array('required' => true))
//            ->add('password', PasswordType::class, array('required' => true))
            ->add('save', SubmitType::class);
    }
}
