<?php

$config = [
    'id' => 'basic',
    'name' => 'Riabonow',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => require __DIR__ . '/aliases.php',
    'timeZone' => 'Asia/Kolkata',
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\AdminModule',
        ],
    ],
    'language' => 'en',
    'sourceLanguage' => 'en',
    'components' => [
        'request' => [
            'class' => '\yii\web\Request',
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'assetManager' => [
            'linkAssets' => false
        ],
        'errorHandler' => [
            'errorAction' => 'error/index',
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => '@web/login',
            'on afterLogin' => function ($event) {
                \app\models\User::setLoginTime($event->identity);
            },
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.sendgrid.net',
                'username' => 'apikey',
                'password' => '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',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
        'email' => [
            'class' => 'app\components\Email'
        ],
        'file' => [
            'class' => 'app\components\FileUpload'
        ],
        'function' => [
            'class' => 'app\components\UtilityFunctions'
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                //Admin
                '<module:(admin)>' => '<module>/default/login',
                '<module:(admin)>/settings' => '<module>/user/settings',
                '<module:(admin)>/<action:(login|logout|password|forgot|reset-password|category-builder)>' => '<module>/default/<action>',
                '<module:(admin)>/sort/<modelname:.+>' => 'admin/default/sort',
                '<module:(admin)>/<controller(product-review)>/<product:.+>/<action>' => '<module>/<controller>/<action>',
                '<module:(admin)>/contents/<page:.+>' => '<module>/content/index',
                '<module:(admin)>/<controller:.+>/<id:.+>/<action(view|update|duplicate|password|show)>' => '<module>/<controller>/<action>',
                '<module:(admin)>/upload-content/<id>/<action>' => '<module>/media/<action>',
                [
                    'pattern' => '<module:(admin)>/<controller:.+>/delete', //All delete actions in admin
                    'route' => '<module>/<controller>/delete',
                    'defaults' => ['value' => 1],
                ],
                [
                    'pattern' => '<module:(admin)>/<controller:.+>/restore', //All restore actions in admin
                    'route' => '<module>/<controller>/delete',
                    'defaults' => ['value' => 0],
                ],
                [
                    'pattern' => '<module:(admin)>/<controller:.+>/remove', //All remove actions in admin
                    'route' => '<module>/<controller>/delete',
                    'defaults' => ['value' => -1],
                ],
                '<module:(admin)>/upload-content' => '<module>/media/index',
                '<module:(admin)>/media-content' => '<module>/media/media',
                '<module:(admin)>/upload-content/<action:.+>' => '<module>/media/<action>',
                '<module:(admin)>/<controller:.+>-content' => '<module>/<controller>/index', //Use singular for all model/controller name
                '<module:(admin)>/<controller:.+>/<action:.+>' => '<module>/<controller>/<action>',
                'supported-browsers' => 'error/upgrade',
                'media/image/<w:\d+>x<h:\d+>/<name>' => 'admin/upload/resize',
                '/' => 'site/index'
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@runtime/cache/web'
        ],
        'tcache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@runtime/cache/translation'
        ]
    ],
    'params' => [
        'salt' => 'xC420.l',
        'fromEmail' => 'jegadesh29@gmail.com',
        'toEmail' => 'jegadesh29@gmail.com',
    ]
];

include(__DIR__ . '/env.php');

return $config;
