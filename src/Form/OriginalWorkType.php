<?php 

// src/Form/OriginalWorkType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class OriginalWorkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('authors', EntityType::class, [
                'class' => 'App\Entity\Author',
                'choice_label' => 'displayName',
                'multiple' => true,
                'expanded' => false
            ])
            ->add('title', TextType::class)
            ->add('year', IntegerType::class)
            ->add('save', SubmitType::class)
        ;
    }




}