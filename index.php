<?php
/*
Plugin Name: Account Manager
Version: 1.0
Author: Julien Appert
Author URI: http://julienappert.com
*/


namespace jappert/acma;

defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

define('ACMA_FILE', __FILE__);

require_once dirname(LDWAM_FILE) . '/vendor/autoload.php';
require_once dirname(LDWAM_FILE) . '/includes/admin.php';
require_once dirname(LDWAM_FILE) . '/includes/front.php';

use ZxcvbnPhp\Zxcvbn;

function acma_trad($string){
  global $ldwam;

  $trads = $ldwam->trads;
  $keys = explode('.',$string);
  foreach($keys as $key){
    if(array_key_exists($key, $trads)){
      $trads = $trads[$key];
    }
  }
  if(is_string($trads))
    return $trads;
  return $string;
}

function acma_field($type, $name, $label = null, $value = null, $args = array()){
  if(is_null($label)) $label = $name;
  if(is_null($value)) $value = '';

  if(isset($_POST[$name])) $value = $_POST[$name];

  if(isset($args['id'])) $id = $args['id']; else $id = $name;
  if(isset($args['placeholder'])) $placeholder = $args['placeholder']; else $placeholder = '';
  if(isset($args['choices'])) $choices = $args['choices']; else $choices = array();
  if(isset($args['test_strength'])) $test_strength = $args['test_strength']; else $test_strength = true;
  if(isset($args['inline'])) $inline = $args['inline']; else $inline = false;
  if(isset($args['postid'])) $postid = $args['postid']; else $postid = NULL;
  if(isset($args['attrs'])) $attrs = $args['attrs']; else $attrs = array();

  if(strlen($field_url = locate_template('/fields/' . $type . '.php', false)) > 0){
    require($field_url);
  }
  else{
    require(dirname(ACMA_FILE) . '/templates/fields/' . $type . '.php');
  }
}

function acma_ajax_action() {

    if(wp_verify_nonce($_POST['_ajax_nonce'], 'acma-ajax-nonce')) {
      $postid = $_POST['postid'];
      // handle file upload
      $status = wp_handle_upload($_FILES['file'], array('test_form' => false));

      $filename = $status['file'];
      $filetype = $status['type'];
      $fileurl = $status['url'];

      $attachment = array(
        'guid'  =>  $fileurl,
        'post_mime_type'  =>  $filetype,
        'post_title'  =>  sanitize_title(basename($filename)),
        'post_status' =>  'inherit',
        'post_content'  =>  ''
      );

      $attach_id = wp_insert_attachment($attachment, $filename, $postid);
      require_once( ABSPATH . 'wp-admin/includes/image.php' );
      $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
      wp_update_attachment_metadata( $attach_id, $attach_data );
      //set_post_thumbnail( $postid, $attach_id );

      $src = wp_get_attachment_image_src($attach_id, 'medium');
      echo json_encode(array(
        'id'  =>  $attach_id,
        'url' =>  $src[0]
      ));
    }
    exit;
}
add_action('wp_ajax_acma_ajax_action', "acma_ajax_action");

class AcMa_Init{

  public $trads = array();

  public function __construct(){
    new AcMa_Admin();
    new AcMa_Front();
  }

};
$GLOBALS['acma'] = new AcMa_Init();
