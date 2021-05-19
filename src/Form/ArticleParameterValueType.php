<?php


namespace flexycms\FlexyArticlesBundle\Form;


use flexycms\FlexyArticlesBundle\Entity\ArticleParameter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;



class ArticleParameterValueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $parameter = $event->getData();

            $form = $event->getForm();

            $type = TextType::class;

            //dump($parameter);

            $options = array();
            switch($parameter->getType())
            {
                case "string":
                    $type = TextType::class;
                    $options = ['required' => false,];
                    break;
                case "int":
                    $type = IntegerType::class;
                    $options = ['required' => false,];
                    break;
                case "float":
                    $type = NumberType::class;
                    $options = ['required' => false,];
                    break;
                case "datetime":
                    $type = DateTimeType::class;
                    $options = [
                        'widget' => 'single_text',
                        'format' => 'dd.MM.yyyy HH:mm:ss',
                        'html5' => false,
                        'attr' => ['class' => 'js-datepicker', 'data-type' => 'datetime', 'autocomplete' => 'off', 'readonly' => 'readonly'],
                        'row_attr' => ['class' => 'date-field'],
                    ];
                    break;
                case "bool":
                    $type = CheckboxType::class;
                    $options = [
                        'required' => false,
                        'row_attr' => [
                            'class' => 'custom-control custom-switch',
                        ],
                        'attr' => [
                            'class' => 'custom-control-input',
                        ],
                        'label_attr' => [
                            'class' => 'custom-control-label',
                        ],];
                    break;
            }
            $form->add("value", $type,
                ['label' => $parameter->getLabel()] + $options
            );





        });


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArticleParameter::class,
        ]);
    }
}