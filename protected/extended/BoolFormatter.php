<?php

namespace app\extended;

class BoolFormatter extends \yii\i18n\Formatter
{
    public function asBool($value)
    {
        if((int) $value)
            return 'Yes';

        return 'No';
    }
}