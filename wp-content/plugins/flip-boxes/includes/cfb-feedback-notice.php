<?php
/*
|--------------------------------------------------------------|
|   Admin Side Plugin Review Notice - CoolPlugins.net          |
|--------------------------------------------------------------|
*/
if (!class_exists('cfb_CoolPlugins_Review_Notice')) {
    class cfb_CoolPlugins_Review_Notice {

        private $plugin_url = CFB_URL;
        private $plugin_name = 'Flip Boxes';
        private $plugin_slug = 'cfb';
        private $review_option = 'Flip-Boxes-ratingDiv';
        private $installation_date_option = 'Flip-Boxes-installDate';
        private $review_link = 'https://wordpress.org/support/plugin/flip-boxes/reviews/#new-post';
        private $plugin_logo = 'assets/images/flipboxes-logo.png';
        private $buy_link = ''; //leave it blank if you don't want to show buy pro button

        /**
         * The Constructor
         */
        public function __construct() {
            // register actions   
            if(is_admin()){
                add_action('admin_notices', array($this, 'cool_admin_notice_for_review'));
                add_action('wp_ajax_'.$this->plugin_slug.'_dismiss_notice', array($this, 'cool_dismiss_review_notice'));
            }
        }

        // ajax callback for review notice
        public function cool_dismiss_review_notice(){
            $rs = update_option($this->review_option,'yes');
            echo json_encode(array("success"=>"true"));
            exit;
        }
        
        // admin notice
        public function cool_admin_notice_for_review(){
            if(!current_user_can('update_plugins')){
                return;
            }

            // get installation dates and rated settings
            $installation_date = get_option($this->installation_date_option);
            $alreadyRated = get_option($this->review_option)!=false?get_option($this->review_option):"no";
        
            // check user already rated 
            if($alreadyRated == "yes") {
                return;
            }

            // grab plugin installation date and compare it with current date
            $display_date = date('Y-m-d h:i:s');
            $install_date = new DateTime($installation_date);
            $current_date = new DateTime($display_date);
            $difference = $install_date->diff($current_date);
            $diff_days= $difference->days;
        
            // check if installation days is greator then week
            if (isset($diff_days) && $diff_days>=3) {
                echo $this->cool_create_notice_content();
            }
        }  

        // generated review notice HTML
        function cool_create_notice_content(){
            $plugin_buy_button = '';
            if($this->buy_link != ''){
                $plugin_buy_button = '<li><a href="'.$this->buy_link.'" target="_blank" class="buy-pro-btn button button-secondary" title="Buy Pro">Buy Pro</a></li>';
            }
            
            $html='
            <div data-ajax-url="'.admin_url('admin-ajax.php').'" data-ajax-callback="'.$this->plugin_slug.'_dismiss_notice" class="'.$this->plugin_slug.'-review-notice-wrapper notice">
                <div class="logo_container">
                    <a href="'.esc_url($this->review_link).'" target="_blank"><img src="'.$this->plugin_url.$this->plugin_logo.'" alt="'.$this->plugin_name.'"></a>
                </div>
                <div class="message_container">
                    <p>Thanks for using <b>'.$this->plugin_name.'</b> WordPress plugin. We hope it meets your expectations!<br/>Please give us a quick rating, it works as a boost for us to keep working on more <a href="https://coolplugins.net" target="_blank"><strong>Cool Plugins</strong></a>!</p>
                    <ul>
                        <li><a href="'.esc_url($this->review_link).'" class="rate-it-btn button button-primary" target="_blank" title="Submit A Review...">Rate Now! ★★★★★</a></li>
                        <li><a href="javascript:void(0);" class="already-rated-btn button button-secondary '.$this->plugin_slug.'_dismiss_notice" title="Already Rated - Close This Notice!">Already Rated</a></li>
                        <li><a href="javascript:void(0);" class="already-rated-btn button button-secondary '.$this->plugin_slug.'_dismiss_notice" title="Not Interested - Close This Notice!">Not Interested</a></li>
                        '.$plugin_buy_button.'
                    </ul>
                </div>
            </div>
            ';

            // css styles
            $style='
            <style>
            #wpbody .'.$this->plugin_slug.'-review-notice-wrapper.notice {
                padding: 5px;
                margin: 5px 0;
                display: table;
                max-width: 820px;
                border-radius: 5px;
                border: 1px solid #ced3d6;
                box-sizing: border-box;
                box-shadow: 2px 4px 8px -2px rgba(0, 0, 0, 0.1)
            }
            .'.$this->plugin_slug.'-review-notice-wrapper .logo_container {
                width: 80px;
                display: table-cell;
                padding: 5px;
                vertical-align: middle;
            }
            .'.$this->plugin_slug.'-review-notice-wrapper .logo_container a,
            .'.$this->plugin_slug.'-review-notice-wrapper .logo_container img {
                width:80px;
                height:auto;
                display:inline-block;
            }
            .'.$this->plugin_slug.'-review-notice-wrapper .message_container {
                display: table-cell;
                padding: 5px;
                vertical-align: middle;
            }
            .'.$this->plugin_slug.'-review-notice-wrapper p,
            .'.$this->plugin_slug.'-review-notice-wrapper ul {
                padding: 0;
                margin: 0;
                line-height: 1.25em;
                display: flow-root;
            }
            .'.$this->plugin_slug.'-review-notice-wrapper ul {
                margin-top: 10px;
            }
            .'.$this->plugin_slug.'-review-notice-wrapper ul li {
                float: left;
                margin: 0px 10px 0 0;
            }
            .'.$this->plugin_slug.'-review-notice-wrapper ul li .button-primary {
                background: #ffe37d;
                text-shadow: none;
                border-color: #a69516;
                box-shadow: none;
                color: #403906;
            }
            .'.$this->plugin_slug.'-review-notice-wrapper ul li .button-secondary {
                background: #fff;
                background-color: #fff;
                border: 1px solid #757575;
                color: #757575;
            }
            .'.$this->plugin_slug.'-review-notice-wrapper ul li .button-secondary.already-rated-btn:after {
                color: #f12945;
                content: "\f153";
                display: inline-block;
                vertical-align: middle;
                margin: -1px 0 0 5px;
                font-size: 14px;
                line-height: 14px;
                font-family: dashicons;
            }
            .'.$this->plugin_slug.'-review-notice-wrapper ul li .button-primary:hover {
                background: #222;
                border-color: #000;
            }
            @media screen and (max-width: 660px) {
                .'.$this->plugin_slug.'-review-notice-wrapper .logo_container{
                    display:none;
                }
                .'.$this->plugin_slug.'-review-notice-wrapper .message_container {
                    display: flow-root;
                }
            }
            </style>
            ';

            // close notice script
            $script='
            <script>
            jQuery(document).ready(function ($) {
                $(".'.$this->plugin_slug.'_dismiss_notice").on("click", function (event) {
                    var $this = $(this);
                    var wrapper=$this.parents(".'.$this->plugin_slug.'-review-notice-wrapper");
                    var ajaxURL=wrapper.data("ajax-url");
                    var ajaxCallback=wrapper.data("ajax-callback");         
                    $.post(ajaxURL, { "action":ajaxCallback }, function( data ) {
                        wrapper.slideUp("fast");
                    }, "json");
                });
            });
            </script>
            ';

            $html .= '
            '.$style.'
            '.$script;

            return $html; 
        }
    }//class end
}