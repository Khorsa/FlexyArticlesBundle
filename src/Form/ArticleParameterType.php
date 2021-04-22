<?php

namespace flexycms\FlexyArticlesBundle\Form;


use flexycms\FlexyArticlesBundle\Entity\ArticleParameter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class ArticleParameterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'delete',
            ButtonType::class,
            [
                'label' => '<i class="far fa-minus-square"></i>',
                'label_html' => true,
                'attr' => ['class' => 'btn btn-danger delete-parameter'],
            ]);
        $builder->add('code', TextType::class, ['label' => 'код']);
        $builder->add('label', TextType::class, ['label' => 'метка']);
        $builder->add('type', ChoiceType::class, ['label' => 'тип', 'choices' => ArticleParameter::getTypes()]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArticleParameter::class,
        ]);
    }
}