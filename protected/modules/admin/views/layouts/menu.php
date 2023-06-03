<?php

use yii\helpers\Html;

$menu = Yii::$app->request->get('tab', false);
if ($menu !== false)
    $tab = $menu;
else
    $tab = isset(Yii::$app->controller->tab) ? Yii::$app->controller->tab : '';
?>
<ul class="nav">
    <li<?php if ($tab == 'refresh') echo ' class="active"'; ?>><?= Html::a('<span>Refresh</span>', ['refresh/index'], ['class' => 'fa fa-address-book']) ?></li>
     <li<?php if ($tab == 'option-chain') echo ' class="active"'; ?>><?= Html::a('<span>Option Chain</span>', ['option-chain/index'], ['class' => 'fa fa-address-book']) ?></li>
     <li<?php if ($tab == 'chart') echo ' class="active"'; ?>><?= Html::a('<span>Chart</span>', ['chart/index'], ['class' => 'fa fa-address-book']) ?></li>
</ul>
<ul class="nav">
    <li<?php if ($tab == 'user') echo ' class="active"'; ?>><?= Html::a('<span>Admin</span>', ['user/index'], ['class' => 'fa fa-address-book']) ?></li>
        <li<?php if ($tab == 'settings') echo ' class="active"'; ?> title="Settings"><?= Html::a('<span>Settings</span>', ['user/settings'], ['class' => 'fa fa-cog']) ?></li>
            <li<?php if ($tab == 'cache') echo ' class="active"'; ?>><?= Html::a('<span>Clear Cache</span>', ['default/clear'], ['class' => 'fa fa-codiepie']) ?></li>
                <li title="Logout"><?= Html::a('<span>Logout</span>', ['default/logout'], ['class' => 'fa fa-sign-out', 'data-action' => '']) ?></li>
</ul>
<div class="panel_left_button"> <img src="<?= Yii::getAlias('@icons') ?>/logo.png" alt=""></div>