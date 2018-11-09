<?php
namespace daodao97\apidoc;

/**
 * apidoc module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'daodao97\apidoc\controllers';
    
    public $defaultRoute = 'api/doc';
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        // custom initialization code goes here
    }
}
