<?php

use yii\helpers\Url;

$meta = Yii::$app->controller->meta;
$share = $meta['share'];
//Page title
echo '<title>' . ($meta['title'] == "" ? Yii::$app->name : \yii\helpers\Html::encode($meta['title'])) . '</title>';

//SEO meta tags
if ($meta['canonical'] != "")
    echo '<link rel="canonical" href="' . $meta['canonical'] . '">';
if ($meta['keywords'] != "")
    echo '<meta name="keywords" content="' . $meta['keywords'] . '" />';
if ($meta['description'] != "")
    echo '<meta name="description" content="' . $meta['description'] . '" />';
