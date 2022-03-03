<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => 'email',
                    ]
            ])
            ->add('password',PasswordType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => 'Password',
                    ]
            ])
            ->add('cin',TextType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => 'cin',
                    ]
            ])
            ->add('nom',TextType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => 'Nom',
                    ]
            ])
            ->add('prenom',TextType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => 'prenom',
                    ]
            ])
            ->add('photo',FileType::class,[
                'data_class' => null,
                'attr' => ['class' => 'form-control' 
                    ]
            ])
            ->add('isVerified')
            ->add('carte')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
