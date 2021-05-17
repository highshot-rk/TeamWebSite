<?php

function pp_custom_excerpt_length( $attribute ) {
   
    $pp_content_length = isset($attribute['content-length']) && !empty($attribute['content-length']) ? $attribute['content-length'] : 55;
    $read_more_button = isset($attribute['read-more-button']) && !empty($attribute['read-more-button']) ? $attribute['read-more-button'] : 'no';
    if($read_more_button=='no'){
        $read_m_btn='';
    }
    else{
        $read_more_text = isset($attribute['read-more-text']) && !empty($attribute['read-more-text'])? esc_attr($attribute['read-more-text']) : __('Read More','cool_process');
        $read_m_btn= '&hellip;<a class="read_more pp_read_more" href="' . get_permalink(get_the_ID()) . '">' .$read_more_text. '</a>';
	}
   				
   
    $post_content= wpautop( 
        // wp_trim_words() gets the first X words from a text string
        wp_trim_words(
            get_the_content(), // We'll use the post's content as our text string
            $pp_content_length, // We want the first 55 words
            $read_m_btn // This is what comes after the first 55 words
        )
    );
	
	return $post_content;
}
        
