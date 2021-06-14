<?php

namespace flexycms\FlexyArticlesBundle\Form;

use flexycms\FlexyArticlesBundle\Entity\ArticleCategory;
use flexycms\FlexyArticlesBundle\EntityRequest\ArticleCategoryRequest;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ArticleCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Название',
            ))
            ->add('code', TextType::class, array(
                'label' => 'Код-ссылка категории статей',
                'attr' => ['class' => "flexy-translit", 'data-from' => '#article_category_name' ]
            ))
            ->add('SEOTitle', TextType::class, array(
                'label' => 'SEOTitle',
                'required' => false,
                'empty_data' => '',
                'attr' => ['class' => "flexy-copy", 'data-from' => '#article_category_name' ]
            ))
            ->add('SEODescription', TextType::class, array(
                'label' => 'SEODescription',
                'required' => false,
                'empty_data' => '',
                'attr' => ['class' => "flexy-copy", 'data-from' => '#article_category_name' ]
            ))
            ->add('SEOKeywords', TextType::class, array(
                'label' => 'SEOKeywords',
                'required' => false,
                'empty_data' => '',
                'attr' => ['class' => "flexy-copy", 'data-from' => '#article_category_name' ]
            ))
            ->add('createAt', DateTimeType::class, array(
                'label' => 'Дата создания',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm:ss',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker', 'data-type' => 'datetime', 'autocomplete' => 'off', 'readonly' => 'readonly'],
                'row_attr' => ['class' => 'date-field'],

            ))
            ->add('updateAt', DateTimeType::class, array(
                'label' => 'Дата изменения',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm:ss',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker', 'data-type' => 'datetime', 'autocomplete' => 'off', 'readonly' => 'readonly'],
                'row_attr' => ['class' => 'date-field'],

            ))

            ->add('image', FileType::class, array(
                'label' => 'Изображение',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Выберите изображение JPEG или PNG',
                    ])
                ],
            ))
            ->add('imageAlt', TextType::class, array(
                'label' => 'Альтернативная надпись (тэг Alt)',
                'mapped' => true,
                'required' => false,
            ))
            ->add('imageTitle', TextType::class, array(
                'label' => 'Заголовок (тэг Title)',
                'mapped' => true,
                'required' => false,
            ))


            ->add('parent', EntityType::class, array(
                'label' => 'Родительская категория',
                'class' => ArticleCategory::class,
                'placeholder' => '== Верхняя категория ==',
                'empty_data' => null,
                'required' => false,
                'choice_label' => 'name',
            ))
            ->add('defaultArticleType', EntityType::class, array(
                'label' => 'Тип статей по-умолчанию',
                'class' => ArticleType::class,
                'placeholder' => '== Выберите тип ==',
                'empty_data' => null,
                'required' => true,
                'choice_label' => 'name',
            ))
            ->add('showInMenu', CheckboxType::class, array(
                'label' => 'Отображать в меню',
                'required' => false,
                'row_attr' => [
                    'class' => 'custom-control custom-switch',
                ],
                'attr' => [
                    'class' => 'custom-control-input',
                ],
                'label_attr' => [
                    'class' => 'custom-control-label',
                ],
            ))
            ->add('save', SubmitType::class, array(
                'label' => '<i class="fas fa-save"></i><br>Сохранить',
                'label_html' => true,
                'attr' => [
                    'class' => 'btn btn-success',
                ],
            ))
            ->add('apply', SubmitType::class, array(
                'label' => '<i class="fas fa-check"></i><br>Применить',
                'label_html' => true,
                'attr' => [
                    'class' => 'btn btn-success',
                ],
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ArticleCategoryRequest::class,
        ]);
    }
}
