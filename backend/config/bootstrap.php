<?php
\yii\base\Event::on(\yii\rest\Controller::className(), \yii\rest\Controller::EVENT_BEFORE_ACTION, ['backend\event\Before', 'index']);
