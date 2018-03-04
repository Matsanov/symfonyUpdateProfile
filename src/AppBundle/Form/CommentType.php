<?php
/**
 * Created by PhpStorm.
 * User: mac_v
 * Date: 3/2/2018
 * Time: 4:11 PM
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('commentText', TextareaType::class)
            ->add('save', SubmitType::class, array(
                'attr' => array('class' => 'save'),))
            ->getForm();

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Comment'
        ));

    }

    public function getBlockPrefix()
    {
        return 'app_bundle_register';
    }

}