<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class home_categories extends Module implements WidgetInterface {

    public function __construct() {
        $this->name = 'home_categories';
        $this->prefix = strtoupper($this->name);
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'kch.software';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Home categories');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        $this->templateFile = 'module:home_categories/views/templates/hook/home_categories.tpl';

    }

    public function install() {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        return parent::install() && $this->registerHook('displayHomeCategories') && $this->registerHook('displayHeader');
    }

    public function getContent() {

        return $this->postProcess().$this->renderForm();

    }

    public function postProcess()
    {

        if (Tools::isSubmit('submitBlockCategories')) {

            Configuration::updateValue('HOME_CATEGORY', Tools::getValue('HOME_CATEGORY'));
            Configuration::updateValue('HOME_CATEGORY_LIMIT', Tools::getValue('HOME_CATEGORY_LIMIT'));

            $this->_clearCache('*');

        }

    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->getTranslator()->trans('Settings', array(), 'Admin.Global'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'categories',
                        'tree' => [
                            'id' => 'categories',
                            'selected_categories' => [Configuration::get('HOME_CATEGORY')],
                        ],
                        'label' => $this->trans('Kategorie z której mają być pobrane podkategorie', [], 'Modules.Homepopup.Admin'),
                        'name' => 'HOME_CATEGORY',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Limit', [], 'Modules.Homepopup.Admin'),
                        'name' => 'HOME_CATEGORY_LIMIT',
                    ),
                ),
                'submit' => array(
                    'title' => $this->getTranslator()->trans('Save', array(), 'Admin.Actions'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->submit_action = 'submitBlockCategories';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues()
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'HOME_CATEGORY_LIMIT' => Tools::getValue('HOME_CATEGORY_LIMIT', Configuration::get('HOME_CATEGORY_LIMIT'))
        );
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {

        if (!$this->isCached($this->templateFile, $this->getCacheId($this->name))) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->fetch($this->templateFile, $this->getCacheId($this->name));

    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        return [
            'home_categories' => $this->getCategories()
        ];
    }

    protected function getCategories()
    {
        $categories = false;

        try{

            $categories = Db::getInstance()->executeS('SELECT id_category FROM '._DB_PREFIX_.'category WHERE is_home = 1');

        } catch (Exception $e) {

            return false;

        }

        if($categories){
            
            return array_map(function (array $category) {
                $object = new Category(
                    $category['id_category'],
                    $this->context->language->id
                );

                $category['image'] = $this->getImage(
                    $object,
                    $object->id_image
                );

                $category['name'] = isset($object->subtitle) && !empty($object->subtitle) ? $object->subtitle : $object->name;
    
                $category['url'] = $this->context->link->getCategoryLink(
                    $category['id_category'],
                    $object->link_rewrite
                );
    
                return $category;
            }, $categories);

            usort($output, function ($item1, $item2) {
                return $item1['position'] <=> $item2['position'];
            });

        } else {

            return false;

        }
    }

    protected function getImage($object, $id_image)
    {
        $retriever = new ImageRetriever(
            $this->context->link
        );

        return $retriever->getImage($object, $id_image);
    }

    public function hookDisplayHeader(array $params)
    {
        if ($this->context->controller->php_self == 'index') {
            $this->context->controller->registerStylesheet('modules-'.$this->name, 'modules/' . $this->name . '/css/'.$this->name.'.css', ['media' => 'all', 'priority' => 500]);
        }
    }

}
