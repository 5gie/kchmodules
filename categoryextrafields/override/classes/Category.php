<?php


class Category extends CategoryCore
{

    public $heading;
    public $subheading;

    public function __construct($idCategory = null, $idLang = null, $idShop = null)
    {
        self::$definition['fields']['heading'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml');
        self::$definition['fields']['subheading'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml');
        
        parent::__construct($idCategory, $idLang, $idShop);
    }

}
