<?php

use yii\helpers\Html;
use app\extended\GridView;
?>
<div class="nifty_data" data-url="https://groww.in/v1/api/option_chain_service/v1/option_chain/nifty?expiry=<?php echo date('Y-m-01'); ?>"></div>
<form class="_form_row">
       <div class="form-group _form_col_4">
           <label class="control-label" for="options_contracts">View Options Contracts for:</label>
           <select id="options_contracts" class="form-control" name="options_contracts">
               <option value="nifty">Nifty</option>
               <option value="nifty-bank">Bank Nifty</option>
           </select>
       </div>
       <div class="form-group _form_col_4">
           <label class="control-label" for="expiry_date">Expiry Date</label>
           <select id="expiry_date" class="form-control" name="expiry_date">
               <option value="">Please select</option>
           </select>
       </div>
   </form>