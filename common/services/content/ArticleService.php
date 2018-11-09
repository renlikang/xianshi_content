<?php
/**
 * @author rlk
 */

namespace common\services\content;

use common\models\admin\OperationArticleModel;
use common\models\content\ArticleCommentModel;
use common\models\content\ArticleFakePraise;
use common\models\content\ArticleModel;
use common\models\content\ArticlePraiseModel;
use common\models\content\AuthorAttentionModel;
use common\models\content\ParagraphContentModel;
use common\models\content\ParagraphModel;
use common\models\content\TagModel;
use common\models\elasticsearch\ArticleElasticModel;
use common\models\User;
use common\services\RetCode;
use common\services\UserService;
use PetstoreIO\Tag;
use yii\base\Exception;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;

class ArticleService
{
    public function createArticle($params, $backendUid = 0)
    {
        $transaction = ArticleModel::getDb()->beginTransaction();
        $model = new ArticleModel;
        if(isset($params['article']) && $params['article'] ) {
            $model->authorId = $params['authorId'];
            if(isset($params['article']['type']) && $params['article']['type']) {
                $model->type = $params['article']['type'];
            }

            if(isset($params['article']['source']) && $params['article']['source']) {
                $model->source = $params['article']['source'];
            }

            if(isset($params['article']['title']) && $params['article']['title']) {
                $model->title = $params['article']['title'];
            }

            if(isset($params['article']['subTitle']) && $params['article']['subTitle']) {
                $model->subTitle = $params['article']['subTitle'];
            }

            if(isset($params['article']['summary']) && $params['article']['summary']) {
                $model->summary = $params['article']['summary'];
            }

            if(isset($params['article']['orderId']) && $params['article']['orderId']) {
                $model->orderId = $params['article']['orderId'];
            }

            if(isset($params['article']['covers']) && $params['article']['covers']) {
                $model->covers = $params['article']['covers'];
            }

            if(isset($params['article']['coverType']) && $params['article']['coverType']) {
                $model->coverType = $params['article']['coverType'];
            }

            if(isset($params['article']['headImg']) && $params['article']['headImg']) {
                $model->headImg = $params['article']['headImg'];
            }

            if (isset($params['article']['cTime']) && $params['article']['cTime']) {
                $model->cTime = $params['article']['cTime'];
            } else {
                $model->cTime = date('Y-m-d H:i:s');
            }

            if(isset($params['article']['tagNameArr']) && $params['article']['tagNameArr']) {
                $tagEs = [];
                foreach ($params['article']['tagNameArr'] as $k => $v) {
                    $tagModel = TagModel::findOne($v['tagName']);
                    if(!$tagModel) {
                        TagService::create($v['tagName']);
                    }
                    
                    $tagEs[] = TagService::detail($v['tagName']);
                }

                $model->tagNames = Json::encode($tagEs);
            } else {
                $model->tagNames = Json::encode([]);
            }

            if(!$model->save()) {
                Yii::error(Json::encode($model->errors), __CLASS__.'::'.__FUNCTION__);
                $transaction->rollBack();
                throw new Exception("文章保存失败", RetCode::DB_ERROR);
            }

            $contentInfo = $params['contentInfo'] ?? [];
            $this->createParagraph($model->articleId, $contentInfo);
            $fakePraise = $params['fakePraise'] ?? 0;
            $this->createFakePraise($model->articleId, $fakePraise);
            $transaction->commit();
            if($backendUid) {
                $operationArticleModel = new OperationArticleModel;
                $operationArticleModel->articleId = $model->articleId;
                $operationArticleModel->aid = $backendUid;
                if(!$operationArticleModel->save()) {
                    Yii::error(Json::encode($operationArticleModel->errors), __CLASS__.'::'.__FUNCTION__);
                }
            }

            if(isset($params['article']['tagNameArr']) && $params['article']['tagNameArr']) {
                foreach ($params['article']['tagNameArr'] as $k => $v) {
                    TagService::mapArticle($v['tagName'], $model->articleId);
                }
            }

            return $model;
        } else {
            throw new Exception("文章信息不能为空", RetCode::ERROR);
        }
    }

