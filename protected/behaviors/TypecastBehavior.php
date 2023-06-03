<?php

namespace app\behaviors;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class TypecastBehavior extends Behavior {

    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'typecastFields',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'typecastFields',
        ];
    }

    public function typecastFields() {
        $owner = $this->owner;
        foreach ($owner->rules() as $idx => $rule) {
            if (isset($rule[0]) && isset($rule[1]) && in_array($rule[1], ['integer', 'string', 'number'])) {
                $rule[0] = $this->castArray($rule[0]);
                if ($rule[1] == "integer") {
                    foreach ($rule[0] as $r)
                        $owner->{$r} = (int) $owner->{$r};
                } else if ($rule[1] == "float" || $rule[1] == "number") {
                    foreach ($rule[0] as $r)
                        $owner->{$r} = (float) $owner->{$r};
                }
            }
        }
    }

    public function castArray($attr) {
        if (!array($attr))
            return (array) $attr;

        return $attr;
    }

    public function canGetProperty($name, $checkVars = true) {
        return true;
    }

    public function canSetProperty($name, $checkVars = true) {
        return true;
    }

}
