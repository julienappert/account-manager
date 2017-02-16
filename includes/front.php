<?php
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

use Symfony\Component\Yaml\Yaml;

class AcMa_Front{

  public $trads = array();


  public function __construct(){

    add_action('init',array($this,'init'));
    add_shortcode('register_form',array($this, 'register_form'));
    add_shortcode('lostpassword_form',array($this, 'lostpassword_form'));
    add_shortcode('login_form',array($this, 'login_form'));
    add_shortcode('account_form',array($this, 'account_form'));
    add_action('wp_enqueue_scripts',array($this,'wp_enqueue_scripts') );
    add_action('template_redirect', array($this, 'template_redirect'));
    add_filter('lostpassword_url', array($this, 'lostpassword_url'));

  }

  public function template_redirect(){
    global $wp_query;

    if(isset($wp_query->query_vars[acma_trad('slug.logout')])){
      wp_logout();
      wp_redirect(get_permalink());
      exit;
    }
  }

  public function init(){
    global $wpdb, $acma, $acma_errors, $acma_notif;

    $theme_trads = $plugins_trads = array();
    $plugin_trads_path = dirname(LDWAM_FILE) . '/languages/' . get_locale() . '.yaml';
    if(file_exists($plugin_trads_path))
      $plugins_trads = Yaml::parse(file_get_contents($plugin_trads_path));

    $theme_trads_path = locate_template('/languages/acma-' . get_locale() . '.yaml');
    if(strlen($theme_trads_path)>0)
      $theme_trads = Yaml::parse(file_get_contents($theme_trads_path));

    $acma->trads = array_replace_recursive($plugins_trads, $theme_trads, $acma->trads);


    add_rewrite_endpoint( acma_trad('slug.logout'), EP_PERMALINK | EP_PAGES );


    /* REGISTER */
    if(isset($_POST['acma-register'])){
      if(wp_verify_nonce($_POST['register_nonce'], 'acmaregister-nonce')) {
        $errors = array();

        $email = $wpdb->escape($_POST['email']);
        if(!is_email($email)){
          $errors['email'] = acma_trad("register.email_novalid");
        }
         if( email_exists( $email )) {
           $errors['email'] = acma_trad("register.email_exists");
         }

         $password = $wpdb->escape($_POST['password']);
         if(strlen(trim($password)) == 0){
           $errors['password'] = acma_trad('register.password_need');
         }

         $errors = apply_filters('acma_register_errors', $errors);

         if(count($errors)>0){
           $acma_errors = $errors;
           $acma_notif = array(
              'type'    =>  'warning',
              'message' =>  acma_trad('register.got_errors')
            );
         }
         else{
           $user_id = wp_create_user( $email, $password, $email );
           apply_filters('acma_register_update', $user_id);

           $user = get_user_by( 'id', $user_id );
           if( $user ) {
               wp_set_current_user( $user_id, $user->user_login );
               wp_set_auth_cookie( $user_id );
               do_action( 'wp_login', $user->user_login );
           }

           $redirect = apply_filters('acma_register_redirect_to', $_POST['redirect_to']);
           if(strlen($redirect)>0){
             wp_redirect($redirect); exit;
           }
         }
      }
    }

    /* LOGIN */
    if(isset($_POST['acma-login'])){
      if(wp_verify_nonce($_POST['login_nonce'], 'acmalogin-nonce')) {
        $creds = array();

        if(strlen(trim($_POST['email'])) == 0){
          $acma_notif = array(
             'type'    =>  'warning',
             'message' =>  acma_trad('login.email_empty')
           );
        }
        elseif(strlen(trim($_POST['email'])) == 0){
          $acma_notif = array(
             'type'    =>  'warning',
             'message' =>  acma_trad('login.password_empty')
           );
        }
        else{

          $creds['user_login'] = $wpdb->escape($_POST['email']);
          $creds['user_password'] = $wpdb->escape($_POST['password']);
          $user = wp_signon( $creds, false );
          if ( is_wp_error($user) )
             $acma_notif = array(
                'type'    =>  'warning',
                'message' =>  __($user->get_error_message())
              );
          else{
            $redirect = apply_filters('acma_login_redirect_to', $_POST['redirect_to'], $user);
            if(strlen($redirect)>0){
              wp_redirect($redirect); exit;
            }
            else{
              wp_redirect(home_url('/')); exit;
            }
          }

        }
      }
    }


    /* LOST PASSWORD */
    if(isset($_POST['acma-lostpassword'])){
      if(wp_verify_nonce($_POST['lostpassword_nonce'], 'acmalostpassword-nonce')) {
        $email = $wpdb->escape($_POST['email']);
        if(!is_email($email)){
          $acma_notif = array(
            'type'  =>  'warning',
            'message' =>  acma_trad("lostpassword.email_novalid")
          );
        }
        elseif( !email_exists( $email )) {
          $acma_notif = array(
            'type'  =>  'warning',
            'message' =>   acma_trad("lostpassword.email_noexists")
          );
        }
        else{
          $email_content = locate_template('/emails/lost-password.html');
          if(strlen($email_content) == 0)
            $email_content = file_get_contents(dirname(LDWAM_FILE) . '/templates/emails/lost-password.html');

          $verif = wp_generate_password();
          $user_id = email_exists( $email );
          update_user_meta($user_id, 'verif', $verif);
          $email_content = str_replace('%URL%',
            add_query_arg(array(
              'newpass' => urlencode($verif),
              'email' =>  urlencode($email)
              ), $_SERVER['HTTP_REFERER']
          ), $email_content );
          wp_mail(
            $email,
            acma_trad('lostpassword.email_subject'),
            $email_content,
            array('Content-Type: text/html; charset=UTF-8')
          );
          $acma_notif = array(
            'type'  =>  'info',
            'message' =>  acma_trad("lostpassword.email_sent")
          );
        }
      }
    }

    /* CHANGE PASSWORD */
    if(isset($_GET['newpass'])){
      $verif = $_GET['newpass'];
      $email = $_GET['email'];
      if($user_id = email_exists($email)){
        if($verif == get_user_meta($user_id,'verif', true)){

          /*$user = get_user_by( 'id', $user_id );
          if( $user ) {
              wp_set_current_user( $user_id, $user->user_login );
              wp_set_auth_cookie( $user_id );
              do_action( 'wp_login', $user->user_login );
          }*/
        }
      }
    }
    if(isset($_POST['acma-changepassword'])){
      if(wp_verify_nonce($_POST['changepassword_nonce'], 'acmachangepassword-nonce')) {
        $user_id = email_exists($wpdb->escape($_POST['email']));
        if($user_id){
          $password = $wpdb->escape($_POST['password']);
          $user_data = wp_update_user(array(
            'ID'  =>  $user_id,
            'user_pass' =>  $password
          ));
          $acma_notif = array(
            'type'  =>  'success',
            'message' =>  acma_trad('lostpassword.password_changed')
          );
        }
      //  add_filter( 'send_password_change_email', function(){});
      }
    }

    /* ACCOUNT */
    if(isset($_POST['acma-account'])){
      if(wp_verify_nonce($_POST['account_nonce'], 'acmaaccount-nonce')) {
        $errors = array();

        $user = wp_get_current_user();

        $email = $wpdb->escape($_POST['email']);
        if(!is_email($email)){
          $errors['email'] = acma_trad("account.email_novalid");
        }
        elseif( $email != $user->user_email){
          if( email_exists( $email )) {
            $errors['email'] = acma_trad("account.email_exists");
          }
        }

         $oldpassword = $wpdb->escape($_POST['oldpassword']);
         $password = $wpdb->escape($_POST['password']);

         if( strlen($password) > 0 && !wp_check_password( $oldpassword, $user->user_pass, $user->ID) ){
           $errors['oldpassword'] = acma_trad("account.oldpassword_notmatch");
         }


         $errors = apply_filters('acma_account_errors', $errors);

         if(count($errors)>0){
           $acma_errors = $errors;
           $acma_notif = array(
              'type'    =>  'warning',
              'message' =>  acma_trad('account.got_errors')
            );
         }
         else{
           $user_id = $user->ID;
           if($user_id){
             $args = array(
               'ID' =>  $user_id,
               'user_login' =>  $email,
               'user_email' =>  $email
             );
             if(strlen(trim($password)) > 0){
               $args['user_pass'] = $password;
             }
             wp_update_user($args);
             apply_filters('acma_account_update', $user_id);
             $acma_notif = array(
                'type'    =>  'success',
                'message' =>  acma_trad('account.updated')
              );
           }
         }
      }
    }

  }

