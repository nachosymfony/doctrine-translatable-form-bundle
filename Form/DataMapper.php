<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 17/5/16
 * Time: 20:58
 */

namespace nacholibre\DoctrineTranslatableFormBundle\Form;


use Doctrine\ORM\EntityManager;
use nacholibre\DoctrineTranslatableFormBundle\Interfaces\TranslatableFieldInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Exception;

class DataMapper implements DataMapperInterface {


    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TranslationRepository
     */
    private $repository;

    /**
     * @var FormBuilderInterface
     */
    private $builder;

    private $translations=[];


    private $locales=[];

    private $required_locale;

    private $property_names=[];




    public function __construct(EntityManager $entityManager){

        $this->em = $entityManager;
        $this->repository = $this->em->getRepository('Gedmo\Translatable\Entity\Translation');

    }

    public function setBuilder(FormBuilderInterface $builderInterface){
        $this->builder = $builderInterface;
    }

    public function setRequiredLocale($locale){
        $this->required_locale = $locale;
    }

    public function setLocales(array $locales){
        $this->locales = $locales;
    }

    public function getLocales()
    {
        return $this->locales;
    }

    public function getTranslations($entity) {
        if(!count($this->translations)){
            $this->translations = $this->repository->findTranslations($entity);
        }

        return $this->translations;

    }

    public function getOrCreateFormField($builder, $name, $type, $options) {
        $field = false;

        try {
            $field = $builder->get($name);
        } catch (\InvalidArgumentException $e) {
            $field = $builder->add($name, $type, $options)->get($name);
        }

        return $field;
    }

    public function add($name, $type, $options=[]) {
        //$this->property_names[] = $name;

        //$translationsFormField = false;
        //try {
        //    $translationsFormField = $this->builder->get('translations');
        //} catch (\InvalidArgumentException $e) {
        //}

        //if (!$translationsFormField) {
        //    $translationsFormField = $this->builder->add('translations', TranslationsType::class, [
        //        'mapped' => false,
        //    ]);
        //}

        $translationsFormField = $this->getOrCreateFormField($this->builder, 'translations', TranslationsType::class, [
            //'mapped' => false,
        ]);

        foreach ($this->locales as $iso) {
            $fieldName = 'lang_' . $iso;

            //$languageGroup = $this->createOrGetField($translationsFormField, 'lang_' . $iso, TranslatableGroupType::class, [
            //    'mapped' => false,
            //    'lang' => $iso,
            //]);

            $languageGroup = $this->getOrCreateFormField($translationsFormField, $fieldName, TranslatableGroupType::class, [
                //'mapped' => false,
                'lang' => $iso,
            ]);


            $languageGroup->add($name, $type, $options);
        }

        return $this;
    }


    /**
     * @param $name
     * @param $type
     * @param array $options
     * @return DataMapper
     * @throws \Exception
     */
    public function add_old($name, $type, $options=[])
    {

        $this->property_names[] = $name;

        //$translations = $this->builder->add('translations', TranslationsType::class, [
        //    'mapped' => false,
        //]);
        //$translationsField = $this->builder->get('translations');

        //$translationsField->add('lang_bg', TranslatableGroupType::class, [
        //    'mapped' => false,
        //    'lang' => 'bg',
        //])->get('lang_bg')->add('name', TextType::class, [
        //    'label' => 'Name',
        //]);

        //$translationsField->add('lang_en', TranslatableGroupType::class, [
        //    'mapped' => false,
        //    'lang' => 'en',
        //])->get('lang_en')->add('name', TextType::class, [
        //    'label' => 'Name',
        //]);

        $field = $this->builder
            ->add($name, $type)
            ->get($name);

        if(!$field->getType()->getInnerType() instanceof TranslatableFieldInterface)
            throw new \Exception("{$name} must implement TranslatableFieldInterface");

        foreach($this->locales as $iso){

            $options = [
                "label"   => $iso,
                "required"=> $iso == $this->required_locale
            ];

            $field->add($iso, get_class($field->getType()->getParent()->getInnerType()), $options);

        }

        return $this;

    }


