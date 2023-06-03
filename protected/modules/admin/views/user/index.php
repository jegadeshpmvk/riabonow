<?php

use yii\helpers\Html;
use app\extended\GridView;
?>
<div class="options">
    <?= Html::a('<span>Add New User</span>', ['user/create'], ['class' => 'fa fa-plus']) ?>
    <?= Html::a('<span>Search</span>', NULL, ['class' => 'fa fa-search']) ?>
</div>
<h1 class="page_title">Users</h1>
<?=
GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'username',
        ],
        [
            'attribute' => 'email'
        ],
        [
            'class' => 'app\extended\ActionColumn',
            'header' => 'Action',
            'contentOptions' => ['class' => 'grid-actions']
        ],
    ],
]);
?>
<?= $this->render('_search', ['model' => $searchModel]) ?>