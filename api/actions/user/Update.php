<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace api\actions\user;

use common\components\WXBizDataCrypt;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

/**
 * UpdateAction implements the API endpoint for updating a model.
 *
 * For more details and usage information on UpdateAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Update extends \yii\rest\Action
{
    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;


    /**
     * Updates an existing model.
     * @param string $id the primary key of the model.
     * @return \yii\db\ActiveRecordInterface the model being updated
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function run($id)
    {
        $model = User::findOne($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        if ($code = Yii::$app->request->post('code')) {
            $session = Login::code2Session($code);
            $sessionKey = $session['session_key'];
        } else {
            $sessionKey = $model->session_key;
        }

        $encryptedData = Yii::$app->request->post('encryptedData');
        $iv = Yii::$app->request->post('iv');

        $appid = \Yii::$app->params['appid'];
        $crypt = new WXBizDataCrypt($appid, $sessionKey);
        $result = $crypt->decryptData($encryptedData, $iv, $data);
        if ($result == 200) {
            Yii::info("解密成功" . $data['unionId'], __CLASS__ . "::" . __FUNCTION__);
            $model->unionid = $data['unionId'];
        } else {
            Yii::error("解密失败" . $result, __CLASS__ . "::" . __FUNCTION__);
        }

        $model->scenario = $this->scenario;
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }
}
