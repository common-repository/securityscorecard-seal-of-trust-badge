<?php
/*
Plugin Name: SecurityScorecard Seal of Trust Badge
Plugin URI: https://securityscorecard.com/blog/how-badges-helps-you-put-your-security-score-front-and-center
Description: Add the SecurityScorecard Badge of Trust to your website using a custom shortcode or widget.
Version: 1.1
Author: SecurityScorecard
Author URI: https://securityscorecard.com/
License: GPL2
 */

// Adds widget: SecurityScorecard Seal of Trust Badge2
class Securityscorecardsea_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'securityscorecardsea_widget',
            esc_html__('SecurityScorecard Seal of Trust Badge', 'textdomain')
        );
    }

    private $widget_fields = array(
        array(
            'label' => 'SecurityScorecard Domain',
            'id' => 'sscdomain_text',
            'type' => 'text',
        ),
        array(
            'label' => 'Hide Attribution Link',
            'id' => 'hideattribution_checkbox',
            'default' => 'yes',
            'type' => 'checkbox',
        ),
    );
    
    private $ssc_allowed_tags = array(
            'a' => array(
                'href' => array(),
                'class' => array(),
                'target' => array(),
                'alt' => array(),
                'title' => array(),
                'style' => array()
            ),
            'input' => array(
                'type' => array(),
                'class' => array(),
                'name' => array(),
                'value' => array(),
                'id' => array(),
                'checked' => array(),
                'placeholder' => array()
            ),
            'iframe' => array(
                'loading' => array(),
                'width' => array(),
                'height' => array(),
                'src' => array(),
                'style' => array(),
                'frameborder' => array()
            ),
            'p' => array(),
            'svg' => array(
                'width' => array(),
                'viewbox' => array(),
                'viewBox' => array(),
                'style' => array()
            ),
            'path' => array(
                'd' => array(),
                'fill' => array()
            ),
            'div' => array(
                'class' => array(),
                'style' => array()
            ),
            'br' => array(),
            );

    public function widget($args, $instance)
    {
        

        $hostValueFromWidget = $instance['sscdomain_text'];

        $urlparts = parse_url(home_url());
        $domain = ($hostValueFromWidget !== '') ? $hostValueFromWidget : $urlparts['host'];
        $badgeTitle = esc_attr(get_bloginfo('name')) . ' Security Rating';
        
        echo wp_kses($args['before_widget'], $this->ssc_allowed_tags);
        echo wp_kses(sscbadge_print_bagde_frontend_html($domain, $instance['hideattribution_checkbox']), $this->ssc_allowed_tags);
        echo wp_kses($args['after_widget'], $this->ssc_allowed_tags);
    }

    public function field_generator($instance)
    {
        $output = '';
        foreach ($this->widget_fields as $widget_field) {
            $default = '';
            if (isset($widget_field['default'])) {
                $default = $widget_field['default'];
            }
            $widget_value = !empty($instance[$widget_field['id']]) ? $instance[$widget_field['id']] : esc_html__($default, 'textdomain');
            switch ($widget_field['type']) {
                case 'checkbox':
                    $output .= '<p>';
                    $output .= '<input class="checkbox" type="checkbox" ' . checked($widget_value, true, false) . ' id="' . esc_attr($this->get_field_id($widget_field['id'])) . '" name="' . esc_attr($this->get_field_name($widget_field['id'])) . '" value="1">';
                    $output .= '<label for="' . esc_attr($this->get_field_id($widget_field['id'])) . '">' . esc_attr($widget_field['label'], 'textdomain') . '</label>';
                    $output .= '</p>';
                    break;
                default:
                    $output .= '<p>';
                    $output .= '<label for="' . esc_attr($this->get_field_id($widget_field['id'])) . '">' . esc_attr($widget_field['label'], 'textdomain') . ':</label> ';
                    $output .= '<input class="widefat" id="' . esc_attr($this->get_field_id($widget_field['id'])) . '" name="' . esc_attr($this->get_field_name($widget_field['id'])) . '" type="' . $widget_field['type'] . '" value="' . esc_attr($widget_value) . '" placeholder="Leave empty to use current domain">';
                    $output .= '</p>';
            }
        }
        echo wp_kses($output, $this->ssc_allowed_tags);
    }

    public function form($instance)
    {
        $this->field_generator($instance);
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        foreach ($this->widget_fields as $widget_field) {
            switch ($widget_field['type']) {
                default:
                    $instance[$widget_field['id']] = (!empty($new_instance[$widget_field['id']])) ? strip_tags($new_instance[$widget_field['id']]) : '';
            }
        }
        return $instance;
    }
}

function sscbadge_register_securityscorecardsea_widget()
{
    register_widget('Securityscorecardsea_Widget');
}
add_action('widgets_init', 'sscbadge_register_securityscorecardsea_widget');

