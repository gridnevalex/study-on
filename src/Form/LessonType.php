<?php

namespace App\Form;

use App\Entity\Lesson;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Form\DataTransformer\CourseIdToCourseObject;

class LessonType extends AbstractType
{
    private $transformer;

    public function __construct(CourseIdToCourseObject $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('content', TextareaType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('serialNumber', IntegerType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'type' => 'number')))
            ->add('course', HiddenType::class)
        ;
        $builder->get('course')
        ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
    }
}
