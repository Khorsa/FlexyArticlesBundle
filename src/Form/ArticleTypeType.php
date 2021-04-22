<?php

namespace flexycms\FlexyArticlesBundle\Form;

use flexycms\FlexyArticlesBundle\EntityRequest\ArticleTypeRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ArticleTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder->add('parameters', CollectionType::class, [
                'entry_type' => ArticleParameterType::class,
                'entry_options' => ['label' => false],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])



            ->add('name', TextType::class, array(
                'label' => 'Название',
            ))



            ->add('hasRemark', CheckboxType::class, array(
                'label' => 'Есть подпись?',
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


            ->add('hasRubric', CheckboxType::class, array(
                'label' => 'Используются рубрики?',
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


            ->add('hasSEO', CheckboxType::class, array(
                'label' => 'Есть SEO-параметры?',
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

            ->add('hasImage', CheckboxType::class, array(
                'label' => 'Есть основное изображение?',
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

            ->add('hasImageAlbum', CheckboxType::class, array(
                'label' => 'Есть фотоальбом?',
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

            ->add('publishedDefault', CheckboxType::class, array(
                'label' => 'Опубликовано по-умолчанию?',
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

            ->add('hasDate', CheckboxType::class, array(
                'label' => 'Есть дата?',
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




            ->add('descriptionType', ChoiceType::class, [
                'label' => 'Тип описания',
                'choices'  => [
                    'Без описания' => 0,
                    'Редактор TinyMCE' => 1,
                    'Код HTML' => 2,
                ],
            ])

            ->add('textType', ChoiceType::class, [
                'label' => 'Тип текста',
                'choices'  => [
                    'Без описания' => 0,
                    'Редактор TinyMCE' => 1,
                    'Код HTML' => 2,
                ],
            ])

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
            'data_class' => ArticleTypeRequest::class,
        ]);
    }

}
