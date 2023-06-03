<?php

namespace app\components;

use Yii;
use yii\web\UrlRuleInterface;
use yii\base\BaseObject;

class CustomPageUrlRule extends BaseObject implements UrlRuleInterface
{

    public function createUrl($manager, $route, $params)
    {
        if ($route === 'site/custom-page') {
            if (isset($params['url'])) {
                return $params['url'];
            }
        }
        return false;
    }

    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();
        $split = explode("/", $pathInfo);
        $count = count($split);
        $error = 0;
        $parent = $params['redirect'] = $params['model'] = $params['url'] = false;


        return false;
    }
}