if (!function_exists('sscbadge_print_bagde_frontend_html')) {
    function sscbadge_print_bagde_frontend_html($domain, $hideattribution)
    {

        $html = '';

        if ($domain) {
            $html .= '<iframe loading="lazy" src="https://securityscorecard.com/security-rating/badge/' . $domain . '" width="256" height="100" frameborder="0"><br />
</iframe>';
        }

        if ( $hideattribution != 1 ) {

            $badgeTitle = esc_attr(get_bloginfo('name')) . ' Security Rating';

            $html .= sprintf('<div class="ssc-powered-by" style="width:256px; text-align:center; ">
			<a href="https://securityscorecard.com/security-rating/%1$s?utm_medium=badge&utm_source=%1$s&utm_campaign=wp-plugin" target="_BLANK" title="%2$s" alt="%2$s">
			<svg viewBox="0 0 20 25" width="14" style="vertical-align:text-bottom">
			  <path d="M131.735 17.249V16.2048C131.484 16.5769 131.153 16.8815 130.767 17.0933C130.382 17.305 129.954 17.4179 129.518 17.4226C127.807 17.4226 126.599 16.0312 126.599 13.7547C126.609 11.5071 127.813 10.0709 129.518 10.0709C129.955 10.0755 130.385 10.1899 130.77 10.4044C131.156 10.6189 131.487 10.9271 131.735 11.3031V7.57736H132.753V17.249H131.735ZM131.735 15.3355V12.1738C131.52 11.8353 131.23 11.5558 130.89 11.359C130.549 11.1621 130.169 11.0537 129.78 11.0428C128.45 11.0428 127.662 12.1999 127.662 13.7547C127.662 15.3095 128.449 16.4521 129.78 16.4521C130.168 16.4439 130.548 16.3382 130.888 16.1438C131.228 15.9494 131.519 15.6721 131.735 15.3355Z" fill="white"></path>
			  <path d="M16.4242 16.1542L16.4229 21.3596L20.7506 18.7547L20.7478 13.5276L16.4242 10.9213V16.1542Z" fill="#6641f3"></path>
			  <path d="M6.05225 22.398L10.3758 25L14.6953 22.4009V17.1955L6.05225 22.398Z" fill="#6641f3"></path>
			  <path d="M20.7469 11.4435L20.7441 6.24966L16.4247 3.64624L12.1177 6.24242L20.7469 11.4435Z" fill="#6641f3"></path>
			  <path d="M6.05518 9.89587V15.1056L10.376 17.709L14.6955 15.1114V9.89587L10.376 7.29245L6.05518 9.89587Z" fill="#6641f3"></path>
			  <path d="M0.00277065 13.5406L0 18.7547L4.3236 21.3567L8.64997 18.7518L0.00277065 13.5406Z" fill="#6641f3"></path>
			  <path d="M4.32611 3.64624L0.00667689 6.2511L0.00390625 11.4579L4.32611 14.0642V3.64624Z" fill="#6641f3"></path>
			  <path d="M10.376 5.20828L14.6955 2.60486L10.376 0L6.05518 2.60486V7.81314L10.376 5.20828Z" fill="#6641f3"></path>
			</svg> %3$s
			</a>
		  </div>', $domain, $badgeTitle, 'Powered by SecurityScorecard');
        }

        return $html;

    }
}

add_shortcode('ssc-badge', 'sscbadge_ssc_badge_shortcode_function');
function sscbadge_ssc_badge_shortcode_function($atts)
{
    $atts = shortcode_atts(array(
        'domain' => '',
        'hide_attribution' => '0',
    ), $atts, 'ssc-badge');

	$hideAttribution = ( 1 == $atts['hide_attribution'] ) ? 1 : 0;


    $urlparts = parse_url(home_url());
    $domain = ($atts['domain'] !== '') ? sanitize_text_field($atts['domain']) : str_replace('www.','',$urlparts['host']);

    $html = sscbadge_print_bagde_frontend_html($domain, $hideAttribution);

    $ssc_allowed_tags = array(
            'a' => array(
                'href' => array(),
                'class' => array(),
                'target' => array(),
                'alt' => array(),
                'title' => array(),
                'style' => array()
            ),
            'input' => array(
                'type' => array(),
                'class' => array(),
                'name' => array(),
                'value' => array(),
                'id' => array(),
                'checked' => array(),
                'placeholder' => array()
            ),
            'iframe' => array(
                'loading' => array(),
                'width' => array(),
                'height' => array(),
                'src' => array(),
                'style' => array(),
                'frameborder' => array()
            ),
            'p' => array(),
            'svg' => array(
                'width' => array(),
                'viewbox' => array(),
                'viewBox' => array(),
                'style' => array()
            ),
            'path' => array(
                'd' => array(),
                'fill' => array()
            ),
            'div' => array(
                'class' => array(),
                'style' => array()
            ),
            'br' => array(),
            );

    return wp_kses($html, $ssc_allowed_tags);
}