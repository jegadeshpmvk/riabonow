<?php

use yii\helpers\Url;

//Get the rules for this upload object
$rule = Yii::$app->file->getRules($name);
$filetypes = $rule['type'];

//Help the user by some text about the size/dimensions of file upload
$text = isset($text) ? $text : Yii::$app->file->getTextForUser($rule, $filetypes);

//Check if the $files variable is an array
$isArray = is_array($existing) ? true : false;

if ($isArray) { //Enable multiple file uploads & sorting
    $multiple = $sort = true;
    $medialist = $existing;
    $label = 'Upload Files';
    $relation_name = isset($relation) ? $relation : false;
} else { // Allow only single file upload
    $multiple = $sort = false;
    $medialist = array();
    if ($existing)
        $medialist[] = $existing;
    $label = 'Upload File';
}

if (!isset($hidden_id))
    $hidden_id = "";

//Browse existing files (Defaults to "true")
$browse = (isset($browse) && !$browse) ? false : true;

//Crop Button
$crop = isset($crop) ? $crop : false;

//Unique ID
$unique = uniqid();

//Create an upload instance for javascript reference
$instance = $name . '-' . $unique;

//Drag drop upload
$drag = isset($dragDrop) ? $dragDrop : false;
?>
<div class="file-upload-widget">
    <?php if ($text != '') { ?>
        <div class="user-upload-help"><?= $text; ?></div>
    <?php } ?>

    <a class="file-upload">
        <input type="file" class="upload" name="<?= $name; ?>" id="<?= $instance ?>-file-control" data-instance="<?= $instance; ?>" data-hidden="<?= $hidden; ?>"<?php if ($multiple) echo ' multiple="multiple"'; ?> <?php if (isset($required)) echo 'required=""' ?> />
        <label class="fa fa-upload" for="<?= $instance ?>-file-control"><?= $label; ?></label>
    </a><?php
    if ($browse) { //If browse is enabled, display the button
        ?><a class="browse-library fa fa-folder-open-o" href="<?= Url::to(['upload/browse', 'folder' => 'uploads']); ?>">Choose from Media Library</a><?php
    }
    if ($crop) { //If crop is enabled, display the button
        if (isset($crop->banner)) {
            $cropRec = $crop->banner;
            $cropFolder = "big";
        } else if (isset($crop->image)) {
            $cropRec = $crop->image;
            $cropFolder = "bg";
        }
        ?><a class="crop-from fa fa-folder-open-o" href="<?= Yii::$app->file->getUrl($cropRec, $cropFolder) ?>" target="_blank">Crop from Blog Image</a><?php } ?>
    <div class="<?php echo $name; ?>-uploaded-files list-of-files" data-for="<?= $name; ?>"<?php if ($sort) echo ' data-sort="yes"'; ?>>
        <?php
        foreach ($medialist as $m) {
            $file = ($isArray && $relation_name !== false) ? $m->$relation_name : $m;
            $fileurl = Yii::$app->file->getUrl($file, []);
            $fa = 'fa fa-file-' . $file->type . '-o';
            $hei = 134;
            $wid = $file->type == 'image' && ($file->extension == 'jpg' || $file->extension == 'png' || $file->extension == 'jpeg') ? round($hei * $file->width / $file->height) : 0;
            $link = $fileurl['thumb'];
            $orglink = $fileurl['file'];
            ?>
            <span class="media complete <?= $fa; ?>">
                <a href="<?= $orglink; ?>" class="file-name" target="_blank"><?php echo $file->orgname; ?></a>
                <?php if ($wid) { ?>
                    <img id="file_<?= $file->id; ?>" src="<?= $link; ?>" />
                <?php } else { ?>
                    <img id="file_<?= $file->id; ?>" src="<?= $orglink; ?>" />
                <?php } ?>
                <input class="media_id_ref_for_<?= $name; ?>" type="hidden" id="<?= $hidden_id; ?>" name="<?= $hidden; ?>" value="<?= $file->id; ?>" />
                <a class="delete-file"></a>
                <a class="fa fa-pencil edit-image" href="<?= Url::to(['default/image', 'id' => (string) $file->id]) ?>"></a>
            </span>
        <?php } ?>
    </div>
    <?php if ($drag) { ?>
        <div class="drag-drop">
            <div class="bg"></div>
            <div class="receiver">
                <div class="circle scale"></div>
                <div class="file file1"></div>
                <div class="file file2"></div>
                <div class="file file3"></div>
                <div class="file file4"></div>
                <div class="file file5"></div>
                <div class="text">
                    <div class="title">Incoming!</div>
                    <p>Drop your files to instantly upload them</p>
                </div>
            </div>
        </div>
    <?php } ?>

</div>
<?php
//Initialize file upload
$upload_script = '
	page.upload_object["' . $instance . '"] = $("#' . $instance . '-file-control").upload({
		uploadlink: "' . Url::to(['upload/file']) . '",
		multiple: ' . (int) $multiple . ', 
		formats: ' . json_encode(Yii::$app->file->getFormats($rule)) . ',
		sizelimit: ' . json_encode($rule['maxsize']) . '
	});';
$this->registerJs($upload_script);
?>