    public static function updateBatchArticleDetail($articleIdArr, $params)
    {
        if(!$params) {
            return true;
        }

        /** @var ArticleModel[] $all */
        $all = ArticleModel::find()->where(['articleId' => $articleIdArr])->all();
        $change = [];
        if($params['orderId']) {
            $change['orderId'] = $params['orderId'];
        }

        foreach ($all as $k => $v) {
            foreach ($params as $kk => $vv) {
                $v->$kk = $vv;
                if(!$v->save()) {
                    Yii::error(Json::encode($v->errors), __CLASS__.'::'.__FUNCTION__.'::'.__LINE__);
                }
            }
        }

        return true;
    }

    public function updateArticle($articleId, $params)
    {
        $transaction = ArticleModel::getDb()->beginTransaction();
        $model = ArticleModel::findOne($articleId);
        ParagraphModel::deleteAll(['articleId' => $articleId]);
        ParagraphContentModel::deleteAll(['articleId' => $articleId]);
        if(isset($params['article']) && $params['article'] ) {
            $model->authorId = $params['authorId'];
            if(isset($params['article']['type']) && $params['article']['type']) {
                $model->type = $params['article']['type'];
            }
            if(isset($params['article']['title']) && $params['article']['title']) {
                $model->title = $params['article']['title'];
            } else {
                $model->title = '';
            }
            if(isset($params['article']['subTitle']) && $params['article']['subTitle']) {
                $model->subTitle = $params['article']['subTitle'];
            }
            if(isset($params['article']['summary']) && $params['article']['summary']) {
                $model->summary = $params['article']['summary'];
            }

            if(isset($params['article']['orderId']) && $params['article']['orderId']) {
                $model->orderId = $params['article']['orderId'];
            }

            if(isset($params['article']['covers']) && $params['article']['covers']) {
                $model->covers = $params['article']['covers'];
            }

            if(isset($params['article']['coverType']) && $params['article']['coverType']) {
                $model->coverType = $params['article']['coverType'];
            }

            if(isset($params['article']['headImg']) && $params['article']['headImg']) {
                $model->headImg = $params['article']['headImg'];
            }

            $tagEs = [];
            if(isset($params['article']['tagNameArr']) && $params['article']['tagNameArr']) {
                foreach ($params['article']['tagNameArr'] as $k => $v) {
                    $tagModel = TagModel::findOne($v['tagName']);
                    if(!$tagModel) {
                        TagService::create($v['tagName']);
                    }

                    $tagEs[] = TagService::detail($v['tagName']);
                }
            }

            $model->tagNames = Json::encode($tagEs);
            if(!$model->save()) {
                Yii::error($model->errors, __CLASS__.'::'.__FUNCTION__);
                $transaction->rollBack();
                throw new Exception("文章保存失败", RetCode::DB_ERROR);
            }

            $contentInfo = $params['contentInfo'] ?? [];
            $this->createParagraph($articleId, $contentInfo);
            $fakePraise = $params['fakePraise'] ?? 0;
            $this->createFakePraise($articleId, $fakePraise);
            $transaction->commit();
            if(isset($params['article']['tagNameArr']) && $params['article']['tagNameArr']) {
                TagService::deleteMapArticle($articleId);
                foreach ($params['article']['tagNameArr'] as $k => $v) {
                    TagService::mapArticle($v['tagName'], $articleId);
                }
            }
        } else {
            throw new Exception("文章信息不能为空", RetCode::ERROR);
        }
    }

    public function createFakePraise($articleId, $fakePraise)
    {
        if ($fakePraise <= 0) {
            return;
        }

        if (!$model = ArticleFakePraise::findOne(['articleId' => $articleId])) {
            $model = new ArticleFakePraise();
            $model->articleId = $articleId;
        }
        $model->fakePraise = $fakePraise;
        $model->save();

        if ($model->errors) {
            Yii::error(json_encode($model->errors), __CLASS__ . "::" . __FUNCTION__ . "::" . __LINE__);
            throw new \yii\db\Exception("伪造点赞数失败");
        }
    }

