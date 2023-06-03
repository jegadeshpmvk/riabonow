
<div class="option_chain_content">
    <form class="_form_row">
        <div class="form-group _form_col_4">
           <label class="control-label" for="option_current_date">Current Date</label>
            <input type="date" id="option_current_date" class="form-control" name="current_date" />
       </div>
       <div class="form-group _form_col_4">
           <label class="control-label" for="option_options_contracts">View Options Contracts for:</label>
           <select id="option_options_contracts" class="form-control" name="options_contracts">
               <option value="nifty">Nifty</option>
               <option value="nifty-bank">Bank Nifty</option>
           </select>
       </div>
       <div class="form-group _form_col_4">
           <label class="control-label" for="option_expiry_date">Expiry Date</label>
           <input type="date" id="option_expiry_date" class="form-control" name="expiry_date" />
       </div>
         <div class="form-group _form_col_4">
           <label class="control-label" for="from_strike_price">From Strike Price</label>
           <input type="number" id="from_strike_price" class="form-control" name="from_strike" />
       </div>
         <div class="form-group _form_col_4" style="margin-top:15px">
           <label class="control-label" for="to_strike_price">To Strike Price</label>
           <input type="number" id="to_strike_price" class="form-control" name="to_strike" />
       </div>
        <div class="form-group _form_col_4" style="margin-top:15px">
           <label class="control-label" for="option_options_minute">Minutes</label>
           <select id="option_options_minute" class="form-control" name="options_minute">
               <option value="5">5 Minutes</option>
               <option value="10">10 Minutes</option>
               <option value="15">15 Minutes</option>
               <option value="30">30 Minutes</option>
               <option value="60">60 Minutes</option>
           </select>
       </div>
    </form>
</div>
<div class="chart" id="chart"></div>
<div class="chart" id="chart_pe"></div>
