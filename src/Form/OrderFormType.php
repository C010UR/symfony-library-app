<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class OrderFormType extends AbstractType
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
            ->add('phoneNumber', TelType::class, [
                'required' => true,
                'trim' => true,
                'invalid_message' => 'Phone number is not valid.',
                'constraints' => [new Assert\NotBlank(message: 'Phone number cannot be empty.')],
            ])
            ->add('book', EntityType::class, [
                'required' => true,
                'class' => Book::class,
                'invalid_message' => 'Book is not valid.',
                'constraints' => [new Assert\NotBlank(message: 'Book is not specified.')],
            ])
            ->add('quantity', IntegerType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Quantity cannot be empty.'),
                    new Assert\GreaterThan(0, message: 'Quantity must be greater than {{ compared_value }}. {{ value }} was provided.'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