    public function createParagraph($articleId, $params)
    {
        ParagraphModel::deleteAll(['articleId' => $articleId]);
        ParagraphContentModel::deleteAll(['articleId' => $articleId]);
        foreach ($params as $k => $v) {
            $model = new ParagraphModel;
            $model->articleId = $articleId;
            $model->orderId = intval($k) + 1;
            if(!$model->save()) {
                Yii::error($model->errors, __CLASS__."::".__FUNCTION__);
                throw new Exception("段落保存失败", RetCode::ERROR);
            }

            $this->createParagraphContent($articleId, $model->paragraphId, $v);
        }
    }

    public function createParagraphContent($articleId, $paragraphId, $params)
    {
        ParagraphContentModel::deleteAll(['paragraphId' => $paragraphId]);
        foreach ($params as $k => $v) {
            $model = new ParagraphContentModel;
            $model->orderId = $k + 1;
            $model->articleId = $articleId;
            $model->paragraphId = $paragraphId;
            $model->type = $v['type'];
            $model->content = $v['content'];
            $model->cTime = date('Y-m-d H:i:s');
            if(!$model->save()) {
                Yii::error($model->errors, __CLASS__."::".__FUNCTION__);
                throw new Exception("段落内容保存失败", RetCode::ERROR);
            }
        }

        return true;
    }

    public function filterArticle($params)
    {
        if(isset($params['article']) && $params['article'] ) {

            if(isset($params['article']['title']) && $params['article']['title']) {
                FilterService::text($params['article']['title']);
            }

            if(isset($params['article']['subTitle']) && $params['article']['subTitle']) {
                FilterService::text($params['article']['subTitle']);
            }

            if(isset($params['article']['summary']) && $params['article']['summary']) {
                FilterService::text($params['article']['summary']);
            }

            if(isset($params['article']['covers']) && $params['article']['covers']) {
                foreach ($params['article']['covers'] as $k => $v) {
                    if($v['type'] == 'image') {
                        FilterService::img($v['url']);
                    }
                }
            }

            $contentInfo = $params['contentInfo'] ?? [];
            foreach ($contentInfo as $k => $v) {
                foreach ($v as $kk => $vv) {
                    if($vv['type'] == 'image') {
                        FilterService::text($vv['content']);
                    } else if($vv['type'] = 'text') {
                        FilterService::img($vv['content']);
                    }
                }
            }
        }
    }

