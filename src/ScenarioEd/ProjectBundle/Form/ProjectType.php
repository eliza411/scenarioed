<?php

namespace ScenarioEd\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('project_name')
            ->add('project_description')
            ->add('repository_uri')
            ->add('base_url')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ScenarioEd\ProjectBundle\Entity\Project'
        ));
    }

    public function getName()
    {
        return 'scenarioed__projectbundle_projecttype';
    }
}