  public function wp_enqueue_scripts(){
    global $acma;

    if(!is_admin()){
      wp_enqueue_script('zxcvbn-js',plugins_url('scripts/zxcvbn.js',LDWAM_FILE),array(),'', true);
      wp_enqueue_script('validate-js',plugins_url('scripts/validate-js/validate.js',LDWAM_FILE),array(),'', true);
      wp_enqueue_style('acma-css',plugins_url('styles/front.css',LDWAM_FILE));

      // https://github.com/uxsolutions/bootstrap-datepicker
      wp_enqueue_style('acma-datepicker-css',plugins_url('scripts/datepair/datepicker/css/bootstrap-datepicker3.standalone.css',LDWAM_FILE));
      wp_enqueue_script('acma-datepicker',plugins_url('scripts/datepair/datepicker/js/bootstrap-datepicker.min.js',LDWAM_FILE),array('jquery'),'', true);
      wp_enqueue_script('acma-datepicker-locale',plugins_url('scripts/datepair/datepicker/locales/bootstrap-datepicker.fr.min.js',LDWAM_FILE),array('jquery'),'', true);

      // https://github.com/jonthornton/jquery-timepicker
      wp_enqueue_script('acma-timepicker',plugins_url('scripts/datepair/timepicker/js/jquery.timepicker.min.js',LDWAM_FILE),array('jquery'),'', true);
      wp_enqueue_style('acma-timepicker-css',plugins_url('scripts/datepair/timepicker/css/jquery.timepicker.css',LDWAM_FILE));

      // http://jonthornton.github.io/Datepair.js/
      wp_enqueue_script('acma-datepair',plugins_url('scripts/datepair/jquery.datepair.min.js',LDWAM_FILE),array('jquery'),'', true);

      wp_enqueue_script('acma-js',plugins_url('scripts/front.js',LDWAM_FILE),array('jquery','zxcvbn-js','validate-js', 'jquery-ui-sortable', 'plupload'),'', true);
      wp_localize_script( 'acma-js', 'acma', $acma->trads );
    }
  }

