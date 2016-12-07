<?php

namespace nacholibre\DoctrineTranslatableFormBundle\Form;

use nacholibre\DoctrineTranslatableFormBundle\Interfaces\TranslatableFieldInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class DynamicSlugType extends AbstractType {
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            //'slug_input' => null,
            'toggable' => true,
            //"compound" => true,
        ]);

        $resolver->setRequired(["slug_input", "toggable"]);
        //$resolver->setAllowedValues("compound", true);
    }

    public function finishView(FormView $view, FormInterface $form, array $options) {
        $view->vars['slug_input'] = $options['slug_input'];
        parent::finishView($view, $form, $options);
    }

    public function getParent() {
        return TextType::class;
    }
}
