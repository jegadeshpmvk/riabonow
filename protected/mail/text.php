<?php

use yii\helpers\Url;

$html = '';
if ($title == 'Password Reset') {
    $link = Url::to(['reset-password', 'id' => $user->email_hash], true);
    $html .= 'Please activate your account by clicking on the link below.<br>';
    $html .= '<a href="' . $link . '">' . $link . '</a>';
} else if ($title == 'New Jobcard Creation') {
    $html .= 'Job card Has been ' . ($model->isNewRecord ? 'Created' : 'Updated') . '<br>';
    $html .= 'Job card id: ' . $model->id . '<br>';
    $html .= 'Customer Name: ' . $model->customer->username . '<br>';
    $html .= 'Box Name: ' . $model->box->name . '<br>';
    $html .= 'Quantity: ' . $model->qty . '<br>';
    $html .= 'Status: ' . $model->getStatus($model->state) . '<br>';
    $html .= 'Delivery Date: ' . $model->delivery_date . '<br>';
} else if ($title == 'New Box Creation') {
    $link = '/box/?id=' . $model->id; //Url::to(['box', 'id' => $model->id], true);
    $html .= 'New Box Has been Created. It is Waiting for your approval.<br>';
    $html .= 'Box id: ' . $model->id . '<br>';
    $html .= 'Box Name: ' . $model->name . '<br>';
    $html .= 'Box Link: <a href="' . $link . '">' . $link . '</a>';
}
echo $html;
