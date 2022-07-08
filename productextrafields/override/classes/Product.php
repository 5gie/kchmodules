<?php

class Product extends ProductCore {
    
    public $subtitle;
    public $additional_description;
    
    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, \Context $context = null) {
        self::$definition['fields']['subtitle'] = [
            'type' => self::TYPE_HTML,
            'lang' => true,
            'required' => false,
            'validate' => 'isCleanHtml'
        ];
        self::$definition['fields']['additional_description'] = [
            'type' => self::TYPE_HTML,
            'lang' => true,
            'required' => false,
            'validate' => 'isCleanHtml'
        ];
        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
    }
}