    public static function articleOldList($page, $size, $type, $params= [])
    {
        $list = ArticleModel::find();
        if($params) {
            if(isset($params['articleId']) && $params['articleId']) {
                $list->andWhere(['articleId' => $params['articleId']]);
                $list->orderBy([new \yii\db\Expression('FIELD (articleId, ' . implode(',', $params['articleId']) . ')')]);
            }
        }

        $list->andWhere(['deleteFlag' => 0]);
        $modelClone = clone $list;
        $total = (int)$modelClone->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => $size]);

        /** @var ArticleModel[] $article */
        $article = $list->offset($pages->offset)->limit($size)->all();
        $data = [];
        $ret = [];
        $ret['data'] = [];
        foreach ($article as $k => $v) {
            $data['article'] = $v->toArray();
            $data['article']['commentTotal'] = self::getCommentTotal($v);
            $data['article']['praiseTotal'] = self::getPraiseTotal($v);
            $data['article']['praiseStatus'] = self::getPraiseStatus($v);
            $data['author'] = UserService::detail($v->authorId);
            if($data['author']) {
                $data['author']['attentionStatus'] = self::getAttentionStatus($v);
            }

            $data['orderId'] = $v->orderId;
            $ret['data'][] = $data;
        }

        $ret['page'] = $page;
        $ret['size'] = $size;
        $ret['total'] = $total;
        return $ret;
    }

    public static function articleList($offset, $page, $size, $type, $params= [])
    {
        $list = ArticleModel::find();
        if($params) {
            if(isset($params['articleId']) && $params['articleId']) {
                $list->andWhere(['articleId' => $params['articleId']]);
                $list->orderBy([new \yii\db\Expression('FIELD (articleId, ' . implode(',', $params['articleId']) . ')')]);
            }
        }

        $list->andWhere(['deleteFlag' => 0]);
        $modelClone = clone $list;
        $total = (int)$modelClone->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => $size]);
        if(!$offset && $page) {
            $pages->setPage($page - 1);
            $offset = $pages->offset;
        }else {
            $page = intval($offset / $size) + 1;
        }

        /** @var ArticleModel[] $article */
        $article = $list->offset($offset)->limit($size)->all();
        $data = [];
        $ret = [];
        $ret['list'] = [];
        foreach ($article as $k => $v) {
            $data['article'] = $v->toArray();
            $data['article']['commentTotal'] = self::getCommentTotal($v);
            $data['article']['praiseTotal'] = self::getPraiseTotal($v);
            $data['article']['praiseStatus'] = self::getPraiseStatus($v);
            $data['author'] = UserService::detail($v->authorId);
            if($data['author']) {
                $data['author']['attentionStatus'] = self::getAttentionStatus($v);
            }

            $data['orderId'] = $v->orderId;
            $ret['list'][] = $data;
        }

        $ret['offset'] = $nexOffset = $offset + $size;
        $ret['hasMore'] = 1;
        if($nexOffset >= $total) {
            $ret['hasMore'] = 0;
        }
        $ret['page'] = $page;
        $ret['size'] = $size;
        $ret['total'] = $total;
        return $ret;
    }

    public static function articleListNew($offset, $page, $size, $type, $params= [])
    {
        $condition = [];
        $list = ArticleModel::find();
        if($type == 'my') {
            $myAttention = AuthorAttentionModel::find()->where(['uid' => Yii::$app->user->id])->all();
            if(!$myAttention) {
                $myAttentionAuthor = [];
            } else {
                $myAttentionAuthor = ArrayHelper::getColumn($myAttention, 'authorId');
            }
            
            //$list->andWhere(['in', 'authorId', $myAttentionAuthor]);
            $condition[]['andWhere'] = ['in', 'authorId', $myAttentionAuthor];
        } else if($type == 'myCreation') {
            $list->andWhere(['authorId' => Yii::$app->user->id]);
            $condition[]['andWhere'] = ['=', 'authorId', Yii::$app->user->id];
        }

        if($params) {
            if(isset($params['tagName']) && $params['tagName']) {
                $tagName = $params['tagName'];
                $articleIdArr = TagService::getArticleIdArr($tagName);
                //$list->andWhere(['in', 'articleId', $articleIdArr]);
                $condition[]['andWhere'] = ['in', 'articleId', $articleIdArr];
                unset($params['tagName']);
            }

            if(isset($params['articleIdArr'])) {
                if($params['articleIdArr']) {
                    $condition[]['andWhere'] = ['=', 'articleId', $params['articleIdArr']];
                } else {
                    $condition[]['andWhere'] = ['=', 'articleId', 0];
                }

                unset($params['articleIdArr']);
            }

            if(isset($params['type']) && $params['type']) {
                $condition[]['andWhere'] = ['=', 'type', $params['type']];
                unset($params['type']);
            }

            if(isset($params['genre']) && $params['genre']) {
                $condition[]['andWhere'] = ['=', 'genre', $params['genre']];
                unset($params['genre']);
            }

            if(isset($params['articleTitle']) && $params['articleTitle']) {
                //$list->andFilterWhere(['like', 'title', $params['articleTitle']]);
                $condition[]['andWhere'] = ['like', 'title', $params['articleTitle']];
                unset($params['articleTitle']);
            }

            if(isset($params['authorId']) && (int)$params['authorId']) {
                //$list->andWhere(['authorId' => $params['authorId']]);
                $condition[]['andWhere'] = ['=', 'authorId', $params['authorId']];
                unset($params['authorId']);
            }

            if(isset($params['startTime']) && $params['startTime']) {
                //$list->andWhere(['>=', 'cTime', date('Y-m-d H:i:s', (int)$params['startTime'])]);
                $condition[]['andWhere'] = ['>=', 'cTime', date('Y-m-d H:i:s', (int)$params['startTime'])];
                unset($params['startTime']);
            }


            if(isset($params['endTime']) && $params['endTime']) {
                //$list->andWhere(['<=', 'cTime', date('Y-m-d H:i:s', (int)$params['endTime'])]);
                $condition[]['andWhere'] = ['<=', 'cTime', date('Y-m-d H:i:s', (int)$params['endTime'])];
                unset($params['endTime']);
            }

            if(isset($params['praiseOrder']) && $params['praiseOrder']) {
                if($params['praiseOrder'] == 1) {
                    $condition[]['orderBy'] = 'priseTotal asc, orderId desc, timeOrder desc';
                }  else {
                    $condition[]['orderBy'] = 'priseTotal desc';
                }

                $praiseOrder = $params['praiseOrder'];
                unset($params['praiseOrder']);
            } else if(isset($params['commentOrder']) && $params['commentOrder']) {
                if($params['commentOrder'] == 1) {
                    $condition[]['orderBy'] = 'commentTotal asc';
                    //$list->orderBy('timeOrder asc');
                }  else {
                    $condition[]['orderBy'] = 'commentTotal desc';
                    //$list->orderBy('timeOrder desc');
                }

                $commentOrder = $params['commentOrder'];
                unset($params['commentOrder']);
            } else if(isset($params['timeOrder']) && $params['timeOrder']) {
                if($params['timeOrder'] == 1) {
                    $condition[]['orderBy'] = 'cTime asc';
                    //$list->orderBy('timeOrder asc');
                }  else {
                    $condition[]['orderBy'] = 'cTime desc';
                    //$list->orderBy('timeOrder desc');
                }

                $timeOrder = $params['timeOrder'];
                unset($params['timeOrder']);
            } else if(isset($params['orderId']) && $params['orderId']) {
                if($params['orderId'] == 1) {
                    //$list->orderBy('orderId desc');
                    $condition[]['orderBy'] = 'orderId asc';
                }  else {
                    //$list->orderBy('orderId asc');
                    $condition[]['orderBy'] = 'orderId desc';
                }
                $orderId = $params['orderId'];
                unset($params['orderId']);
            }

            foreach ($params as $k => $v) {
                if(is_array($v)) {
                    $condition[]['andWhere'] = ['in', $k, $v];
                } else {
                    $condition[]['andWhere'] = ['=', $k, $v];
                }
            }
        }

        //$list->andWhere(['deleteFlag' => 0]);
        $condition[]['andWhere'] = ['=', 'deleteFlag', 0];
        if($type == 'myCreation') {
            $condition[]['orderBy'] = 'cTime desc';
        } else if(!isset($timeOrder) && !isset($orderId) && !isset($praiseOrder) && !isset($commentOrder)) {
            $condition[]['orderBy'] = 'orderId desc, cTime desc';
        }

        $article = ArticleElasticModel::list($offset, $page, $size, $condition);
        $article['list'] = [];
        if($article['es']) {
            $modelAll = ArticleModel::find()->where(['articleId' => $article['data']]);
            $modelAll = $modelAll->orderBy([new \yii\db\Expression('FIELD (articleId, ' . implode(',', $article['data']) . ')')])->all();
            $userAll = ArrayHelper::getColumn($modelAll, 'authorId');
            $modelAll = ArrayHelper::index($modelAll, 'articleId');
            /** @var ArticleElasticModel[] $esList */
            $esList = $article['es'];

            $userAll = User::find()->where(['id' => $userAll])->all();
            $userAll = ArrayHelper::index($userAll, 'id');
            foreach ($esList as $k => $v) {
                $data['article'] = $modelAll[$v->articleId]->toArray();
//                $data['article']['commentTotal'] = self::getCommentTotal($modelAll[$v->articleId]);
//                $data['article']['praiseTotal'] = self::getPraiseTotal($modelAll[$v->articleId]);
                $data['article']['commentTotal'] = $v->commentTotal;
                $data['article']['praiseTotal'] = $v->priseTotal;
                $data['article']['fakePraise'] = self::getFakePraise($modelAll[$v->articleId]);
                $data['article']['praiseStatus'] = self::getPraiseStatus($modelAll[$v->articleId]);
                $data['author'] = $userAll[$v->authorId]->toArray();
                if($data['author']) {
                    $data['author']['attentionStatus'] = self::getAttentionStatus($modelAll[$v->articleId]);
                }

                $data['orderId'] = $v->orderId;
                $data['tag'] = Json::decode($v->tagNames, true);
                $article['list'][] = $data;
            }
        }

//        if($article['data']) {
//            foreach ($article['data'] as $k => $v) {
//                $articleOne = ArticleModel::findOne($v);
//                if(!$articleOne) {
//                    continue;
//                }
//
//                $data['article'] = $articleOne->toArray();
//                $data['article']['commentTotal'] = ArticleElasticModel::findOne($v)->commentTotal;
//                $data['article']['praiseTotal'] = ArticleElasticModel::findOne($v)->priseTotal;
//                $data['article']['fakePraise'] = self::getFakePraise($articleOne);
//                $data['article']['praiseStatus'] = self::getPraiseStatus($articleOne);
//                $data['author'] = UserService::detail($articleOne->authorId);
//                if($data['author']) {
//                    $data['author']['attentionStatus'] = self::getAttentionStatus($articleOne);
//                }
//
//                $data['orderId'] = $articleOne->orderId;
//                $data['tag'] = TagService::tagAll($v);
//
//                $article['list'][] = $data;
//            }
//        }

        unset($article['data']);
        return $article;
    }

    public static function commentList($offset, $page, $size, $params= [])
    {
        $model = ArticleCommentModel::find()->where(['deleteFlag' => 0]);
        if(isset($params['articleId']) && $params['articleId']) {
            $model->andWhere(['articleId' => $params['articleId']]);
        }

        $modelClone = clone $model;
        $total = (int)$modelClone->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => $size]);
        if(!$offset && $page) {
            $pages->setPage($page - 1);
            $offset = $pages->offset;
        }else {
            $page = intval($offset / $size) + 1;
        }

        /** @var ArticleCommentModel[] $comment */
        $comment = $model->offset($offset)->limit($pages->pageSize)->all();
        $data = [];
        $ret = [];
        foreach ($comment as $k => $v)  {
            $data[] = self::commentDetail($v->commentId);
        }

        $ret['list'] = $data;
        $ret['offset'] = $nexOffset = $offset + $size;
        $ret['hasMore'] = 1;
        if($nexOffset >= $total) {
            $ret['hasMore'] = 0;
        }
        $ret['page'] = $page;
        $ret['size'] = $size;
        $ret['total'] = $total;
        return $ret;
    }

    public static function commentDetail($commentId)
    {
        $data = [];
        $model = ArticleCommentModel::findOne($commentId);
        if($model) {
            $data = $model->toArray();
            if($data['parentId']) {
                $data['parentAuthor'] = User::findOne($data['parentId']) ?? [];
            } else {
                $data['parentAuthor'] = [];
            }

            $data['article'] = ArticleModel::findOne($model->articleId) ?? [];
            $data['user'] = User::findOne($model->uid) ?? [];
        }

        return $data;
    }

    public static function commentDelete($commentId)
    {
        $model = ArticleCommentModel::findOne($commentId);
        if($model) {
            $model->deleteFlag = 1;
            return $model->save();
        }

        return false;
    }

    public static function getPraiseStatus(ArticleModel $article)
    {
        if (!Yii::$app->user->isGuest) {
            if (ArticlePraiseModel::find()->where("articleId = :articleId and uid = :uid", [
                ':articleId' => $article->articleId,
                ':uid' => Yii::$app->user->id
            ])->one()) {
                return true;
            }
        }

        return false;
    }

    public static function getAttentionStatus(ArticleModel $article)
    {
        if (!Yii::$app->user->isGuest) {
            if (AuthorAttentionModel::find()->where("authorId = :authorId and uid = :uid", [
                ':authorId' => $article->authorId,
                ':uid' => Yii::$app->user->id
            ])->one()) {
                return true;
            }
        }

        return false;
    }

    public static function getCommentTotal(ArticleModel $article)
    {
        return (int)ArticleCommentModel::find()->where("articleId = :articleId and deleteFlag = 0", [
            ":articleId" => $article->articleId
        ])->count();
    }

    public static function getPraiseTotal(ArticleModel $article) : int
    {
        $praiseNumber = (int)ArticlePraiseModel::find()->where("articleId = :articleId and deleteFlag = 0", [
            ":articleId" => $article->articleId
        ])->count();

        return $praiseNumber + self::getFakePraise($article);
    }

    public static function getFakePraise(ArticleModel $article) : int
    {
        return (int)ArticleFakePraise::find()->where("articleId = :articleId", [
            ":articleId" => $article->articleId
        ])->sum('fakePraise');
    }

}