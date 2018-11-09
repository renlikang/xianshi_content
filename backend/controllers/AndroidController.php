<?php

namespace backend\controllers;

class AndroidController extends \yii\rest\Controller {
    public function actions()
    {
        return parent::actions();
    }

    public function actionIndex() {
        return [
            "I am an android.",

            "You can try following commands:",

            [
                "praise" => "Praise to article."
            ]
        ];
    }
}