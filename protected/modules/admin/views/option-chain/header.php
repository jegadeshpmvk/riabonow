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