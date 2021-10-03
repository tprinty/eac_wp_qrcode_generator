<?php

/*
Plugin Name: EAC WP QRCode Generator
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Generate a QR Code with a short code or URL.
Version: 1.0
Author: tprinty
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

include_once('lib/barcode_generator.php');

/*
 * Function to handle the CRON scheduled task to clear out olf
 * QR Codes
 */

add_action( 'clear_old_qr_codes', 'clear_old_qr_codes' );
function clear_old_qr_codes(){
    $upload_dir   = wp_upload_dir();
    $path = $upload_dir['basedir'] ."/qr_codes";
    if ($handle = opendir($path)) {
        while (false !== ($file = readdir($handle))) {
            if ((time()-filectime($path.$file)) < 3600) {
                unlink($path.$file);
            }
        }
    }
}

/*
 * Makes sure we have a temp directory setup and scheduled to CRON
 * to keep things clean
 */
function check_temp_file_dirs(){
    $upload_dir   = wp_upload_dir();
    $path = $upload_dir['basedir'] ."/qr_codes";

    if (!file_exists($path)){
        mkdir($path, 0755, true);
    }

    //Clear out this directory later
    wp_schedule_single_event( time() + 3600, 'clear_old_qr_codes' );

}

/*
 * Main function to generate the codes.
 */
function gen_qr_code($options, $format, $data){

    $generator = new barcode_generator();
    $symbology = "qr";

    //make sure temp files exists
    check_temp_file_dirs();

    $upload_dir   = wp_upload_dir();
    $file = "qr_". rand() . date('ymdyhism') . ".". $format;
    $filename =  $upload_dir['basedir'] ."/qr_codes/".$file;

    if (strtolower($format) == "svg"){
        $render = true;
        $svg = $generator->render_svg($symbology, $data, $options, $filename, $render);
        return $svg;
    }else if(strtolower($format) == "url"){
        $render = false;
        $generator->output_image($format, $symbology, $data, $options, $filename, $render);
        return ($upload_dir["baseurl"].'/qr_codes/'. $file);
    }else{
        $render = false;
        $generator->output_image($format, $symbology, $data, $options, $filename, $render);
        return '<img src="'. $upload_dir["baseurl"].'/qr_codes/'. $file .'" height="'.  $options['h'] .'" width="'. $options['w'] .'">';
    }
}



//The Shortcode Code
add_shortcode('eac_qrcode_generate', 'eac_qrcode_generate');
function eac_qrcode_generate($atts) {

    $format = (isset($atts['format']) ? $atts['format'] : 'png');
    $data = (isset($atts['data']) ? $atts['data'] : 'Hello from Edison Avenue Consulting LLC');

    $options = array();
    $options['p'] = 0; //No padding
    $options['w'] = (isset($atts['width']) ? $atts['width'] : '200');
    $options['h'] = (isset($atts['height']) ? $atts['height'] : '200');
    $options['bc'] = (isset($atts['bgcolor']) ? strtoupper($atts['bgcolor']) : 'FFFFFF');
    $options['cs'] = (isset($atts['spacecolor']) ? strtoupper($atts['spacecolor']) : 'FFFFFF');
    $options['cm'] = (isset($atts['mcolor']) ? strtoupper($atts['mcolor']) : '000000');

    return gen_qr_code($options, $format, $data);

}


//Handle the URL
add_action('parse_request', 'eac_qrcode_generate_url_handler');
function eac_qrcode_generate_url_handler() {
    if( isset($_GET['eac_qrcode_generate']) ) {
        $format = (isset($_GET['format']) ? $_GET['format'] : 'png');
        $data = (isset($_GET['data']) ? $_GET['data'] : 'Hello from Edison Avenue Consulting LLC');

        $options = array();
        $options['p'] = 0; //No padding
        $options['w'] = (isset($_GET['width']) ? $_GET['width'] : '200');
        $options['h'] = (isset($_GET['height']) ? $_GET['height'] : '200');
        $options['bc'] = (isset($_GET['bgcolor']) ? strtoupper($_GET['bgcolor']) : 'FFFFFF');
        $options['cs'] = (isset($_GET['spacecolor']) ? strtoupper($_GET['spacecolor']) : 'FFFFFF');
        $options['cm'] = (isset($_GET['mcolor']) ? strtoupper($_GET['mcolor']) : '000000');

        return gen_qr_code($options, $format, $data);
    }
}