    /**
     * Maps properties of some data to a list of forms.
     *
     * @param mixed $data Structured data.
     * @param FormInterface[] $forms A list of {@link FormInterface} instances.
     *
     * @throws Exception\UnexpectedTypeException if the type of the data parameter is not supported.
     */
    public function mapDataToForms($data, $forms) {
        foreach($forms as $form) {
            //if ($form->getName() == 'translations') {
            //    $form->setData([
            //        'lang_bg' => [
            //            'name' => 'asd',
            //        ]
            //    ]);
            //}

            if ($form->getName() == 'translations') {
                $translations = $this->getTranslations($data);

                $values = [];
                foreach($translations as $iso => $translatedData) {
                    $values['lang_'.$iso] = $translatedData;
                }

                $form->setData($values);

                //foreach($form as $formLang) {
                //    $iso = explode('_', $formLang->getName())[1];

                //    foreach($formLang as $f) {
                //        //$data = $translations[$iso][$f->getName()];
                //        //var_dump($f->getName());
                //        //var_dump($data);
                //        $f->setData('asd');
                //    }

                //    //var_dump($formLang->getName());


                //    //$values = [];
                //    //foreach($translations[$iso] as $fieldName => $fieldValue) {
                //    //    $values[$fieldName] = $fieldValue;
                //    //}
                //    ////var_dump($iso);
                //    ////print_R($values);
                //    //$formLang->setData($values);

                //    //var_dump($translations[$iso]);
                //}

                //print_R($values);
                //$values['info_page']['translations']['bg']['name'] = 'asd';
                //$values['info_page']['translations']['lang_bg']['name'] = 'asd';
                //$values['lang_bg']['name'] = 'asd';
            } else {
                if(false === $form->getConfig()->getOption("mapped") || null === $form->getConfig()->getOption("mapped")){
                    continue;
                }

                $accessor = PropertyAccess::createPropertyAccessor();
                $form->setData($accessor->getValue($data, $form->getName()));
            }

            //if(false !== in_array($form->getName(), $this->property_names)) {

            //    $values = [];
            //    foreach($this->getLocales() as $iso){

            //        if(isset($translations[$iso])){
            //            $values[$iso] =  $translations[$iso][$form->getName()];
            //        }

            //    }
            //    $form->setData($values);

            //}else{

            //    if(false === $form->getConfig()->getOption("mapped") || null === $form->getConfig()->getOption("mapped")){
            //        continue;
            //    }

            //    $accessor = PropertyAccess::createPropertyAccessor();
            //    $form->setData($accessor->getValue($data, $form->getName()));

            //}

        }

    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[] $forms A list of {@link FormInterface} instances.
     * @param mixed $data Structured data.
     *
     * @throws Exception\UnexpectedTypeException if the type of the data parameter is not supported.
     */
    public function mapFormsToData($forms, &$data)
    {


        $entityInstance = $data;

        /**
         * @var $form FormInterface
         */
        foreach ($forms as $form) {
            if ($form->getName() == 'translations') {
                $translations = $form->getData();
                //print_R($translations);

                foreach($translations as $lang => $data) {
                    $iso = explode('_', $lang)[1];

                    foreach($data as $fname => $fdata) {
                        $this->repository->translate($entityInstance, $fname, $iso, $fdata);
                    }
                }

                //print_R($translations);
                //exit;

                //foreach($this->locales as $iso) {
                //    echo 'here';
                //}

                //echo 'asd';
                //exit;
            } else {
                if(false === $form->getConfig()->getOption("mapped") || null === $form->getConfig()->getOption("mapped")){
                    continue;
                }

                $accessor = PropertyAccess::createPropertyAccessor();
                $accessor->setValue($entityInstance, $form->getName(), $form->getData());
            }

            //continue;


            //$entityInstance = $data;


            //if(false !== in_array($form->getName(), $this->property_names)) {


            //    $translations = $form->getData();
            //    foreach($this->getLocales() as $iso) {
            //        if(isset($translations[$iso])){
            //            $this->repository->translate($entityInstance, $form->getName(), $iso, $translations[$iso] );
            //        }
            //    }


            //}else{

            //    if(false === $form->getConfig()->getOption("mapped") || null === $form->getConfig()->getOption("mapped")){
            //        continue;
            //    }

            //    $accessor = PropertyAccess::createPropertyAccessor();
            //    $accessor->setValue($entityInstance, $form->getName(), $form->getData());

            //}

        }

    }


}
