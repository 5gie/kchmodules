<?php

use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class categoryextrafields extends Module
{
    function __construct()
    {
        $this->name = 'categoryextrafields';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'kch.software';
        parent::__construct();
        $this->bootstrap = true;
        $this->displayName = $this->l('Category extra fields');
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('additionalCategoryFormFields') &&
            $this->registerHook('actionCategoryFormBuilderModifier') &&
            $this->registerHook('actionAfterCreateCategoryFormHandler') &&
            $this->registerHook('actionAfterUpdateCategoryFormHandler') &&
            $this->alterCategoryTable();
    }
    public function uninstall()
    {
        return $this->removeTableFields() && parent::uninstall();
            
    }

    public function alterCategoryTable()
    {

        $sql = 'ALTER TABLE `ps_category_lang` ADD `heading` VARCHAR(150) NOT NULL;';
        $sql .= 'ALTER TABLE `ps_category_lang` ADD `subheading` VARCHAR(150) NOT NULL;';
        return Db::getInstance()->execute($sql);

    }

    public function removeTableFields()
    {

        $sql = 'ALTER TABLE `ps_category_lang` DROP `heading`;';
        $sql .= 'ALTER TABLE `ps_category_lang` DROP `subheading`;';
        return Db::getInstance()->execute($sql);

    }

    public function hookAdditionalCategoryFormFields($params)
    {
        return [
            (new FormField)
                ->setName('heading')
                ->setType('text')
                ->setRequired(false)
                ->setLabel($this->l('nagłówek')),
            (new FormField)
                ->setName('subheading')
                ->setType('text')
                ->setRequired(false)
                ->setLabel($this->l('Tekst pod nagłówkiem'))
        ];
    }

    public function hookActionCategoryFormBuilderModifier(array $params)
    {
        /** @var FormBuilderInterface $formBuilder */
        $formBuilder = $params['form_builder'];
        $formBuilder->add('heading', TranslatableType::class, [
            'label' => 'Nagłówek',
            'required' => false
        ])->add('subheading', TranslatableType::class, [
            'label' => 'Tekst pod nagłówkiem',
            'required' => false
        ]);

        $category = new Category($params['id']);
        $params['data']['heading'] = $category->heading;
        $params['data']['subheading'] = $category->subheading;

        $formBuilder->setData($params['data']);
    }


    public function hookActionAfterUpdateCategoryFormHandler(array $params)
    {
        $this->updateCategoryData($params);
    }

    public function hookActionAfterCreateCategoryFormHandler(array $params)
    {
        $this->updateCategoryData($params);
    }

    private function updateCategoryData(array $params)
    {
        try{
            $category = new Category((int) $params['id']);
            $category->heading = $params['form_data']['heading'];
            $category->subheading = $params['form_data']['subheading'];
            $category->update();
        } catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

}
