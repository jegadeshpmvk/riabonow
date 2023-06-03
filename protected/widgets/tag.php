<?php
$attr = isset($attr) ? $attr : 'name';
foreach ($tags as $t) {
    ?>
    <div class="tag" id="<?= $hidden ?><?= $t->_id ?>">
        <?= $t->$attr ?>
        <input type="hidden" name="<?= $hidden ?>[]" value="<?= $t->_id ?>" />
        <a data-id="<?= $t->_id ?>" class="remove fa fa-times-circle"></a>
    </div>
<?php } ?>
<?= $dropDown ?>