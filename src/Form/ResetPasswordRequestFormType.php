<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'invalid_message' => 'Email is not valid.',
                'empty_data' => null,
                'trim' => true,
                'constraints' => [new Assert\NotBlank(message: 'Email is not specified.')],
            ])
            ->add('link', UrlType::class, [
                'required' => true,
                'invalid_message' => 'Link is not valid.',
                'empty_data' => null,
                'trim' => true,
                'constraints' => [new Assert\NotBlank(message: 'Link is not specified.')],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
