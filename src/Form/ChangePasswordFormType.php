<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('password', PasswordType::class, [
            'required' => true,
            'invalid_message' => 'Password is not valid.',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Password is not specified.',
                ]),
                new Assert\Length([
                    'min' => 6,
                    'minMessage' => 'Password should be at least {{ limit }} characters.',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
