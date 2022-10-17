<?php

namespace App\Form;

use App\Entity\Article;/*
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;*/

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class, ['required' => true])
            ->add('content',TextType::class)
            //->add('createAt',DateTimeType::class)
            //->add('updateAt',DateTimeType::class)
            ->add('author',TextType::class)
            ->add('nbViews',IntegerType::class, ['required' => true])
            ->add('published')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
