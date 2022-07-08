<?php

class productextratab extends Module
{
    function __construct()
    {
        $this->name = 'productextratab';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'kch.software';
        parent::__construct();
        $this->bootstrap = true;
        $this->displayName = $this->l('Product extra content');
    }

    public function install()
    {
        return $this->alterCategoryTable()
        && parent::install()
        && $this->registerHook('actionProductAdd')
        && $this->registerHook('actionProductDelete')
        && $this->registerHook('actionProductUpdate')
        // && $this->registerHook('displayFooterProduct')
        && $this->registerHook('displayWrapperBottom')
        && $this->registerHook('displayHeader')
        && $this->registerHook('displayAdminProductsExtra');
        
    }
    public function uninstall()
    {
        return $this->removeTableFields() && parent::uninstall();
            
    }

    public function alterCategoryTable()
    {

        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'product_extra_fields` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `id_product` int(11) NOT NULL,
			  `id_lang` int(11) NOT NULL,
			  `id_shop` int(11) NOT NULL,
			  `title` varchar(250) NOT NULL,
			  `subtitle` varchar(250) NOT NULL,
			  `content` TEXT NOT NULL,
			  `sort` int(11) NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        return Db::getInstance()->execute($sql);

    }

    public function removeTableFields()
    {
        $sql = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'product_extra_fields`';
        return Db::getInstance()->execute($sql);
    }

    public function hookActionProductDelete(array $params)
    {
        if (!$id_product = $params['id_product']) return;
        return Db::getInstance()->delete('product_extra_fields', 'id_product = '.$id_product);
    }

    public function hookActionProductAdd(array $params)
    {
        $this->updateProductData($params);
    }

    public function hookActionProductUpdate(array $params)
    {
        $this->updateProductData($params);
    }

    public function hookDisplayAdminProductsExtra(array $params)
    {

        if (!$id_product = $params['id_product']) {
            return;
        }

        $languages = Language::getLanguages(true);

        $extra_fields = $this->getExtraFields($id_product, $this->context->shop->id);

        $products = (array) json_decode(Configuration::get($this->name . '_products'), true);

        $this->context->smarty->assign(
            array(
                'languages' => $languages,
                'fields_amount' => 6,
                'extra_fields' => $extra_fields,
                'extra_fields_enabled' => (bool) in_array($id_product, $products),
                'default_language' => $this->context->employee->id_lang,
            )
        );
        return $this->display(__FILE__, 'views/templates/hook/extrafields.tpl'); 

    }

    public function updateProductData(array $params)
    {
        if (!$id_product = $params['id_product']) {
            return;
        } else if(!$extra_fields = Tools::getValue('extra_fields')) {
            return;
        }

        $languages = Language::getLanguages(true);

        foreach ($languages as $lang) {

            $this->deleteExtraFields($id_product, $lang['id_lang'], $this->context->shop->id);

        }

        foreach($extra_fields as $field){

            foreach($languages as $lang){

                $data = [
                    'title' => addslashes($field['title'][$lang['id_lang']]),
                    'subtitle' => addslashes($field['subtitle'][$lang['id_lang']]),
                    'content' => addslashes($field['content'][$lang['id_lang']]),
                    'sort' => $field['sort'][$lang['id_lang']]
                    
                ];

                $this->insertExtraField($id_product, $lang['id_lang'], $this->context->shop->id, $data);

            }

        }
        
        $this->updateProductEnabled($params['id_product']);

        $this->_clearCache('*');

    }

    public function updateProductEnabled($id_product)
    {
        $products = (array)json_decode(Configuration::get($this->name . '_products'), true);
        if ((bool)Tools::getValue('extra_fields_enabed')) {
            if(!in_array($id_product, $products)){
                $products[] = $id_product;
            }
        } else {
            $products = array_diff($products, [$id_product]);
        }
        Configuration::updateValue($this->name . '_products', json_encode($products));
    }

    public function getExtraFields($id_product, $id_shop, $id_lang = false){

        $q = '';
        if($id_lang) {
            $q = ' AND id_lang = ' . (int)$id_lang;
        }

        $sql = 'SELECT * FROM `'._DB_PREFIX_.'product_extra_fields` WHERE id_product = '.(int)$id_product.' AND id_shop = '.(int)$id_shop.''.$q;
        return Db::getInstance()->executeS($sql);

    }
    public function getExtraField($id_product, $id_lang, $id_shop){


        $sql = 'SELECT * FROM `'._DB_PREFIX_.'product_extra_fields` WHERE id_product = '.(int)$id_product.' AND id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$id_shop.'';
        return Db::getInstance()->getRow($sql);

    }
    public function deleteExtraFields($id_product, $id_lang, $id_shop){

        return Db::getInstance()->delete('product_extra_fields', 'id_product = ' . (int)$id_product . ' AND id_lang = ' . (int)$id_lang . ' AND id_shop = ' . (int)$id_shop . '');

    }
    public function insertExtraField($id_product, $id_lang, $id_shop, $data){

        $data = array_merge([
            'id_product' => $id_product,
            'id_lang' => $id_lang,
            'id_shop' => $id_shop,
        ], $data);
        
        return Db::getInstance()->insert(
            'product_extra_fields',
            $data
        );

    }

    public function hookDisplayWrapperBottom(array $params)
    {

        if (!$this->context->controller instanceof ProductControllerCore){
            return;
        }

        if(!$product = $this->context->controller->getProduct()) {
            return;
        }

        $cacheKey = $this->name . '|' . $product->id;
        $template = 'module:' . $this->name . '/views/templates/hook/product.tpl';

        if (!$this->isCached($template, $this->getCacheId($cacheKey))) {

            $products = (array) json_decode(Configuration::get($this->name . '_products'), true);

            if(!in_array($product->id, $products)){
                return;
            }

            $extra_fields = $this->getExtraFields($product->id, $this->context->shop->id, $this->context->language->id);

            $this->context->smarty->assign([
                'extra_fields' => $extra_fields
            ]);

        }

        return $this->fetch($template, $this->getCacheId($cacheKey));

    }

    public function hookDisplayHeader(array $params)
    {
        if($this->context->controller->php_self == 'product'){
            $this->context->controller->registerJavascript('extra_fields_js', 'modules/' . $this->name . '/views/assets/js/extrafields.js', ['position' => 'bottom', 'priority' => 0]);
            $this->context->controller->registerStylesheet('extra_fields_css', 'modules/' . $this->name . '/views/assets/css/extrafields.css', ['position' => 'bottom', 'priority' => 0]);
        }
    }

}
