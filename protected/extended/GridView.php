<?php

namespace app\extended;

use yii\helpers\Html;

class GridView extends \yii\grid\GridView {

    public function renderTableRow($model, $key, $index) {
        $cells = [];
        /* @var $column Column */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderDataCell($model, $key, $index);
        }
        if ($this->rowOptions instanceof Closure) {
            $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
        } else {
            $options = $this->rowOptions;
        }
        $options['data-key'] = is_array($key) ? json_encode($key) : (string) $key;
        if (isset($model->deleted)) {
            $options['class'] = isset($options['class']) ? $options['class'] . ' ' : '';
            $options['class'] .= 'd' . (int) $model->deleted;
        } else {
            $options['class'] = isset($options['class']) ? $options['class'] . ' ' : '';
            $options['class'] .= 'd0';
        }
        $options['data-sort'] = "items[]_" . (isset($model->id) ? $model->id : $model->code);
        return Html::tag('tr', implode('', $cells), $options);
    }

}
