<?php

/*
Plugin Name: EAC WP Qrcode Generator
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: tprinty
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

include_one('lib/barcode_generator.php');


//The Shortcode Code
add_shortcode('eac_qrcode_generate', 'eac_qrcode_generate');
function eac_qrcode_generate($atts) {
    $generator = new barcode_generator();
    $symbology = "qr";

    //PNG SVG or JPEG
    $format = (isset($atts['format']) ? $atts['format'] : 'png');

    if (strtolower($format) == "svg"){
        $svg = $generator->render_svg($symbology, $data, $options);
        echo $svg;
    }else{
        $generator->output_image($format, $symbology, $data, $options);
    }

    echo "There were errors with your arguments please check and confirm";
}

//Handle the URL
add_action('parse_request', 'eac_qrcode_generate_url_handler');
function eac_qrcode_generate_url_handler() {
    if( isset($_GET['eac_qrcode_generate']) ) {
        $generator = new barcode_generator();
        $symbology = "qr";

        // do something

        exit();
    }
}
