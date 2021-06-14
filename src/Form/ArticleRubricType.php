<?php

namespace flexycms\FlexyArticlesBundle\Form;


use flexycms\FlexyArticlesBundle\EntityRequest\ArticleRubricRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ArticleRubricType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Название',
            ))
            ->add('section', TextType::class, array(
                'label' => 'Раздел рубрик',
                'attr' => ['list' => 'rubriclist',  'autocomplete' => "off"],
            ))
            ->add('code', TextType::class, array(
                'label' => 'Код-ссылка рубрики статей',
                'attr' => ['class' => "flexy-translit", 'data-from' => '#article_rubric_name' ]
            ))
            ->add('sort', NumberType::class, array(
                'label' => 'Сортировка',
                'required' => false,

            ))
            ->add('textPosition', ChoiceType::class, array(
                'label' => 'Положение текста',
                'choices' => [
                     "слева вверху" => 0,
                     "посередине вверху" => 1,
                     "справа вверху" => 2,
                     "слева посередине" => 3,
                     "посередине" => 4,
                     "справа посередине" => 5,
                     "слева внизу" => 6,
                     "посередине внизу" => 7,
                     "справа внизу" => 8,
                ],
            ))
            ->add('textColor', ColorType::class, array(
                'label' => 'Цвет текста',
            ))
            ->add('SEOTitle', TextType::class, array(
                'label' => 'SEOTitle',
                'required' => false,
                'empty_data' => '',
                'attr' => ['class' => "flexy-copy", 'data-from' => '#article_rubric_name' ]
            ))
            ->add('SEODescription', TextType::class, array(
                'label' => 'SEODescription',
                'required' => false,
                'empty_data' => '',
                'attr' => ['class' => "flexy-copy", 'data-from' => '#article_rubric_name' ]
            ))
            ->add('SEOKeywords', TextType::class, array(
                'label' => 'SEOKeywords',
                'required' => false,
                'empty_data' => '',
                'attr' => ['class' => "flexy-copy", 'data-from' => '#article_rubric_name' ]
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
            'data_class' => ArticleRubricRequest::class,
        ]);
    }
}
