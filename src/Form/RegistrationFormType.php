<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom',TextType::class,[
            'attr' => ['class' => 'form-control' , 'placeholder' => 'nom',
                ]
        ])
        ->add('prenom',TextType::class,[
            'attr' => ['class' => 'form-control' , 'placeholder' => 'prenom',
                ]
        ])
        ->add('cin',TextType::class,[
            'attr' => ['class' => 'form-control' , 'placeholder' => 'cin',
                ]
        ])
        ->add('photo',TextType::class,[
            'attr' => ['class' => 'form-control' , 'placeholder' => 'photo',
                ]
        ])
        ->add('email',EmailType::class,[
            'attr' => ['class' => 'form-control' , 'placeholder' => 'email',
                ]
        ])
         
            ->add('plainPassword', PasswordType::class, [

                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password','placeholder' => 'Password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}