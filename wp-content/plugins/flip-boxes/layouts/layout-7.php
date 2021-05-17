<?php
$layout_html = '';
if($flipbox_image){
    $flipbox_image = $flipbox_image;
}
else 
{
    $flipbox_image = ''.CFB_URL.'assets/images/black-background.jpg';
}


$layout_html .= '<div class="flex-'.esc_attr($cols).' cfb-box-'.$i.' cfb-box-wrapper">
            <div class="flipbox-container cfb-'.$flip_layout.' cfb-flip" data-effect="'.esc_attr($effect).'" data-height="'.esc_attr($height).'">
              <div class="flipbox-front-layout flipbox-front-filled cfb-data" style="background:'.esc_attr($flipbox_color_scheme).'">
                
                <div class="flipbox-front-description">
                  <h4>'.esc_html($flipbox_title).'</h4>
                  <p>'.$front_desc.'</p>
                </div>
              </div>
              <div class="flipbox-back-layout flipbox-background-img cfb-data" style="background-image: url('.esc_attr($flipbox_image).');color:'.esc_attr($flipbox_color_scheme).'">';
                  if($flipbox_icon!=''){  
                    $layout_html .= '<div class="flipbox-icon flipbox-solid-icon" style="font-size:'.esc_attr($icon_size).'">
                      <i class="fa '.esc_attr($flipbox_icon).'"></i>
                    </div>';
                  }
              $layout_html .= '<p style="color:'.esc_attr($flipbox_color_scheme).'">'.$back_desc.'</p>';
              if($read_more_text!='' && $flipbox_url!=''){
                $layout_html .= '<a target="'.esc_html($dynamic_target).'" href="'.esc_url($flipbox_url).'" style="color:'.esc_attr($flipbox_color_scheme).'" class="back-layout-btn">'.esc_html($read_more_text).'</a>';
              }
              $layout_html .= '</div>
            </div>
          </div>';
