<?php

// src/Form/ContactType.php

namespace App\Form;

use App\Entity\Author;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;

class ContactType extends AbstractType
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

        $builder
            ->add('name', TextType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => true])
            ->add('object', TextType::class, ['required' => true])
            ->add('message', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'rows' => '8',
                ]
                ])
            ->add('send', SubmitType::class)
        ;

    }
}
