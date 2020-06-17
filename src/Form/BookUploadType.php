<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BookUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'book',
                FileType::class,
                [
                    'label' => 'Загрузить книгу',
                    'mapped' => false,
                    'required' => true,
                    'constraints' => [
                        new File(
                            [
                                'maxSize' => '1024m',
                                'mimeTypes' => [
                                    'text/xml',
                                    'application/epub+zip',
                                ],
                                'mimeTypesMessage' => 'Неверный формат файла',
                            ]
                        )
                    ],
                ]
            );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
