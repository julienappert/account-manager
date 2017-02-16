<?php
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

use Symfony\Component\Yaml\Yaml;

class AcMa_Admin{

  public function __construct(){
		add_action('init',array($this,'init'));
		add_action('admin_menu',array($this,'admin_menu'));
    add_action( 'admin_notices', array($this,'admin_notices') );
	}

  public function init(){
    global $acma, $acma_admin_notices;

    if(is_admin()){
      $theme_trads = $plugins_trads = array();
      $plugin_trads_path = dirname(LDWAM_FILE) . '/languages/admin-' . get_locale() . '.yaml';
      if(file_exists($plugin_trads_path))
        $plugins_trads = Yaml::parse(file_get_contents($plugin_trads_path));

      $theme_trads_path = locate_template('/languages/acma-admin-' . get_locale() . '.yaml');
      if(strlen($theme_trads_path)>0)
        $theme_trads = Yaml::parse(file_get_contents($theme_trads_path));

      $acma->trads = array_replace_recursive($plugins_trads, $theme_trads, $acma->trads);

      if(isset($_POST['acma-pages-submit'])){
        if(wp_verify_nonce($_POST['acma_pages_nonce'], 'acma-pages-nonce')) {
          update_option('acma-register_id', $_POST['register']);
          update_option('acma-login_id', $_POST['login']);
          update_option('acma-lost_password_id', $_POST['lost_password']);
          update_option('acma-account_id', $_POST['account']);

          $acma_admin_notices = acma_trad('settings_saved');
        }
      }

    }
  }

  public function admin_menu(){
    add_menu_page(
      'Account Manager',
      'Account Manager',
      'manage_options',
      'acma',
      array($this,'admin_page')
    );
    add_submenu_page(
      'acma',
      'Pages',
      'Pages',
      'manage_options',
      'acma-pages',
      array($this,'pages')
    );
  }
  public function admin_page(){

  }

  public function admin_notices(){
    global $acma_admin_notices;
    if(!is_null($acma_admin_notices)){
      echo '<div class="notice notice-success is-dismissible"><p>'.$acma_admin_notices.'</p></div>';
    }
  }

  public function pages(){

    $register_id = get_option('acma-register_id', -1);
    $login_id = get_option('acma-login_id', -1);
    $lost_password_id = get_option('acma-lost_password_id', -1);
    $account_id = get_option('acma-account_id', -1);
    ?>
    <div class="wrap">
      <h2><?php echo acma_trad('pages.title'); ?></h2>
      <form action="" method="post">
        <input type="hidden" name="acma_pages_nonce" value="<?php echo wp_create_nonce('acma-pages-nonce'); ?>"/>
        <table class="form-table">
          <tbody>

            <tr>
              <th scope="row">
                <label for="register"><?php echo acma_trad('pages.register'); ?></label>
              </th>
              <td>
                <?php wp_dropdown_pages(array(
                  'name'  =>  'register',
                  'id'  =>  'register',
                  'selected'  =>  $register_id,
                  'show_option_none'  =>  acma_trad('pages.select_one_page')
                )); ?>
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label for="login"><?php echo acma_trad('pages.login'); ?></label>
              </th>
              <td>
                <?php wp_dropdown_pages(array(
                  'name'  =>  'login',
                  'id'  =>  'login',
                  'selected'  =>  $login_id,
                  'show_option_none'  =>  acma_trad('pages.select_one_page')
                )); ?>
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label for="lost_password"><?php echo acma_trad('pages.lost_password'); ?></label>
              </th>
              <td>
                <?php wp_dropdown_pages(array(
                  'name'  =>  'lost_password',
                  'id'  =>  'lost_password',
                  'selected'  =>  $lost_password_id,
                  'show_option_none'  =>  acma_trad('pages.select_one_page')
                )); ?>
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label for="account"><?php echo acma_trad('pages.account'); ?></label>
              </th>
              <td>
                <?php wp_dropdown_pages(array(
                  'name'  =>  'account',
                  'id'  =>  'account',
                  'selected'  =>  $account_id,
                  'show_option_none'  =>  acma_trad('pages.select_one_page')
                )); ?>
              </td>
            </tr>

          </tbody>
        </table>
        <p class="submit">
          <input type="submit" class="button button-primary" name="acma-pages-submit" value="<?php echo acma_trad('pages.submit'); ?>">
        </p>
      </form>
    </div>
    <?php
  }
}
?>
