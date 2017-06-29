<?php

class wooPsr {

    protected $option_name = 'woopsr';

//--Hooks     
    public function __construct() {
        $woospr = get_option('woopsr');
        add_filter('woocommerce_get_settings_pages', array($this, 'woopsr_settings_class'));
        if ($woospr['enabled'] == "yes") {
            if ($woospr['single_enable'] == "yes") {
                add_action('wp_enqueue_scripts', array($this, 'woopsr_scripts'));
                add_action('woocommerce_single_product_summary', array($this, 'woo_star_counts'));
            }
            if ($woospr['multi_enable'] == "yes") {
                add_action('wp_enqueue_scripts', array($this, 'woopsr_scripts'));
                add_action('woocommerce_after_shop_loop_item_title', array($this, 'woo_star_counts'));
            }
            if ($woospr['cmnt_enabled'] == "yes") {
                add_action('wp_ajax_comment_helpful', array($this, 'comment_helpful'));
                add_action('wp_ajax_nopriv_comment_helpful', array($this, 'comment_helpful'));
                add_action('woocommerce_review_comment_text', array($this, 'woocommerce_review_display_comment_text'), 100);
            }
            if ($woospr['reviews_single_enable'] == "yes") {
                add_filter('woocommerce_product_tabs', array($this, 'remove_review_tab'), 98);
                add_action('woocommerce_product_tabs', array($this, 'woo_new_product_tab'), 100);
            }
        }
        //Check if woocommerce plugin is installed.
        add_action('admin_notices', array($this, 'check_required_plugins'));
        add_filter("plugin_action_links_" . WOOPSR_BASE, array($this, 'add_action_links'));
        add_action('wp_ajax_show_all_rating', array($this, 'show_all_rating'));
        add_action('wp_ajax_nopriv_show_all_rating', array($this, 'show_all_rating'));
        add_action('wp_enqueue_scripts', array($this, 'star_rating_styles_method'));
        add_action('wp_enqueue_scripts', array($this, 'star_rating_styles_method'));
    }

//--Scripts & stylesheets 
    public function woopsr_scripts() {
        wp_enqueue_style('custom-star-rating-css', plugin_dir_url(__FILE__) . 'css/custom-star-rating.css');
        wp_enqueue_style('custom-animation', plugin_dir_url(__FILE__) . 'css/animated.css');
        wp_enqueue_style('custom-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
        wp_enqueue_script('custom-star-rating-js', plugin_dir_url(__FILE__) . ('js/custom-star-rating.js'), array('jquery'), '1.0.0', true);
        wp_enqueue_script('hoverintent-js', plugin_dir_url(__FILE__) . ('js/jquery.hoverIntent.js'), array('jquery'), '1.0.0', true);
        wp_localize_script('custom-star-rating-js', 'StarCount', array('ajaxUrl' => admin_url('admin-ajax.php')));
    }

//Check if woocommerce is installed and activated
    public function check_required_plugins() {
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            ?>
            <div id="message" class="error">
                <p>Woo Star Count requires <a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a> to be activated in order to work. Please install and activate <a href="<?php echo admin_url('/plugin-install.php?tab=search&amp;type=term&amp;s=WooCommerce'); ?>" target="">WooCommerce</a> first.</p>
            </div>
            <?php
            deactivate_plugins('/woo-star-count/starcount.php');
        }
    }

//--Settings link on plugins page
    public function add_action_links($links) {
        array_unshift($links, '<a href="' . admin_url('admin.php?page=wc-settings&tab=woopsr') . '">Settings</a>');
        return $links;
    }

//--Update like or dislike
    public function comment_helpful() {
        global $wpdb;
        if (!is_user_logged_in()) {
            echo json_encode(['status' => "login_issue", 'url' => wp_login_url()]);
            exit;
        }
        $comment_id = $_POST['commentId'];
        $cmnt_status = $_POST['commentStatus'];
        $user_id = get_current_user_id();
        update_comment_meta($comment_id, "mg-cmnt-hlp-userid-$user_id", $cmnt_status);
        $likeCount = $wpdb->get_var("SELECT COUNT(*) meta_value FROM $wpdb->commentmeta WHERE meta_key LIKE '%mg-cmnt-hlp-userid-%' AND meta_value = 1 AND comment_id = $comment_id");
        echo $likeCount;
        exit;
    }

//--Label for like and dislike
    public function woocommerce_review_display_comment_text() {
        global $wpdb;
        $woospr = get_option('woopsr');
        $comment_id = get_comment_ID();
        $user_id = get_current_user_id();

        $likeCount = $wpdb->get_var("SELECT COUNT(*) meta_value FROM $wpdb->commentmeta WHERE meta_key LIKE '%mg-cmnt-hlp-userid-%' AND meta_value = 1 AND comment_id = $comment_id");

        $activeCheck = $wpdb->get_var("SELECT COUNT(*) meta_value FROM $wpdb->commentmeta WHERE meta_key ='mg-cmnt-hlp-userid-$user_id' AND meta_value = 1 AND comment_id = $comment_id");

        $inactiveCheck = $wpdb->get_var("SELECT COUNT(*) meta_value FROM $wpdb->commentmeta WHERE meta_key ='mg-cmnt-hlp-userid-$user_id' AND meta_value = 0 AND comment_id = $comment_id");

        $class = $class2 = "";
        if ($activeCheck == 1) {
            $class = "mg-active";
        }
        if ($inactiveCheck == 1) {
            $class2 = "mg-active";
        }

        $woo_cmnt_str = str_replace('{count}', "<span class='likeid'>$likeCount</span>", $woospr['woo_cmnt']);

        echo "<div class='cmnt-last'> $woo_cmnt_str <button class='mg-cmnt-like $class' type='button' commentId='$comment_id' value='1'>Yes</button> <button class='mg-cmnt-unlike $class2' commentId='$comment_id' value='0'>No</button></div>";
    }

//--Remove the review tab
    public function remove_review_tab($tabs) {
        unset($tabs['reviews']);
        return $tabs;
    }

//--Add new tab
    public function woo_new_product_tab($tabs) {
        global $product;
        $reviewCount = $product->get_review_count();
        $tabs['reviews'] = array(
            'title' => __("Reviews ($reviewCount)", 'woocommerce'),
            'priority' => 50,
            'callback' => array($this, 'woo_new_product_tab_content')
        );
        return $tabs;
    }

//--New tab content
    public function woo_new_product_tab_content() {
        global $wpdb;
        global $post;
        $link = get_permalink($post->ID);
        $product = wc_get_product($post->ID);
        $rating_count = $product->get_rating_count();
        $average = $product->get_average_rating();
        $num = 5;
        $allcount = array();
        while ($num > 0) {
            $allcount[] = $wpdb->get_var("SELECT COUNT(*) meta_value FROM $wpdb->commentmeta LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id =$wpdb->comments.comment_ID WHERE meta_key = 'rating' AND comment_post_ID =  $post->ID AND comment_approved = '1' AND meta_value = $num");
            $num = $num - 1;
        }

        if ($rating_count > 0) {
            $fifthPercent = ($allcount[0] / $rating_count) * 100;
            $fourthPercent = ($allcount[1] / $rating_count) * 100;
            $thirdPercent = ($allcount[2] / $rating_count) * 100;
            $secondPercent = ($allcount[3] / $rating_count) * 100;
            $firstPercent = ($allcount[4] / $rating_count) * 100;
            $ratedCount = (5 / $rating_count) * 100;

            $fifthTitle = round($fifthPercent) != 0 ? round($fifthPercent) . "% of reviews have 5 stars" : "";
            $fourthTitle = round($fourthPercent) != 0 ? round($fourthPercent) . "% of reviews have 4 stars" : "";
            $thirdTitle = round($thirdPercent) != 0 ? round($thirdPercent) . "% of reviews have 3 stars" : "";
            $secondTitle = round($secondPercent) != 0 ? round($secondPercent) . "% of reviews have 2 stars" : "";
            $firstTitle = round($firstPercent) != 0 ? round($firstPercent) . "% of reviews have 1 star" : "";

            $rating_html = "";
            $rating_html .= '<div id="big-page-wrap" class="big-page-wrap">
    <section>
        <span class="big-wstar-text">Rated  ' . $average . '  out of 5</span>
    </section>
    <section>
        <span class="big-wstar">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
        <div title="' . $fifthTitle . '" class="big-wstar-progress-bar"><span style="width: ' . $fifthPercent . '%"></span></div>
        <span class="big-wstar-num">' . $allcount[0] . '</span>
    </section>
    <section>
        <span class="big-wstar">&#9733;&#9733;&#9733;&#9733;</span>
        <div title="' . $fourthTitle . '" class="big-wstar-progress-bar"><span style="width: ' . $fourthPercent . '%"></span></div>
        <span class="big-wstar-num">' . $allcount[1] . '</span>
    </section>
    <section>
        <span class="big-wstar">&#9733;&#9733;&#9733;</span>
        <div title="' . $thirdTitle . '" class="big-wstar-progress-bar"><span style="width: ' . $thirdPercent . '%"></span></div>
        <span class="big-wstar-num">' . $allcount[2] . '</span>
    </section>
    <section>
        <span class="big-wstar">&#9733;&#9733;</span>
        <div title="' . $secondTitle . '" class="big-wstar-progress-bar"><span style="width: ' . $secondPercent . '%"></span></div>
        <span class="big-wstar-num">' . $allcount[3] . '</span>
    </section>
    <section>
        <span class="big-wstar">&#9733;</span>
        <div title="' . $firstTitle . '" class="big-wstar-progress-bar"><span style="width: ' . $firstPercent . '%"></span></div>
        <span class="big-wstar-num">' . $allcount[4] . '</span>
    </section>
    <section>
        <span class="big-wstar-review"><a class="woocommerce-review-link" href="' . $link . '#reviews" rel="nofollow">See all ' . $rating_count . ' reviews</a></span>
    </section>
</div>';
            echo $rating_html;
        }
        comments_template();
    }

//--Posting id through hidden field
    public function woo_star_counts() {
        global $post;
        echo '<input type="hidden" class="pid" value="' . $post->ID . '">';
    }

//--Include the class-wc-settings-star-count.php file
    public function woopsr_settings_class($settings) {
        $settings[] = include 'class-wc-settings-star-count.php';
        return $settings;
    }

//--Ajax request for woo-star-count popup
    public function show_all_rating() {
        $woospr = get_option('woopsr');
        global $wpdb;
        $id = $_POST['id'];
        $link = get_permalink($id);
        $product = wc_get_product($id);
        $rating_count = $product->get_rating_count();
        $average = $product->get_average_rating();
        $num = 5;
        $allcount = array();
        while ($num > 0) {
            $allcount[] = $wpdb->get_var("SELECT COUNT(*) meta_value FROM $wpdb->commentmeta LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID WHERE meta_key = 'rating' AND comment_post_ID = $id AND comment_approved = '1' AND meta_value = $num");
            $num = $num - 1;
        }
        if ($rating_count > 0) {
            $fifthPercent = ($allcount[0] / $rating_count) * 100;
            $fourthPercent = ($allcount[1] / $rating_count) * 100;
            $thirdPercent = ($allcount[2] / $rating_count) * 100;
            $secondPercent = ($allcount[3] / $rating_count) * 100;
            $firstPercent = ($allcount[4] / $rating_count) * 100;
            $ratedCount = (5 / $rating_count) * 100;
            $rating_html = "";
            $rating_html .= '<div id="page-wrap" class="page-wrap animated">
                            <section>
                                <span class="wstar-text arrow-up">Rated  ' . $average . '  out of 5</span>
                            </section>
                            <section>
                                <span class="wstar">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                                <span class="wstar-progress-bar"><span style="width: ' . $fifthPercent . '%"></span></span>
                                <span class="wstar-num">' . $allcount[0] . '</span>
                            </section>
                            <section>
                                <span class="wstar">&#9733;&#9733;&#9733;&#9733;</span>
                                <div class="wstar-progress-bar"><span style="width: ' . $fourthPercent . '%"></span></div>
                                <span class="wstar-num">' . $allcount[1] . '</span>
                            </section>
                            <section>
                                <span class="wstar">&#9733;&#9733;&#9733;</span>
                                <div class="wstar-progress-bar"><span style="width: ' . $thirdPercent . '%"></span></div>
                                <span class="wstar-num">' . $allcount[2] . '</span>
                            </section>
                            <section>
                                <span class="wstar">&#9733;&#9733;</span>
                                <div class="wstar-progress-bar"><span style="width: ' . $secondPercent . '%"></span></div>
                                <span class="wstar-num">' . $allcount[3] . '</span>
                            </section>
                            <section>
                                <span class="wstar">&#9733;</span>
                                <div class="wstar-progress-bar"><span style="width: ' . $firstPercent . '%"></span></div>
                                <span class="wstar-num">' . $allcount[4] . '</span>
                            </section>
                            <section>
                                <span class="wstar-review"><a class="woocommerce-review-link" href="' . $link . '#reviews" rel="nofollow">See all ' . $rating_count . ' reviews</a></span>
                            </section>
</div>';
            echo $rating_html;
        }
        exit;
    }

//--frontend style css
    public function star_rating_styles_method() {
        wp_enqueue_style('custom-style', plugin_dir_url(__FILE__) . 'css/custom-star-rating.css');
        $woospr = get_option('woopsr');
        $bgcolor = $woospr['bg_color']; //E.g. #FF0000
        $txtcolor = $woospr['text_color'];
        $firstcolor = $woospr['first_color'];
        $secondcolor = $woospr['sec_color'];
        $hovercolor = $woospr['hover_color'];
        $wooBoxWidth = $woospr['review_box_width'];
        $wooBtnBgCol = $woospr['btn_bg_color'];
        $wooBtnActiveCol = $woospr['btn_active_color'];
        $wooBtnFont = $woospr['btn_font_size'];
        $wooBtnClr = $woospr['btn_txt_color'];
        $reviewsfirstcolor = $woospr['reviews_first_color'];
        $reviewssecondcolor = $woospr['reviews_sec_color'];
        !empty($woospr['popup_animation']) ? $popupAnimation = $woospr['popup_animation'] : $popupAnimation = "";

        $custom_css = "
                .star-rating:hover + #page-wrap{animation-name: {$popupAnimation};}#page-wrap{
                        background: {$bgcolor};
                }.arrow-up:before {
                border-bottom: 12px solid {$bgcolor};
                    }#page-wrap section .wstar,#page-wrap section .wstar-text,#page-wrap section .wstar-num,#page-wrap section .wstar-review a{color:{$txtcolor}}.wstar-progress-bar span{   
                        background-color: {$firstcolor};
                        background-image: -moz-linear-gradient(top,{$firstcolor}, {$secondcolor});
                        background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, {$firstcolor}), color-stop(1, {$secondcolor}));
                        background-image: -webkit-linear-gradient({$firstcolor},{$secondcolor});
                            }#page-wrap section .wstar-review a:hover {
                            color: {$hovercolor};
                            }.big-wstar-progress-bar span{   
                        background-color: {$reviewsfirstcolor};
                        background-image: -moz-linear-gradient(top,{$reviewsfirstcolor}, {$reviewssecondcolor});
                        background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, {$reviewsfirstcolor}), color-stop(1, {$secondcolor}));
                        background-image: -webkit-linear-gradient({$reviewsfirstcolor},{$reviewssecondcolor});
                        }.mg-cmnt-like,.mg-cmnt-unlike{font-size: {$wooBtnFont}px !important; color:{$wooBtnClr}; background-color: {$wooBtnBgCol};}.mg-active{ background-color:{$wooBtnActiveCol}}#big-page-wrap{width:{$wooBoxWidth}%
};
}";
        wp_add_inline_style('custom-style', $custom_css);
    }

}
?>