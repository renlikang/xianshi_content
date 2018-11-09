<?php

$vendorDir = dirname(__DIR__);

return array (
  'yiisoft/yii2-swiftmailer' => 
  array (
    'name' => 'yiisoft/yii2-swiftmailer',
    'version' => '2.1.1.0',
    'alias' => 
    array (
      '@yii/swiftmailer' => $vendorDir . '/yiisoft/yii2-swiftmailer/src',
    ),
  ),
  'yiisoft/yii2-bootstrap' => 
  array (
    'name' => 'yiisoft/yii2-bootstrap',
    'version' => '2.0.8.0',
    'alias' => 
    array (
      '@yii/bootstrap' => $vendorDir . '/yiisoft/yii2-bootstrap/src',
    ),
  ),
  'yiisoft/yii2-debug' => 
  array (
    'name' => 'yiisoft/yii2-debug',
    'version' => '2.0.13.0',
    'alias' => 
    array (
      '@yii/debug' => $vendorDir . '/yiisoft/yii2-debug',
    ),
  ),
  'yiisoft/yii2-gii' => 
  array (
    'name' => 'yiisoft/yii2-gii',
    'version' => '2.0.7.0',
    'alias' => 
    array (
      '@yii/gii' => $vendorDir . '/yiisoft/yii2-gii/src',
    ),
  ),
  'yiisoft/yii2-faker' => 
  array (
    'name' => 'yiisoft/yii2-faker',
    'version' => '2.0.4.0',
    'alias' => 
    array (
      '@yii/faker' => $vendorDir . '/yiisoft/yii2-faker',
    ),
  ),
  'yiisoft/yii2-redis' => 
  array (
    'name' => 'yiisoft/yii2-redis',
    'version' => '2.0.8.0',
    'alias' => 
    array (
      '@yii/redis' => $vendorDir . '/yiisoft/yii2-redis/src',
    ),
  ),
  'fedemotta/yii2-aws-sdk' => 
  array (
    'name' => 'fedemotta/yii2-aws-sdk',
    'version' => '2.0.0.0',
    'alias' => 
    array (
      '@fedemotta/awssdk' => $vendorDir . '/fedemotta/yii2-aws-sdk',
    ),
  ),
  'yiisoft/yii2-queue' => 
  array (
    'name' => 'yiisoft/yii2-queue',
    'version' => '2.1.0.0',
    'alias' => 
    array (
      '@yii/queue' => $vendorDir . '/yiisoft/yii2-queue/src',
      '@yii/queue/amqp' => $vendorDir . '/yiisoft/yii2-queue/src/drivers/amqp',
      '@yii/queue/amqp_interop' => $vendorDir . '/yiisoft/yii2-queue/src/drivers/amqp_interop',
      '@yii/queue/beanstalk' => $vendorDir . '/yiisoft/yii2-queue/src/drivers/beanstalk',
      '@yii/queue/db' => $vendorDir . '/yiisoft/yii2-queue/src/drivers/db',
      '@yii/queue/file' => $vendorDir . '/yiisoft/yii2-queue/src/drivers/file',
      '@yii/queue/gearman' => $vendorDir . '/yiisoft/yii2-queue/src/drivers/gearman',
      '@yii/queue/redis' => $vendorDir . '/yiisoft/yii2-queue/src/drivers/redis',
      '@yii/queue/sync' => $vendorDir . '/yiisoft/yii2-queue/src/drivers/sync',
      '@yii/queue/sqs' => $vendorDir . '/yiisoft/yii2-queue/src/drivers/sqs',
    ),
  ),
  'linslin/yii2-curl' => 
  array (
    'name' => 'linslin/yii2-curl',
    'version' => '1.2.1.0',
    'alias' => 
    array (
      '@linslin/yii2/curl' => $vendorDir . '/linslin/yii2-curl',
    ),
  ),
  'notamedia/yii2-sentry' => 
  array (
    'name' => 'notamedia/yii2-sentry',
    'version' => '1.3.0.0',
    'alias' => 
    array (
      '@notamedia/sentry' => $vendorDir . '/notamedia/yii2-sentry/src',
    ),
  ),
  'filsh/yii2-oauth2-server' => 
  array (
    'name' => 'filsh/yii2-oauth2-server',
    'version' => '2.0.1.0',
    'alias' => 
    array (
      '@filsh/yii2/oauth2server' => $vendorDir . '/filsh/yii2-oauth2-server',
    ),
    'bootstrap' => 'filsh\\yii2\\oauth2server\\Bootstrap',
  ),
  'yiisoft/yii2-elasticsearch' => 
  array (
    'name' => 'yiisoft/yii2-elasticsearch',
    'version' => '2.0.5.0',
    'alias' => 
    array (
      '@yii/elasticsearch' => $vendorDir . '/yiisoft/yii2-elasticsearch',
    ),
  ),
);
