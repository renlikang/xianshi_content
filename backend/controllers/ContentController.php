<?php
/**
 * @author rlk
 */

namespace backend\controllers;

use common\models\content\ArticleModel;
use common\models\content\AuthorModel;
use common\models\User;
use common\services\content\ArticleService;
use common\services\content\AuthorService;
use common\services\RetCode;
use common\services\UserService;
use yii\base\Exception;
use yii\helpers\Json;
use yii\rest\Controller;
use Yii;
use yii\web\ForbiddenHttpException;

class ContentController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/content/loading",
     *     tags={"内容管理"},
     *     summary="导入内容",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "formData",name = "article",description = "导入的文章信息",required = true, type = "string"),
     *     @SWG\Response(response = 200,description = " success"),
     *     @SWG\Response(response = 100,description = "导入的文章信息失败"),
     * )
     *
     */
    public function actionLoading()
    {
        try {
            $article = Yii::$app->request->post();
            if(isset($article['authorInfo']) && $article['authorInfo']) {
                if(!$article['authorInfo']['unionid']) {
                    return RetCode::response(RetCode::ERROR, [],[],'媒体作者unionid不能为空');
                }

                $unionid = $article['authorInfo']['unionid'];
                $author = User::findOne(['unionid' => $unionid]);
                if(!$author) {
                    $params['name'] = $article['authorInfo']['author'];
                    $params['avatarImg'] = $article['authorInfo']['authorIconLink'] ?? '';
                    $params['unionid'] = $unionid;
                    $params['signature'] = $article['authorInfo']['signature'] ?? '';;
                    $authorId = UserService::create($params);
                    if($authorId == false) {
                        return RetCode::response(RetCode::ERROR, [],[],'保存失败');
                    }
                } else {
                    $authorId = $author->id;
                }

                $params = [];
                $params['article']['type'] = $article['articleInfo']['type'] ?? 1;
                $params['article']['source'] = $article['articleInfo']['source'] ?? '';
                $params['article']['title'] = $article['articleInfo']['title'] ?? '';
                $params['article']['subTitle'] = $article['articleInfo']['subTitle'] ?? '';
                $params['article']['summary'] = $article['articleInfo']['summary'] ?? '';
                $params['article']['orderId'] = $article['articleInfo']['orderId'] ?? 0;
                $params['article']['headImg'] = $article['articleInfo']['headImg'] ?? '';
                if (isset($article['articleInfo']['covers']) && is_array($article['articleInfo']['covers'])) {
                    $params['article']['covers'] = $article['articleInfo']['covers'];
                } else {
                    $params['article']['covers'] = [];
                }
                if (isset($params['article']['cTime']) && $params['article']['cTime']) {
                    $params['article']['cTime'] = date('Y-m-d H:i:s', strtotime($article['articleInfo']['cTime']));
                }
                $params['contentInfo'] = $article['paragraph'];
                $params['authorId'] = $authorId;
                (new ArticleService)->createArticle($params);
            } else {
                return RetCode::response(RetCode::ERROR, [],[],'媒体作者信息不能为空');
            }
        } catch (Exception $e) {
            return RetCode::response($e->getCode(), [], [], $e->getMessage());
        }

    }
}