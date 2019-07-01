<?php

// src/Form/QuoteType.php
namespace App\Form;

use App\Repository\OriginalWorkRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\OriginalWork;
use App\Entity\Author;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('text', TextareaType::class, [
                'empty_data' => ''
            ])
	        ->add('author', EntityType::class, array(
			        'class' => 'App\Entity\Author',
                    'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('a')->orderBy('a.name', 'ASC');
                },
			        'choice_label' => 'displayName',
			        'multiple' => false,
			        'expanded' => false,
                    'placeholder' => '',
                    'required' => false
            ))
	        ->add('categories', EntityType::class, array(
			        'class' => 'App\Entity\Category',
                    'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')->orderBy('c.name', 'ASC');
                },
			        'choice_label' => 'name',
			        'multiple' => true,
			        'expanded' => true,
                    'label_attr' => ['class' => 'checkbox-inline'],
                    'required' => false
            ))
	        ->add('notes', TextareaType::class, array('required' => false))
	        ->add('save', SubmitType::class)
        ;

        // Display original works from the selected author
        $formModifier = function (FormInterface $form, Author $author = null)
        {
            $works = null === $author ? [] : $author->getOriginalWorks();

            $form->add('originalWork', EntityType::class, [
                'class' => 'App\Entity\OriginalWork',
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('w')->orderBy('w.title', 'ASC');
                },
		        'choice_label' => 'title',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => '',
                'choices' => $works,
                'required' => false
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier)
            {
                $formModifier($event->getForm(), $event->getData()->getAuthor());
            }
        );

        $builder->get('author')->addEventListener(FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier)
        {
            $author = $event->getForm()->getData();
            $formModifier($event->getForm()->getParent(), $author);
        }
        );



    }


}