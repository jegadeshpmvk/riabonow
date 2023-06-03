<?php
foreach (app\models\Media::find()->where(['folder' => $folder])->orderBy('id desc')->each(20) as $m) {
    $file = Yii::$app->file->getUrl($m, []);
    $width = ($m->type == 'image' && ($m->extension == 'jpg' || $m->extension == 'png' || $m->extension == 'jpeg')) ? round(140 * $m->width / $m->height) : 140;
    $response = [
        "id" => $m->id,
        "css_class" => "fa-file-" . $m->type . "-o",
        "link" => $file['file'],
        "thumb" => $file['thumb'],
        "status" => "success",
        "reason" => "",
        "updateLink" => \yii\helpers\Url::to(['default/image', 'id' => $m->id])
    ];
    ?>
    <div id="media<?= $m->id; ?>" class="item" data-response='<?= json_encode($response); ?>' data-fa="fa-file-<?= $m->type ?>-o">
        <?php if ($file['thumb'] == '') { ?>
            <i class="media fa <?= $response['css_class']; ?>"></i>
        <?php } ?>
        <img src="<?= $file['thumb']; ?>" width="<?= $width; ?>" height="140" />
        <span class="name"><?= $m->orgname; ?></span>
    </div>
<?php } ?>
