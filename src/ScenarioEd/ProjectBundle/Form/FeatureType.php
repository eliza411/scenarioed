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
        //    ->add('contents', 'textarea', array('attr' => array('rows' => 25, 'cols' => 80)));
            ->add('contents', 'ace_editor', array(
              'wrapper_attr' => array(), // aceeditor wrapper html attributes.
              'width' => 800,
              'height' => 400,
              'font_size' => 16,
              'mode' => 'ace/mode/gherkin-en', // every single default mode must have ace/mode/* prefix
              'theme' => 'ace/theme/xcode', // every single default theme must have ace/theme/* prefix
              'tab_size' => null,
              'read_only' => null,
              'use_soft_tabs' => null,
              'use_wrap_mode' => null,
              'show_print_margin' => null,
              'highlight_active_line' => null
            ));
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
