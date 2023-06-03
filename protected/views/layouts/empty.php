<?php

use yii\widgets\Spaceless;

Spaceless::begin();
$this->beginPage();
$this->beginBody();

echo $content;

$this->endBody();
$this->endPage();
Spaceless::end();
?>