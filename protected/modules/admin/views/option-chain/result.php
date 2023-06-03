<div class="custom_option_headers">
     <?php 
        if(!empty($data)) {
            foreach($data as $k => $d) {
                echo '<div class="data_content_time">';
                    echo '<div class="data_content_main">Time</div>';
                    echo '<div class="data_content_header">';
                        echo '<div class="">1 Min</div>';
                    echo '</div>';
                    foreach($d as $dk => $res) {
                         echo '<div class="data_content_header data_content_body">';
                            echo '<div class="">'.(isset($res["time"]) ? date('h:i:s A', $res["time"]) : "").'</div>';
                        echo '</div>';
                    }
                echo '</div>';
                break;
            }
        }
    ?>
    </div>
    <div class="custom_option_body">
    <?php 
        if(!empty($data)) {
            foreach($data as $k => $d) {
                echo '<div class="data_content" title="'.$k.'">';
                    echo '<div class="data_content_main">'.$k.'</div>';
                    echo '<div class="data_content_header">';
                        echo '<div class="">CE OI CHG</div>';
                        echo '<div class="">CE OI</div>';
                        echo '<div class="">PE OI</div>';
                        echo '<div class="">PE OI CHG</div>';
                    echo '</div>';
                    foreach($d as $dk => $res) {
                         echo '<div class="data_content_header data_content_body">';
                            echo '<div class="">'.($res["ce_oi_change"] < 0 ? '<span class="bg_red_color">'.$res["ce_oi_change"].'</span>' : '<span class="bg_green_color">'.$res["ce_oi_change"].'</span>') .'</div>';
                            echo '<div class="">'.$res["ce_oi"].'</div>';
                            echo '<div class="">'.$res["pe_oi"].'</div>';
                            echo '<div class="">'.($res["pe_oi_change"] < 0 ? '<span class="bg_red_color">'.$res["pe_oi_change"].'</span>' : '<span class="bg_green_color">'.$res["pe_oi_change"].'</span>').'</div>';
                        echo '</div>';
                    }
                echo '</div>';
            }
        }
    ?>
      </div>
