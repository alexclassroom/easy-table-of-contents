<?php

/**
 * Helper Functions
 *
 * @package     saswp
 * @subpackage  Helper/Templates
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.4.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Helper method to check if user is in the plugins page.
 *
 * @author René Hermenau
 * @since  1.4.0
 *
 * @return bool
 */
 
/**
 * display deactivation logic on plugins page
 * 
 * @since 1.4.0
 */

add_filter('admin_footer', 'eztoc_add_deactivation_feedback_modal');
function eztoc_add_deactivation_feedback_modal() {
    
    if( !is_admin()) {
        return;
    }

    $current_user = wp_get_current_user();
    if( !($current_user instanceof WP_User) ) {
        $email = '';
    } else {
        $email = trim( $current_user->user_email );
    }
    require_once EZ_TOC_PATH ."/includes/deactivate-feedback.php";
}

/**
 * send feedback via email
 * 
 * @since 1.4.0
 */
function eztoc_send_feedback() {

    if( isset( $_POST['data'] ) ) {
        parse_str( $_POST['data'], $form );
    }

    $text = '';
    if( isset( $form['eztoc_disable_text'] ) ) {
        $text = implode( "\n\r", $form['eztoc_disable_text'] );
    }

    $headers = array();

    $from = isset( $form['eztoc_disable_from'] ) ? $form['eztoc_disable_from'] : '';
    if( $from ) {
        $headers[] = "From: $from";
        $headers[] = "Reply-To: $from";
    }

    $subject = isset( $form['eztoc_disable_reason'] ) ? $form['eztoc_disable_reason'] : '(no reason given)';

    if($subject == 'technical issue'){

          $text = trim($text);

          if(!empty($text)){

            $text = 'technical issue description: '.$text;

          }else{

            $text = 'no description: '.$text;
          }
      
    }

    $success = wp_mail( 'team@magazine3.in', $subject, $text, $headers );

    die();
}
add_action( 'wp_ajax_eztoc_send_feedback', 'eztoc_send_feedback' );

function cwv_enqueue_makebetter_email_js(){

    if( !is_admin() ) {
        return;
    }

    wp_enqueue_script( 'cwv-make-better-js', EZ_TOC_URL . 'includes/feedback.js', array( 'jquery' ));

    wp_enqueue_style( 'cwv-make-better-css', EZ_TOC_URL . 'includes/feedback.css', false  );
}
add_action( 'admin_enqueue_scripts', 'cwv_enqueue_makebetter_email_js' );
