<?php
namespace tanghengzhi\apidoc;

/**
 * apidoc module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'tanghengzhi\apidoc\controllers';
    
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
