<?php
$layout_html = '';

 $layout_html .= '<div class="flex-'.esc_attr($cols).' cfb-box-'.$i.' cfb-box-wrapper">
            <div class="flipbox-container facebook-icon cfb-'.$flip_layout.' cfb-flip" data-effect="'.esc_attr($effect).'" data-height="'.esc_attr($height).'">
              <div class="flipbox-front-layout cfb-data"  style="background:'.esc_attr($flipbox_color_scheme).'">';
              if($flipbox_icon!=''){
                $layout_html .= '<div class="flipbox-icon flipbox-solid-icon" style="font-size:'.esc_attr($icon_size).'">
                  <i class="fa '.esc_attr($flipbox_icon).'"></i>
                </div>';
              }
              $layout_html .= '</div>
              <div class="flipbox-back-layout cfb-data" style="color:'.esc_attr($flipbox_color_scheme).'">
                <a target="'.esc_html($dynamic_target).'" href="'.esc_url($flipbox_url).'">';
                if($flipbox_icon!=''){
                    $layout_html .= '<div class="flipbox-icon flipbox-solid-icon" style="font-size:'.esc_attr($icon_size).';color:'.esc_attr($flipbox_color_scheme).'">
                      <i class="fa '.esc_attr($flipbox_icon).'"></i>
                    </div>';
                }

                $layout_html .= '</a>  
              </div>
            </div>
          </div>';

