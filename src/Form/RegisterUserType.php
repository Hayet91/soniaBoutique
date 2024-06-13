<?php

namespace App\Form;

use App\Entity\User;
use PhpParser\Node\Stmt\Label;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [ 
                'label' => "Votre adresse email",
                'attr'  => [
                    'placeholder' => "indiquez votre adresse email"
                ]
                ])
            // ->add('roles')
           
            ->add('password',RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identique.',
                'label' => 'Votre mot de passe',
                'required' => true,
                'first_options' => ['label' => 'Mot de passe', 
                'attr' => [ 'placeholder' => 'Merci de saisir votre mot de passe']],
                'second_options' => ['label' => 'confirmez votre mot de passe', 
                'attr' =>['placeholder '=> 'Merci de confirmer votre mot de passe.'],
                'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Length([
                    'min' => 4,
                    'minMessage' => 'Votre mot de passe doit comporter au moins 4 caractères',
                    'max' => 30, 
                ]),]
            ]])

            ->add('firstname', TextType::class,[
                'label' => "Votre prénom",
                'constraints' => [
                new Length([
                    'min' => 2,
                    'minMessage' => 'Votre prénom doit comporter au moins 2 caractères',
                    'max' => 30, 
                ])
                ],
                'attr'  => [
                    'placeholder' => "indiquez votre prénom"
                ]

                
            ])
            ->add('Lastname', TextType::class, [
                'label' => "votre nom",
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit comporter au moins 2 caractères',
                        'max' => 30, 
                    ])
                    ],
                'attr'  => [
                    'placeholder' => "indiquez votre nom"
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Valider",
                'attr'  => [
                    'class' => "btn btn-success"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => 'email'
                ])
            ],
            'data_class' => User::class,
        ]);
    }
}
