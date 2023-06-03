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
                    $a = $b = [];
                    foreach($d as $dk => $res) {
                         echo '<div class="data_content_header data_content_body">';
                         $ce_oi_change = $pe_oi_change = 0;
                            if(isset($d[$dk-1]["ce_oi"])) {
                                if($d[$dk-1]["ce_oi"] != $res['ce_oi']) {
                                    $ce_oi_change = $res['ce_oi'] - $d[$dk-1]["ce_oi"];
                                    $a = [$ce_oi_change];
                                } else {
                                    $ce_oi_change = !empty($a) ? $a[0] : 0;
                                }
                            }
                             if(isset($d[$dk-1]["pe_oi"])) {
                                if($d[$dk-1]["pe_oi"] != $res['pe_oi']) {
                                    $pe_oi_change = $res['pe_oi'] - $d[$dk-1]["pe_oi"];
                                    $b = [$pe_oi_change];
                                } else {
                                    $pe_oi_change = !empty($b) ? $b[0] : 0;
                                }
                            }
                            echo '<div class="">'.($ce_oi_change < 0 ? '<span class="bg_red_color">'.$ce_oi_change.'</span>' : '<span class="bg_green_color">'.$ce_oi_change.'</span>') .'</div>';
                            echo '<div class="">'.$res["ce_oi"].'</div>';
                            echo '<div class="">'.$res["pe_oi"].'</div>';
                            echo '<div class="">'.($pe_oi_change < 0 ? '<span class="bg_red_color">'.$pe_oi_change.'</span>' : '<span class="bg_green_color">'.$pe_oi_change.'</span>').'</div>';
                        echo '</div>';
                    }
                echo '</div>';
            }
        }
    ?>