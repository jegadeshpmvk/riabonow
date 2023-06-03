<?php

echo $form->field($model, $field)->radioList($list, [
    'class' => ['radio-list'],
    'item' => function($index, $label, $name, $checked, $value) {
        $status = $checked == 1 ? "checked" : "";
        $return = '<label class="has-radiobtn">';
        $return .= '<input type="radio" ' . $status . ' ' . (($status == '' && $index == 0) ? "checked" : "") . ' name="' . $name . '" value="' . $value . '">';
        $return .= '<span class="fa fa-circle-o"></span>';
        $return .= $label;
        $return .= '</label>';
        return $return;
    }], ['class' => 'radiobtn'])->label(false);
?>

