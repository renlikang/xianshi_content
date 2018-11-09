<?php

namespace common\components;

use common\models\content\ArticleModel;
use common\models\content\Notices;
use yii\base\BaseObject;
use yii\db\Exception;

class NoticeJob extends BaseObject implements \yii\queue\JobInterface
{
    public $type;
    public $content;
    public $userId;

    public function execute($queue)
    {
        // 点赞需要通知文章作者
        // 评论需要通知文章作者和回复的人（如果有的话）
        // 关注需要通知被关注的人

        if ($this->type == 'like' || $this->type == 'comment')
        {
            $article = ArticleModel::findOne(['articleId' => $this->content['articleId']]);
            $this->saveNotice($article->authorId);
        }
        elseif ($this->type == 'follow') {
            $this->saveNotice($this->content['authorId']);
        }

        if ($this->type == 'comment' && $this->content['replayUid']) {
            $this->saveNotice($this->content['replayUid']);
        }
    }

    protected function saveNotice($userId) {
        $notice = new Notices();
        $notice->type = $this->type;
        $notice->content = $this->content;
        $notice->create_at = time();
        $notice->update_at = time();
        $notice->user_id = $userId;
        $notice->save();

        if ($notice->errors) {
            throw new Exception("保存通知失败");
        }
    }
}