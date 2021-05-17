<?php
$layout_html = '';


$layout_html .= '<div class="flex-'.esc_attr($cols).' cfb-box-'.$i.' cfb-box-wrapper">
            <div class="flipbox-container cfb-'.$flip_layout.' cfb-flip" data-effect="'.esc_attr($effect).'" data-height="'.esc_attr($height).'">
              <div class="flipbox-front-layout cfb-data">
                <div class="flipbox-image-content">
                  <div class="flipbox-image-top">';
                    if(!empty($flipbox_image)){
                        $layout_html .= '<img src="'.esc_attr($flipbox_image).'" alt="" />';
                    }
                    else 
                    {
                        $layout_html .= '<img src="'.CFB_URL . 'assets/images'.'/layout-4.png" alt="" />';
                    }
                    if($flipbox_icon!=''){
                    $layout_html .= '<div class="flip-icon-bototm flipbox-icon" style="font-size:'.esc_attr($icon_size).';border-color:'.esc_attr($flipbox_color_scheme).';color:'.esc_attr($flipbox_color_scheme).'">
                        <i class="fa '.esc_attr($flipbox_icon).'"></i>
                       </div>';
                    }
                    $layout_html .= '</div>
                  <div class="flipbox-img-content">
                    <h5 style="color:'.esc_attr($flipbox_color_scheme).'">'.esc_html($flipbox_title).'</h5>
                  </div>
                </div>
                </div>
              <div class="flipbox-back-layout cfb-data" style="background-color:'.esc_attr($flipbox_color_scheme).'">
                <p>'.$back_desc.'</p>';
                if($read_more_text!='' && $flipbox_url!=''){
                    $layout_html .= '<a target="'.esc_html($dynamic_target).'" href="'.esc_url($flipbox_url).'"  class="back-layout-btn" style="color:'.esc_attr($flipbox_color_scheme).'">'.esc_html($read_more_text).'</a>';
                }
                $layout_html .= '</div>
            </div>
          </div>';
