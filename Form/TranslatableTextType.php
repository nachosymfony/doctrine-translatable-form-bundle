<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 17/5/16
 * Time: 14:53
 */

namespace nacholibre\DoctrineTranslatableFormBundle\Form;


use nacholibre\DoctrineTranslatableFormBundle\Interfaces\TranslatableFieldInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslatableTextType extends AbstractType
    implements TranslatableFieldInterface{



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "compound"        => true,
        ]);
        $resolver->setRequired(["compound"]);
        $resolver->setAllowedValues("compound", true);

    }

    public function getParent()
    {
        return TextType::class;
    }


}
