# Yii2 Elasticemail

![License](https://img.shields.io/packagist/l/cinghie/yii2-elasticemail.svg)
![Latest Stable Version](https://img.shields.io/github/release/cinghie/yii2-elasticemail.svg)
![Latest Release Date](https://img.shields.io/github/release-date/cinghie/yii2-elasticemail.svg)
![Latest Commit](https://img.shields.io/github/last-commit/cinghie/yii2-elasticemail.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/cinghie/yii2-elasticemail.svg)](https://packagist.org/packages/cinghie/yii2-elasticemail)


Yii2 Elasticemail extension to manage the Elastice Email Marketing Platform: 

- Website: https://www.elasticemail.com
- Documentation: https://api.elasticemail.com/public/help#start
- PHP SDK: https://github.com/ElasticEmail/ElasticEmail.WebApiClient-php
- Example: https://github.com/ElasticEmail/ElasticEmail.WebApiClient-php/blob/master/examples/example.php

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require cinghie/yii2-elasticemail "*"
```

or add

```
"cinghie/yii2-elasticemail": "*"
```

Configuration
-------------

Set on your configuration file

```

use cinghie\elasticemail\components\Elasticemail as ElasticemailComponent;

'components' => [ 
    
    'elasticemail' => [
        'class' => ElasticemailComponent::class,
        'apiUrl' => 'https://api.elasticemail.com/v2/',
        'apiKey' => 'YOUR_ELASTICEMAIL_API_KEY'
    ],
    
]
```

## Usage

```
\Yii::$app->elasticemail;
\Yii::$app->elasticemail->getClient();
\Yii::$app->elasticemail->getAccessTokens();
\Yii::$app->elasticemail->getAccount();
\Yii::$app->elasticemail->getCampaigns();
\Yii::$app->elasticemail->getChannels();
\Yii::$app->elasticemail->getContacts();
\Yii::$app->elasticemail->getDomains();
\Yii::$app->elasticemail->getEelists();
\Yii::$app->elasticemail->getTemplates();
```
