<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\OptionChain;
use app\modules\admin\components\Controller;

class RefreshController extends Controller
{

    public $tab = "refresh";


    public function actionIndex()
    {      
        return $this->render('index');
    }
    
     public function actionGetNifty() {
        $date = Yii::$app->request->post('expiry_date');
         $options = Yii::$app->request->post('options');
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://groww.in/v1/api/option_chain_service/v1/option_chain/'.$options.'?expiry='.$date,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
        'Cookie: __cf_bm=qw475hWN2MX3st6Gs8JvspAtAsZ4YCJhdHhQUOC6QNo-1684042147-0-AVzOXMRaBgw7GSUqp/nY2C5tL21r5NrKPn3U5I6TPk5Ws6ZxZU/IMHvrciba/WjOLLUnmHRvIowXRld+oUk1WKA=; __cfruid=070d89563bc714373ffb8f573eb10717354acf70-1684042147; _cfuvid=U6WIyVka7oyfhAHoiW0ma3zdkTP9k2Fw4gapZzHqlXc-1684042147697-0-604800000'
        ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response);
        if(!empty($res) && !empty($res->optionChains)) {
            foreach($res->optionChains as $k => $option) {
                 $model = new OptionChain();
                 $model->type =  $options;
                 $model->strike_price =  @$option->strikePrice;
                 $model->expiry_date = $date;
                 if(isset($option->callOption)) {
                      $model->ce_oi =  @$option->callOption->openInterest;
                 }
                 if(isset($option->putOption)) {
                      $model->pe_oi =  @$option->putOption->openInterest;
                 }
                 $model->save();
            }
        }
        return true;
    }
    
    public function actionGetExpiryDate() {
        $date = date('Y-m-01');
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://groww.in/v1/api/option_chain_service/v1/option_chain/nifty?expiry='.$date,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
        'Cookie: __cf_bm=qw475hWN2MX3st6Gs8JvspAtAsZ4YCJhdHhQUOC6QNo-1684042147-0-AVzOXMRaBgw7GSUqp/nY2C5tL21r5NrKPn3U5I6TPk5Ws6ZxZU/IMHvrciba/WjOLLUnmHRvIowXRld+oUk1WKA=; __cfruid=070d89563bc714373ffb8f573eb10717354acf70-1684042147; _cfuvid=U6WIyVka7oyfhAHoiW0ma3zdkTP9k2Fw4gapZzHqlXc-1684042147697-0-604800000'
        ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return $response;
    }
}
