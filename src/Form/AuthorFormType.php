<?php

namespace App\Form;

use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AuthorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'First name cannot be empty.'),
                    new Assert\Length(
                        min: 3,
                        minMessage: 'First name should have at least {{ limit }} characters. {{ value }} was provided.',
                        max: 255,
                        maxMessage: 'First name should not exceed {{ limit }} characters. {{ value }} was provided.'
                    ),
                ],
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Last name cannot be empty.'),
                    new Assert\Length(
                        min: 3,
                        minMessage: 'Last name should have at least {{ limit }} characters. {{ value }} was provided.',
                        max: 255,
                        maxMessage: 'Last name should not exceed {{ limit }} characters. {{ value }} was provided.'
                    ),
                ],
            ])
            ->add('middleName', TextType::class, [
                'required' => false,
                'trim' => true,
                'constraints' => [
                    new Assert\Length(
                        min: 3,
                        minMessage: 'Middle name should have at least {{ limit }} characters. {{ value }} was provided.',
                        max: 255,
                        maxMessage: 'Middle name should not exceed {{ limit }} characters. {{ value }} was provided.'
                    ),
                ],
            ])
            ->add('website', UrlType::class, [
                'required' => false,
                'invalid_message' => 'Website URL is not valid.',
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'trim' => true,
                'invalid_message' => 'Email is not valid.',
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Assert\Image(
                        maxSize: '8192k',
                        maxSizeMessage: 'The image is too large. The max size is {{ limit }}. {{ value }} was provided.',
                    ),
                ],
            ])
            ->add('removeImage', CheckboxType::class, [
                'required' => false,
                'invalid_message' => 'removeImage flag is not valid.',
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
