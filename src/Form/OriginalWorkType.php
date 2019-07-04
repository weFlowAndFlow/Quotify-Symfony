<?php 

// src/Form/OriginalWorkType.php
namespace App\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Security\Core\Security;

class OriginalWorkType extends AbstractType
{

    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();

        $builder
            ->add('authors', EntityType::class, [
                'class' => 'App\Entity\Author',
                'query_builder' => function(EntityRepository $repository) use ($user)  {
                    return $repository->createQueryBuilder('a')
                        ->andWhere('a.user = :user')
                        ->setParameter('user', $user)
                        ->orderBy('a.name', 'ASC');
                },
                'choice_label' => 'displayName',
                'multiple' => true,
                'expanded' => false
            ])
            ->add('title', TextType::class)
            ->add('year', IntegerType::class, [
                'required' => false
            ])
            ->add('save', SubmitType::class)
        ;
    }




}