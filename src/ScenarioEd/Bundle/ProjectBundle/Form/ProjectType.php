<?php

namespace ScenarioEd\Bundle\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('repository_uri')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ScenarioEd\Bundle\ProjectBundle\Entity\Project'
        ));
    }

    public function getName()
    {
        return 'scenarioed_bundle_projectbundle_projecttype';
    }
}
