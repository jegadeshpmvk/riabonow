<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\OptionChain;
use app\modules\admin\components\Controller;
use yii\web\NotFoundHttpException;

class OptionChainController extends Controller
{

    public $tab = "option-chain";


    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetData()
    {
        $from_strike =  Yii::$app->request->post('from_strike_price');
        $to_strike =  Yii::$app->request->post('to_strike_price');
        $expiry_date =  Yii::$app->request->post('expiry_date');
        $current_date =  Yii::$app->request->post('current_date');
        $type =  Yii::$app->request->post('options');
        $min =  Yii::$app->request->post('min');

        //$data = OptionChain::find()->andWhere(['type' => $type])->all();
        //->andWhere(['like', 'type', $type])->all();

        //$query = (new Query())->select(['*'])->from('option-chain')->where(['type' => $type]);
        $connection = Yii::$app->getDb();
        if($current_date !== '') {
        $command = $connection->createCommand('SELECT * FROM `option-chain` 
WHERE type= "' . $type . '" AND strike_price BETWEEN 
' . $from_strike . ' AND ' . $to_strike . ' AND expiry_date = "' . $expiry_date . '" 
AND MOD(TIMESTAMPDIFF(MINUTE,concat(DATE(FROM_UNIXTIME(`created_at`)), " ", "09:15:00"), FROM_UNIXTIME (created_at)), ' . $min . ') = 0
AND TIMESTAMPDIFF(MINUTE,concat(DATE(FROM_UNIXTIME(`created_at`)), " ", "09:16:00"), FROM_UNIXTIME (created_at)) >= 0
AND (CONVERT(DATE_FORMAT(FROM_UNIXTIME(`created_at`), "%H"), DECIMAL) >= 9)
    AND created_at BETWEEN 
    ' . strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $current_date)))) . ' AND 
    ' . strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $current_date)))));
        } else {
             $command = $connection->createCommand('SELECT * FROM `option-chain` 
WHERE type= "' . $type . '" AND strike_price BETWEEN 
' . $from_strike . ' AND ' . $to_strike . ' AND expiry_date = "' . $expiry_date . '" 
AND MOD(TIMESTAMPDIFF(MINUTE,concat(DATE(FROM_UNIXTIME(`created_at`)), " ", "09:15:00"), FROM_UNIXTIME (created_at)), ' . $min . ') = 0
AND TIMESTAMPDIFF(MINUTE,concat(DATE(FROM_UNIXTIME(`created_at`)), " ", "09:16:00"), FROM_UNIXTIME (created_at)) >= 0
AND (CONVERT(DATE_FORMAT(FROM_UNIXTIME(`created_at`), "%H"), DECIMAL) >= 9)');
        }
        // print_r('SELECT * FROM `option-chain` 
        // WHERE type= "' . $type . '" AND strike_price BETWEEN ' . $from_strike . ' AND ' . $to_strike . ' AND expiry_date = "' . $expiry_date . '" AND (CONVERT(DATE_FORMAT(FROM_UNIXTIME(`created_at`), "%i"), DECIMAL) % ' . $min . ') = 0 AND created_at BETWEEN ' . strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $current_date)))) . ' AND ' . strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $current_date)))));
        $result = $command->queryAll();
        $arr = [];
        if (!empty($result)) {
            foreach ($result as $k => $res) {
                $arr[$res['strike_price']][] = [
                    'time' => $res['created_at'],
                    'ce_oi_change' => $res['ce_oi_change'],
                    'ce_oi' => $res['ce_oi'],
                    'pe_oi' => $res['pe_oi'],
                    'pe_oi_change' => $res['pe_oi_change'],
                    'date_format' => date('d M H:i', $res['created_at'])
                ];
            }
        }
        $a = [
            'header' => $this->render('header', ['data' => $arr]),
            'body' => $this->render('body', ['data' => $arr]),
            'chart' => $arr
        ];
        echo json_encode($a);
        exit;
        //return $this->render('result', ['data' => $arr]);
    }
}
