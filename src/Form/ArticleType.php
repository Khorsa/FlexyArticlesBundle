<?php

namespace flexycms\FlexyArticlesBundle\Form;

use flexycms\FlexyArticlesBundle\Entity\ArticleCategory;
use flexycms\FlexyArticlesBundle\Entity\ArticleRubric;
use flexycms\FlexyArticlesBundle\EntityRequest\ArticleRequest;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;


class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

       $builder->add('parameters', CollectionType::class, [
            'entry_type' => ArticleParameterValueType::class,
            'entry_options' => ['label' => false],
            'label' => false,
            'allow_add' => false,
            'allow_delete' => false,
       ]);

        $builder
            ->add('title', TextType::class, array(
                'label' => 'Заголовок',
            ))

            ->add('backpath', HiddenType::class)
            ->add('sort', NumberType::class)

            ->add('rubrics', EntityType::class, array(
                'class' => ArticleRubric::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.section', 'ASC');
                    },
                'expanded' => true,
                'multiple' => true,
                'label' => false,
                'choice_label' =>function ($rubric) {
                    return $rubric->getSection() . ': '. $rubric->getName();
                    },
            ))

            ->add('remark', TextType::class, array(
                'label' => 'Подпись',
                'required' => false,
            ))
            ->add('dateAt', DateType::class, array(
                'label' => 'Дата',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
                'html5' => false,
                'required' => false,
                'attr' => ['class' => 'js-datepicker', 'data-type' => 'date', 'autocomplete' => 'off'],
                'row_attr' => ['class' => 'date-field'],

            ))
            ->add('code', TextType::class, array(
                'label' => 'Код-ссылка статьи',
                'attr' => ['class' => "flexy-translit", 'data-from' => '#article_title' ]
            ))
            ->add('SEOTitle', TextType::class, array(
                'label' => 'SEOTitle',
                'required' => false,
                'empty_data' => '',
                'attr' => ['class' => "flexy-copy", 'data-from' => '#article_title' ]
            ))
            ->add('SEODescription', TextType::class, array(
                'label' => 'SEODescription',
                'required' => false,
                'empty_data' => '',
                'attr' => ['class' => "flexy-copy", 'data-from' => '#article_title' ]
            ))
            ->add('SEOKeywords', TextType::class, array(
                'label' => 'SEOKeywords',
                'required' => false,
                'empty_data' => '',
                'attr' => ['class' => "flexy-copy", 'data-from' => '#article_title' ]
            ))
            ->add('content', TextareaType::class, array(
                'label' => 'Текст',
                'attr' => ['style' => 'min-height: 600px;', 'class' => 'editor-control'],
                'required' => false,
                'empty_data' => '',
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Описание',
                'attr' => ['style' => 'min-height: 300px;', 'class' => 'editor-control'],
                'required' => false,
                'empty_data' => '',
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

            ->add('imageArray', FileType::class, array(
                'label' => 'Выберите файлы',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new All([
                        new File([
                            "maxSize" => "1024k",
                            "mimeTypes" => [
                                'image/jpeg',
                                'image/png',
                            ],
                            "mimeTypesMessage" => "Выберите изображение JPEG или PNG"
                        ])
                    ])

                ],
                'attr' => ['multiply' => "multiply"],
                'multiple' => true,
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
            ->add('isPublished', CheckboxType::class, array(
                'label' => 'Опубликовано',
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
            ->add('parent', EntityType::class, array(
                'label' => 'Родительская категория',
                'class' => ArticleCategory::class,
                'placeholder' => 'Выберите категорию',
                'empty_data' => null,
                'required' => true,
                'choice_label' => 'name',
            ))
            ->add('articleType', EntityType::class, array(
                'label' => 'Тип статьи',
                'class' => \flexycms\FlexyArticlesBundle\Entity\ArticleType::class,
                'placeholder' => '== Выберите тип ==',
                'empty_data' => null,
                'required' => true,
                'choice_label' => 'name',
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
            ->add('uploadfiles', SubmitType::class, array(
                'label' => '',
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
            'data_class' => ArticleRequest::class,
        ]);
    }
}
