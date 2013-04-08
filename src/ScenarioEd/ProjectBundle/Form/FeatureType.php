<?php

namespace ScenarioEd\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contents', 'textarea', array('attr' => array('rows' => 25, 'cols' => 80)))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ScenarioEd\ProjectBundle\Entity\Feature'
        ));
    }

    public function getName()
    {
        return 'scenarioed_projectbundle_featuretype';
    }
}
