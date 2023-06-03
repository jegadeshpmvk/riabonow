<?php

namespace app\components;

use Yii;
use yii\helpers\Url;
use app\models\CustomPage;
use app\models\OfferCategory;
use app\models\Offer;

class UtilityFunctions extends \yii\base\Component {
    /*     * * 
      DISPLAY STATE AS HTML
     * * */

    public function notification($data, $param = "message", $class = "class") {
        if (isset($data[$param]) && $data[$param] != "")
            return '<div class="message message-' . $data[$class] . '"><p>' . $data[$param] . '</p></div>';

        return "";
    }

    public function state($value = false) {
        $list = [
            0 => 'Draft',
            1 => 'Published'
        ];

        $cls = [
            0 => 'fa-pencil-square',
            1 => 'fa-check-circle',
        ];

        if ($value === false)
            return [$list, $cls];

        $html = '<div class="state-color c' . $value . '">';
        $html .= '<span>' . $list[$value] . '</span>';
        $html .= '</div>';

        return $html;
    }

    /*     * * 
      CHECK IF ALL ATTRIBUTES OF A MODEL ARE EMPTY
     * * */

    public function isEmpty($model) {
        $empty = true;
        foreach ($model->attributes() as $attr) {
            if (in_array($attr, ["_id", "state", "deleted", "type"]))
                continue;

            if (trim($model->{$attr}) != "") {
                $empty = false;
                break;
            }
        }

        return $empty;
    }

    public function checkEmailLinks($email) {
        return preg_replace('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})/', 'mailto:$1', $email);
    }

    /*     * * 
      LIMIT TEXT
     * * */

    public function limit_text($text, $limit = 30) {
        $text = strip_tags($text);
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . '...';
        }
        return $text;
    }

}
