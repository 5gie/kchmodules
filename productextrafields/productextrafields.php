<?php

class productextrafields extends Module {
    
     public function __construct() {

        $this->name = 'productextrafields';
        $this->tab = 'others';
        $this->author = 'kch.software';
        $this->version = '1.0.0';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();
		
        $this->displayName = $this->l('Product extra fields');
		
		
        $this->ps_versions_compliancy = array('min' => '1.7.1', 'max' => _PS_VERSION_);
    }
    
   public function install() {
        if (!parent::install() || !$this->_installSql()
                || ! $this->registerHook('displayAdminProductsMainStepLeftColumnMiddle')
        ) {
            return false;
        }

        return true;
    }
    
     public function uninstall() {
        return parent::uninstall() && $this->_unInstallSql();
    }

    /**
     * @return boolean
     */
    protected function _installSql() {
        $sqlInstallLang = "ALTER TABLE " . _DB_PREFIX_ . "product_lang "
                . "ADD subtitle VARCHAR(250) NULL;";
        $sqlInstallLang .= "ALTER TABLE " . _DB_PREFIX_ . "product_lang "
                . "ADD additional_description text NULL;";
        $returnSqlLang = Db::getInstance()->execute($sqlInstallLang);
        
        return $returnSqlLang;
    }

    /**
     * @return boolean
     */
    protected function _unInstallSql() {
        $sqlInstallLang = "ALTER TABLE " . _DB_PREFIX_ . "product_lang "
                . "DROP subtitle;";
        $sqlInstallLang .= "ALTER TABLE " . _DB_PREFIX_ . "product_lang "
                . "DROP additional_description;";

        $returnSqlLang = Db::getInstance()->execute($sqlInstallLang);
        
        return $returnSqlLang;
    }
    
    
    /**
     * @param type $params
     * @return type
     */
    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params) {
        $product = new Product($params['id_product']);
        $languages = Language::getLanguages(true);

        $this->context->smarty->assign(array(
                'languages' => $languages,
                'subtitle' => $product->subtitle,
                'additional_description' => $product->additional_description,
                'default_language' => $this->context->employee->id_lang,
            )
        );
        
        return $this->display(__FILE__, 'views/templates/hook/extrafields.tpl');
    }
}
