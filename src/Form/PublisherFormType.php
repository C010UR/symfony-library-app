<?php

namespace App\Form;

use App\Entity\Publisher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PublisherFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Publisher name cannot be empty.'),
                    new Assert\Length(
                        min: 3,
                        minMessage: 'Publisher name should have at least {{ limit }} characters. {{ value }} was provided.',
                        max: 255,
                        maxMessage: 'Publisher name should not exceed {{ limit }} characters. {{ value }} was provided.'
                    ),
                ],
            ])
            ->add('address', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Address cannot be empty.'),
                    new Assert\Length(
                        min: 3,
                        minMessage: 'Address should have at least {{ limit }} characters. {{ value }} was provided.',
                        max: 255,
                        maxMessage: 'Address should not exceed {{ limit }} characters. {{ value }} was provided.'
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Publisher::class,
        ]);
    }
}