//VCard QR Code Generator
add_action('parse_request', 'eac_qrcode_generate_vcard_handler');
function eac_qrcode_generate_vcard_handler(){
    if( isset($_GET['eac_qrcode_generate_vcard']) ) {
        $format = (isset($_GET['format']) ? $_GET['format'] : 'png');
        $options = array();
        $options['p'] = 0; //No padding
        $options['w'] = (isset($_GET['width']) ? $_GET['width'] : '200');
        $options['h'] = (isset($_GET['height']) ? $_GET['height'] : '200');
        $options['bc'] = (isset($_GET['bgcolor']) ? strtoupper($_GET['bgcolor']) : 'FFFFFF');
        $options['cs'] = (isset($_GET['spacecolor']) ? strtoupper($_GET['spacecolor']) : 'FFFFFF');
        $options['cm'] = (isset($_GET['mcolor']) ? strtoupper($_GET['mcolor']) : '000000');

        //Build Up the Data
        $first_name = (isset($_GET['first_name']) ? $_GET['first_name'] : 'Tom');
        $last_name = (isset($_GET['last_name']) ? $_GET['last_name'] : 'Printy');
        $name_prefix = (isset($_GET['name_prefix']) ? $_GET['name_prefix'] : 'Mr.');
        $name_suffix = (isset($_GET['name_suffix']) ? $_GET['name_suffix'] : '');
        $title = (isset($_GET['title']) ? $_GET['title'] : 'Owner');
        $org = (isset($_GET['org']) ? $_GET['org'] : 'Edison Avenue Consulting LLC');
        $email = (isset($_GET['email']) ? $_GET['email'] : 'info@edisonave.com');
        $tel =  (isset($_GET['tel']) ? $_GET['tel'] : '+18472356267');
        $url =  (isset($_GET['url']) ? $_GET['url'] : 'https://edisonave.com');

        $data = "BEGIN:VCARD VERSION:3.0";
        $data .="N:" . $last_name ." ". $first_name ." ". $name_prefix ." ". $name_suffix ."\r\n";
        $data .="FN:". $name_prefix ." ". $first_name ." ". $last_name ." ". $name_suffix ."\r\n";
        $data .="TITLE:". $title ."\r\n";
        $data .="ORG:". $org ."\r\n";
        $data .="EMAIL:". $email ."\r\n";
        $data .="TEL:". $tel. "\r\n";
        $data .="URL:". $url ."\r\n";
        $data .= "REV:" . date('Ymd') . "T195243Z\r\n";
        $data .="END:VCARD";

        $data = urlencode($data);

        return gen_qr_code($options, $format, $data);
    }
}

add_shortcode('eac_varcard_qrcode_generate', 'eac_varcard_qrcode_generate');
function eac_varcard_qrcode_generate($atts) {
    $format = (isset($atts['format']) ? $atts['format'] : 'png');

    $options = array();
    $options['p'] = 0; //No padding
    $options['w'] = (isset($atts['width']) ? $atts['width'] : '200');
    $options['h'] = (isset($atts['height']) ? $atts['height'] : '200');
    $options['bc'] = (isset($atts['bgcolor']) ? strtoupper($atts['bgcolor']) : 'FFFFFF');
    $options['cs'] = (isset($atts['spacecolor']) ? strtoupper($atts['spacecolor']) : 'FFFFFF');
    $options['cm'] = (isset($atts['mcolor']) ? strtoupper($atts['mcolor']) : '000000');

    //Build Up the Data
    $first_name = (isset($atts['first_name']) ? $atts['first_name'] : 'Tom');
    $last_name = (isset($atts['last_name']) ? $atts['last_name'] : 'Printy');
    $name_prefix = (isset($atts['name_prefix']) ? $atts['name_prefix'] : 'Mr.');
    $name_suffix = (isset($atts['name_suffix']) ? $atts['name_suffix'] : '');
    $title = (isset($atts['title']) ? $atts['title'] : 'Owner');
    $org = (isset($atts['org']) ? $atts['org'] : 'Edison Avenue Consulting LLC');
    $email = (isset($atts['email']) ? $atts['email'] : 'info@edisonave.com');
    $tel =  (isset($atts['tel']) ? $atts['tel'] : '+18472356267');
    $url =  (isset($atts['url']) ? $atts['url'] : 'https://edisonave.com');

    $data = "BEGIN:VCARD VERSION:3.0\r\n";
    $data .="N:" . $last_name .";". $first_name .";". $name_prefix .";". $name_suffix ."\r\n";
    $data .="FN:". $name_prefix ." ". $first_name ." ". $last_name ." ". $name_suffix ."\r\n";
    $data .="TITLE:". $title ."\r\n";
    $data .="ORG:". $org ."\r\n";
    $data .="EMAIL;TYPE=internet:". $email ."\r\n";
    $data .="TEL;TYPE=work,voice:". $tel. "\r\n";
    $data .="URL:". $url ."\r\n";

    //Address is optional US only
    $address_string = ";;";
    if (isset($atts['address'])){
        $address_string .= $atts['address'];
    }
    if (isset($atts['address2'])){
        $address_string .= ";".$atts['address2'];
    }
    if (isset($atts['city'])){
        $address_string .= ";".$atts['city'];
    }
    if (isset($atts['state'])){
        $address_string .= ";".$atts['state'];
    }
    if (isset($atts['postalcode'])){
        $address_string .= ";".$atts['state'];
    }
    if (strlen($address_string)>3){
        $data .= "ADR:" . $address_string."\r\n";
    }
    //Finish up the vcard
    $data .= "REV:" . date('Ymd') . "T195243Z\r\n";
    $data .="END:VCARD";
    return gen_qr_code($options, $format, $data);
}






//Do the block stuff
