<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Publisher;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Book name cannot be empty.'),
                    new Assert\Length(
                        min: 3,
                        minMessage: 'Book name should have at least {{ limit }} characters. {{ value }} was provided.',
                        max: 255,
                        maxMessage: 'Book name should not exceed {{ limit }} characters. {{ value }} was provided.'
                    )
                ]
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'trim' => true,
                'constraints' => [
                    new Assert\Length(
                        min: 3,
                        minMessage: 'Book description should have at least {{ limit }} characters. {{ value }} was provided.',
                        max: 255,
                        maxMessage: 'Book description should not exceed {{ limit }} characters. {{ value }} was provided.'
                    )
                ]
            ])
            ->add('pageCount', IntegerType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Book page count cannot be empty.'),
                    new Assert\GreaterThan(0, message: 'Book page count must be greater than {{ compared_value }}. {{ value }} was provided.')
                ]
            ])
            ->add('datePublished', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'invalid_message' => 'Date is not valid.',
                'constraints' => [new Assert\NotBlank(message: 'Published date is not specified.')]
            ])
            ->add('publisher', EntityType::class, [
                'required' => true,
                'class' => Publisher::class,
                'invalid_message' => 'Publisher is not valid.',
                'constraints' => [new Assert\NotBlank(message: 'Publisher is not specified.')]
            ])
            ->add('tags', EntityType::class, [
                'required' => true,
                'class' => Tag::class,
                'multiple' => true,
                'invalid_message' => 'Tags are not valid.',
                'constraints' => [new Assert\NotBlank(message: 'Tags are not specified.')]

            ])
            ->add('authors', EntityType::class, [
                'required' => true,
                'class' => Author::class,
                'multiple' => true,
                'invalid_message' => 'Books are not valid.',
                'constraints' => [new Assert\NotBlank(message: 'Authors are not specified.')]
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
            'data_class' => Book::class,
        ]);
    }
}
