<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Editor;
use App\Enum\BookStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('isbn', TextType::class)
            ->add('cover', UrlType::class)
            ->add('editedAt', DateType::class, [
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
            ])
            ->add('plot', TextareaType::class)
            ->add('pageNumber', NumberType::class)
            ->add('status', EnumType::class, [
                'class' => BookStatus::class,
            ])
            ->add('editor', EntityType::class, [
                'class' => Editor::class,
                'choice_label' => 'name',
            ])
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'name',
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('certification', CheckboxType::class, [
                'mapped' => false,
                'label' => "Je certifie que les informations saisies sont correctes",
                'constraints' => [new IsTrue(message: "Vous devez cocher la case pour ajouter un livre")],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
