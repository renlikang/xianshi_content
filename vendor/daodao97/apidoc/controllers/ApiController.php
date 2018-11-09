<?php
namespace daodao97\apidoc\controllers;
use yii\web\Controller;
use Yii;
class ApiController extends Controller
{
    public $layout=false;
    public function actionDoc()
    {
        if(YII_ENV_DEV)
        {
            return $this->render('index',['rest_url'=>'/'.Yii::$app->controller->module->id.'/api/list','oauthConfig'=>[]]);
        }
        else
        {
            return $this->redirect('/');
        }
    }
    
    public function actionList()
    {
        $params = Yii::$app->params['apidoc']??[];
        $doc_dir[] = __DIR__;
        if(isset($params['scan_dir']) && is_array($params['scan_dir']))
        {
            foreach($params['scan_dir'] as $item)
            {
                $doc_dir[] = Yii::getAlias('@'.$item);
            }
        }
        try
        {
            $swagger =  \Swagger\scan($doc_dir);
            echo ($swagger);exit;
        }
        catch(\Exception $exception)
        {
            echo json_encode([$exception->getFile(),$exception->getMessage(),$exception->getLine()]);
        }
    }
    
    /**
     * @SWG\Swagger(
     *     schemes={"http","https"},
     *     basePath="/",
     *     @SWG\Info(
     *         version="1.0.0",
     *         title="Api 文档",
     *     ),
     * )
     */
    public function actionInfo()
    {}
}