  public function register_form(){
    global $wpdb;
    $user = wp_get_current_user();

    $email = $user->user_email;
    $password = '';

    if(isset($_POST['acma-register'])){
      if(wp_verify_nonce($_POST['register_nonce'], 'acmaregister-nonce')) {
        $email = $wpdb->escape($_POST['email']);
        $password = $wpdb->escape($_POST['password']);
      }
    }

    if(strlen($template_url = locate_template('/forms/register.php', false)) > 0){
      require_once($template_url);
    }
    else{
      require_once(dirname(LDWAM_FILE) . '/templates/forms/register.php');
    }
  }

  public function account_form(){
    global $wpdb;
    $user = wp_get_current_user();

    $email = $user->user_email;
    $oldpassword = $password = '';

    if(isset($_POST['acma-account'])){
      if(wp_verify_nonce($_POST['account_nonce'], 'acmaaccount-nonce')) {
        $email = $wpdb->escape($_POST['email']);
        $oldpassword = $wpdb->escape($_POST['oldpassword']);
        $password = $wpdb->escape($_POST['password']);
      }
    }

    if(strlen($template_url = locate_template('/forms/account.php', false)) > 0){
      require_once($template_url);
    }
    else{
      require_once(dirname(LDWAM_FILE) . '/templates/forms/account.php');
    }
  }

  public function lostpassword_form(){
    global $acma_notif, $wpdb;
    $email = '';
    if(isset($_POST['acma-lostpassword'])){
      if(wp_verify_nonce($_POST['lostpassword_nonce'], 'acmalostpassword-nonce')) {
        $email = $wpdb->escape($_POST['email']);
      }
    }
    if(isset($_GET['newpass'])){
      $verif = $_GET['newpass'];
      $email = $_GET['email'];
      if($user_id = email_exists($email)){
        if($verif == get_user_meta($user_id,'verif', true)){

          $password = '';
          if(isset($_POST['acma-changepassword'])){
            if(wp_verify_nonce($_POST['changepassword_nonce'], 'acmachangepassword-nonce')) {
              $password = $wpdb->escape($_POST['password']);
            }
          }

          if(strlen($template_url = locate_template('/forms/change-password.php', false)) > 0){
            require_once($template_url);
          }
          else{
            require_once(dirname(LDWAM_FILE) . '/templates/forms/change-password.php');
          }
          return;

        }
        else{
          $acma_notif = array(
            'type'  =>  'warning',
            'message' =>  acma_trad('lostpassword.verif_novalid')
          );
        }
      }
    }

    if(strlen($template_url = locate_template('/forms/lost-password.php', false)) > 0){
      require_once($template_url);
    }
    else{
      require_once(dirname(LDWAM_FILE) . '/templates/forms/lost-password.php');
    }
  }

  public function login_form(){
    global $wpdb;
    $email = '';
    $password = '';
    if(isset($_POST['acma-login'])){
      if(wp_verify_nonce($_POST['login_nonce'], 'acmalogin-nonce')) {
        $email = $wpdb->escape($_POST['email']);
        $password = $wpdb->escape($_POST['password']);
      }
    }

    if(strlen($template_url = locate_template('/forms/login.php', false)) > 0){
      require_once($template_url);
    }
    else{
      require_once(dirname(LDWAM_FILE) . '/templates/forms/login.php');
    }
  }

  public function lostpassword_url($url){
    $lost_password_id = get_option('acma-lost_password_id', -1);
    if($lost_password_id == -1) return $url;
    else return get_permalink($lost_password_id);
  }

}
?>
