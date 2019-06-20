<?php 

// src/Form/QuoteType.php
namespace App\Form;

use App\Repository\OriginalWorkRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\OriginalWork;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Security;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('text', TextareaType::class)
	        ->add('author', EntityType::class, array(
			        'class' => 'App\Entity\Author',
			        'choice_label' => 'displayName',
			        'multiple' => false,
			        'expanded' => false))
//	        ->add('originalWork', EntityType::class, array(
//			        'class' => 'App\Entity\OriginalWork',
//			        'choice_label' => 'title',
//			        'multiple' => false,
//			        'expanded' => false))
	        ->add('categories', EntityType::class, array(
			        'class' => 'App\Entity\Category',
			        'choice_label' => 'name',
			        'multiple' => true,
			        'expanded' => true))
	        ->add('notes', TextareaType::class, array('required' => false))
	        ->add('save', SubmitType::class)
        ;

       /* $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $dataa = $event->getData();
            $form = $event->getForm();

            $authorID = $quote->
        }*/


    }


}