<?php
$layout_html = '';

$layout_html .= '<div class="flex-'.esc_attr($cols).' cfb-box-'.$i.' cfb-box-wrapper">
            <div class="flipbox-container cfb-'.$flip_layout.' cfb-flip" data-effect="'.esc_attr($effect).'" data-height="'.esc_attr($height).'" >
              <div class="flipbox-front-layout cfb-data">
                <div class="flipbox-img">'; 					
              if(!empty($flipbox_image)){
                  $layout_html .= '<img src="'.esc_attr($flipbox_image).'" alt="" />';
              }
              else{
                $layout_html .= '<img src="'.CFB_URL . 'assets/images/black-background.jpg">';
              }
              $layout_html .= '</div></div>
              <div class="flipbox-back-layout cfb-data" style="background:'.esc_attr($flipbox_color_scheme).'">
              <h4>'.esc_html($flipbox_title).'</h4>
                <p>'.$back_desc.'</p>';
                if($read_more_text!='' && $flipbox_url!=''){
                    $layout_html .= '<a target="'.esc_html($dynamic_target).'" href="'.esc_url($flipbox_url).'" style="color:'.esc_attr($flipbox_color_scheme).'" class="back-layout-btn">'.esc_html($read_more_text).'</a>';
                }
                $layout_html .= '</div>
            </div>
          </div>';

