<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 17/5/16
 * Time: 16:21
 */

namespace nacholibre\DoctrineTranslatableFormBundle\Form;

use Symfony\Component\Form\Exception;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * stof_doctrine_extensions:
        default_locale: %locale%
        translation_fallback: true
        persist_default_translation: true
        orm:
            default:
                translatable: true
 *
 * Class AbstractType
 */
abstract class AbstractTranslatableType extends \Symfony\Component\Form\AbstractType {
    private $locales = [];

    private $required_locales;

    /**
     * @var DataMapperInterface
     */
    private $mapper;

    function __construct(DataMapperInterface $dataMapper) {
        $this->mapper = $dataMapper;
    }

    public function setRequiredLocales($locales) {
        $this->required_locales = $locales;
    }

    public function setLocales(array $locales) {
        $this->locales = $locales;
    }

    /**
     * @param FormBuilderInterface $builderInterface
     * @param array $options
     * @return DataMapperInterface
     */
    protected function createTranslatableMapper(FormBuilderInterface $builderInterface, array $options){
        $this->mapper->setBuilder($builderInterface, $options);
        $this->mapper->setLocales($options["locales"]);
        $this->mapper->setRequiredLocales($options["required_locales"]);
        $builderInterface->setDataMapper($this->mapper);

        return $this->mapper;
    }

    protected function configureTranslationOptions(OptionsResolver $resolver) {
        $resolver->setRequired(["locales", "required_locales"]);

        $data = [
            'locales'         => $this->locales?:["en"],
            "required_locales" => $this->required_locales ?: ["en"],
        ];


        $resolver->setDefaults($data);
    }
}
