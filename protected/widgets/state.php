<?php
$arr = Yii::$app->function->state();
$list = $arr[0];
$cls = $arr[1];
?>

<div class="col page-state">
    <?=
    $form->field($model, 'state', ['template' => "{label}\n<span class='page-state-group'>{input}</span>\n{error}"])->radioList($list, [
        'unselect' => NULL,
        'item' => function($index, $label, $name, $checked, $value) use($cls) {
            $return = '<input id="state-radio-' . $value . '" type="radio" name="' . $name . '" value=' . (int) $value . ($checked ? " checked='checked'" : "") . ' />';
            $return .= '<label class="state-radio" for="state-radio-' . $value . '">';
            $return .= '<i class="fa ' . $cls[$value] . '"></i>';
            $return .= '<span>' . $label . '</span>';
            $return .= '</label>';
            return $return;
        }
    ])->label('Status');
    ?>
</div>