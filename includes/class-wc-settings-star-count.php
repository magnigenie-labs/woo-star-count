<?php

/**
 * Review Stars Count for  WooCommerce
 *
 * @author 	MagniGenie
 * @category 	Admin
 * @version     1.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
if (class_exists('WC_Settings_Page')) :
    /**
     * WC_Settings_Accounts
     */
    class WC_Settings_WOO_Star_count extends WC_Settings_Page {

        /**
         * Constructor.
         */
        public function __construct() {
            $this->id = 'woopsr';
            $this->label = __('Woo Star Count', 'woopsr');
            add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_page'), 20);
            add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
            add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
        }

        /**
         * Get settings array
         *
         * @return array
         */
        //--------------admin design section code goes here----------
        public function get_settings() {
            $categories = get_terms('product_cat', 'orderby=name&hide_empty=0');
            $animateClass = array('' => "default", 'bounce' => "bounce", 'flash' => "flash", 'pulse' => "pulse", 'rubberBand' => "rubberBand", 'shake' => "shake", 'swing' => "swing", 'tada' => "tada", 'wobble' => "wobble", 'jello' => "jello", 'bounceIn' => "bounceIn", 'bounceInDown' => "bounceInDown", 'bounceInLeft' => "bounceInLeft", 'bounceInRight' => "bounceInRight", 'bounceInUp' => "bounceInUp", 'bounceOut' => "bounceOut", 'bounceOutDown' => "bounceOutDown", 'bounceOutLeft' => "bounceOutLeft", 'bounceOutRight' => "bounceOutRight", 'bounceOutUp' => "bounceOutUp", 'fadeIn' => "fadeIn", 'fadeInDown' => "fadeInDown", 'fadeInDownBig' => "fadeInDownBig", 'fadeInLeft' => "fadeInLeft", 'fadeInLeftBig' => "fadeInLeftBig", 'fadeInRight' => "fadeInRight", 'fadeInRightBig' => "fadeInRightBig", 'fadeInUp' => 'fadeInUp', 'fadeInUpBig' => "fadeInUpBig", 'fadeOut' => "fadeOut", 'fadeOutDown' => "fadeOutDown", 'fadeOutDownBig' => "fadeOutDownBig", 'fadeOutLeft' => "fadeOutLeft", 'fadeOutLeftBig' => "fadeOutLeftBig", 'fadeOutRight' => "fadeOutRight", 'fadeOutRightBig' => "fadeOutRightBig", 'fadeOutUp' => "fadeOutUp", 'fadeOutUpBig' => "fadeOutUpBig", 'flip' => "flip", 'flipInX' => "flipInX", 'flipInY' => "flipInY", 'flipOutX' => "flipOutX", 'flipOutY' => "flipOutY", 'lightSpeedIn' => "lightSpeedIn", 'lightSpeedOut' => "lightSpeedOut", 'rotateIn' => "rotateIn", 'rotateInDownLeft' => "rotateInDownLeft", 'rotateInDownRight' => "rotateInDownRight", 'rotateInUpLeft' => "rotateInUpLeft", 'rotateInUpRight' => "rotateInUpRight", 'rotateOut' => "rotateOut", 'rotateOutDownLeft' => "rotateOutDownLeft", 'rotateOutDownRight' => "rotateOutDownRight", 'rotateOutUpLeft' => "rotateOutUpLeft", 'rotateOutUpRight' => "rotateOutUpRight", 'slideInUp' => "slideInUp", 'slideInDown' => "slideInDown", 'slideInLeft' => "slideInLeft", 'slideInRight' => "slideInRight", 'slideOutUp' => "slideOutUp", 'slideOutDown' => "slideOutDown", 'slideOutLeft' => "slideOutLeft", 'slideOutRight' => "slideOutRight", 'zoomIn' => "zoomIn", 'zoomInDown' => "zoomInDown", 'zoomInLeft' => "zoomInLeft", 'zoomInRight' => "zoomInRight", 'zoomInUp' => "zoomInUp", 'zoomOut' => "zoomOut", 'zoomOutDown' => "zoomOutDown", 'zoomOutLeft' => "zoomOutLeft", 'zoomOutRight' => "zoomOutRight", 'zoomOutUp' => "zoomOutUp", 'hinge' => "hinge", 'rollIn' => "rollIn", 'rollOut' => "rollOut");
            $cats = array();
            if ($categories)
                foreach ($categories as $cat)
                    $cats[$cat->term_id] = esc_html($cat->name);
            return apply_filters('woocommerce_' . $this->id . '_settings', array(
                array('title' => __('Woo Star Count Settings', 'woopsr'), 'type' => 'title', 'desc' => '', 'id' => 'woopsr_title'),
                array(
                    'title' => __('Woo Star Count Plugin Enable', 'woopsr'),
                    'desc' => __('Enable Woo star count', 'woopsr'),
                    'type' => 'checkbox',
                    'id' => 'woopsr[enabled]',
                    'default' => 'no'
                ),
                array(
                    'title' => __('Enable Popup For Product Single Page', 'woopsr'),
                    'desc' => __('Enable For Single Product Page', 'woopsr'),
                    'type' => 'checkbox',
                    'id' => 'woopsr[single_enable]',
                    'default' => 'no'
                ),
                array(
                    'title' => __('Enable Popup For Product Archive Page', 'woopsr'),
                    'desc' => __('Enable For Product Archive Page', 'woopsr'),
                    'type' => 'checkbox',
                    'id' => 'woopsr[multi_enable]',
                    'default' => 'no'
                ),
                array(
                    'title' => __('Popup Background Color', 'woopsr'),
                    'desc' => __('', 'woopsr'),
                    'id' => 'woopsr[bg_color]',
                    'type' => 'color',
                    'css' => 'width: 125px;',
                    'default' => '#777777',
                ),
                array(
                    'title' => __('Popup Text color', 'woopsr'),
                    'desc' => __('', 'woopsr'),
                    'id' => 'woopsr[text_color]',
                    'type' => 'color',
                    'css' => 'width: 125px;',
                    'default' => '#ffffff',
                ),
                array(
                    'title' => __('Popup Progress Bar Primary Color', 'woopsr'),
                    'desc' => __('', 'woopsr'),
                    'id' => 'woopsr[first_color]',
                    'type' => 'color',
                    'css' => 'width: 125px;',
                    'default' => '#111111',
                ),
                array(
                    'title' => __('Popup Progress Bar Secondary Color', 'woopsr'),
                    'desc' => __('', 'woopsr'),
                    'id' => 'woopsr[sec_color]',
                    'type' => 'color',
                    'css' => 'width: 125px;',
                    'default' => '#ffffff',
                ),
                array(
                    'title' => __('Popup See All Reviews Hover Color', 'woopsr'),
                    'desc' => __('', 'woopsr'),
                    'id' => 'woopsr[hover_color]',
                    'type' => 'color',
                    'css' => 'width: 125px;',
                    'default' => '#ffffff',
                ),
                array(
                    'title' => __('Popup Animation', 'woopsr'),
                    'desc' => __('', 'woopsr'),
                    'id' => 'woopsr[popup_animation]',
                    'type' => 'select',
                    'options' => $animateClass,
                    'chosen' => true,
                    'css' => 'width: 150px;',
                ),
                array('type' => 'sectionend', 'id' => 'simple_woopsr_options'),
                array('title' => __('Woo Star Count Settings (Reviews Tab)', 'woopsr'), 'type' => 'title', 'desc' => '', 'id' => 'woopsr_title'),
                array(
                    'title' => __('Enable For Product Single Page(Reviews Tab)', 'woopsr'),
                    'desc' => __('Enable For Single Product Page', 'woopsr'),
                    'type' => 'checkbox',
                    'id' => 'woopsr[reviews_single_enable]',
                    'default' => 'no'
                ),
                array(
                    'title' => __('Review Box Width', 'woopsr'),
                    'desc' => __('%', 'woopsr'),
                    'type' => 'number',
                    'id' => 'woopsr[review_box_width]',
                    'default' => '1',
                    'custom_attributes' => array('min' => '1', 'step' => '1'),
                    'css' => 'width: 150px;'
                ),
                array(
                    'title' => __('Progress Bar Primary Color', 'woopsr'),
                    'desc' => __('', 'woopsr'),
                    'id' => 'woopsr[reviews_first_color]',
                    'type' => 'color',
                    'css' => 'width: 125px;',
                    'default' => '#111111',
                ),
                array(
                    'title' => __('Progress Bar Secondary Color', 'woopsr'),
                    'desc' => __('', 'woopsr'),
                    'id' => 'woopsr[reviews_sec_color]',
                    'type' => 'color',
                    'css' => 'width: 125px;',
                    'default' => '#ffffff',
                ),
                array('type' => 'sectionend', 'id' => 'simple_woopsr_options'),
                array('title' => __('Woo Star Count Settings (Comment Section)', 'woopsr'), 'type' => 'title', 'desc' => '', 'id' => 'woopsr_title'),
                array(
                    'title' => __('Enable Comment Section', 'woopsr'),
                    'desc' => __('Enable comment helpful section', 'woopsr'),
                    'type' => 'checkbox',
                    'id' => 'woopsr[cmnt_enabled]',
                    'default' => 'no'
                ),
                array(
                    'title' => __('Comment text', 'woopsr'),
                    'desc' => __('*Remarks : {count} keyword showing number of people helpful for review', 'woopsr'),
                    'id' => 'woopsr[woo_cmnt]',
                    'type' => 'textarea',
                    'css' => 'width: 300px; height:50px;',
                    'default' => '{count} People found this helpful. Was this review helpful to you?',
                ),
                array(
                    'title' => __('Button Background Color', 'woopsr'),
                    'desc' => __('Yes and No button background color', 'woopsr'),
                    'id' => 'woopsr[btn_bg_color]',
                    'type' => 'color',
                    'css' => 'width: 125px;',
                    'default' => '#111111',
                ),
                array(
                    'title' => __('Button Active Color', 'woopsr'),
                    'desc' => __('Yes and No button active color', 'woopsr'),
                    'id' => 'woopsr[btn_active_color]',
                    'type' => 'color',
                    'css' => 'width: 125px;',
                    'default' => '#ffffff',
                ),
                array(
                    'title' => __('Button Text Font Size', 'woopsr'),
                    'desc' => __('px', 'woopsr'),
                    'type' => 'number',
                    'id' => 'woopsr[btn_font_size]',
                    'default' => '1',
                    'custom_attributes' => array('min' => '1', 'step' => '1'),
                    'css' => 'width: 150px;'
                ),
                array(
                    'title' => __('Button Text Color', 'woopsr'),
                    'desc' => __('Yes and No button text color', 'woopsr'),
                    'id' => 'woopsr[btn_txt_color]',
                    'type' => 'color',
                    'css' => 'width: 125px;',
                    'default' => '#ffffff',
                ),
                array('type' => 'sectionend', 'id' => 'simple_woopsr_options'),
            )); // End pages settings
        }

    }
    return new WC_Settings_WOO_Star_count();
endif;