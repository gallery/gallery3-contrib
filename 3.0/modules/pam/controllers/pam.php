<?php defined("SYSPATH") or die("No direct script access.");

/**
 * Enables authentication via multiple external services using a plugin architecture.
 */
class pam_Controller extends Login_Controller {

  private $pam_auth;
  private $plugins;
  private $plugin_path;
  private $create_account;

  /**
   * for now just run _setup
   */
  public function  __construct()
  {
    $this->_setup();
  }

  /**
   * default action for the pam controller
   */
  public function index() {
    $view = new Theme_View("page.html", "other", "login");
    $view->page_title = t("Login");
    $view->content = auth::get_login_form("pam/auth_html");
    print $view;
  }

  /**
   * process login form
   */
  public function auth_html() {
  access::verify_csrf();

    list ($valid, $form) = $this->_auth("pam/auth__html");
    if ($valid) {
      $continue_url = $form->continue_url->value;
      url::redirect($continue_url ? $continue_url : item::root()->abs_url());
    } else {
      $view = new Theme_View("page.html", "other", "login");
      $view->page_title = t("Log in to Gallery");
      $view->content = new View("auth_ajax.html");
      $view->content->form = $form;
      print $view;
    }
  }

  /**
   * display the login form via ajax
   */
  public function ajax() {
    $view = new View("pam_ajax.html");
    $view->form = auth::get_login_form("pam/auth_ajax");
    print $view;
  }

  /**
   * process login form via ajax
   */
  public function auth_ajax() {
  access::verify_csrf();

    list ($valid, $form) = $this->_auth("pam/auth_ajax");
    if ($valid) {
      print json_encode(
        array("result" => "success"));
    } else {
      print json_encode(array("result" => "error", "form" => (string) $form));
    }
  }


  /**
   * authenticate the user
   *
   * @param string $url
   * @return boolean
   */
  private function _auth($url) {
    $form = auth::get_login_form($url);
    $validform = $form->validate();
    $valid = false;

    if ($validform) {

      // retrieve the values from the form
      $name = $form->login->inputs["name"]->value;
      $pass = $form->login->password->value;

      // do we have a user?
      $user = identity::lookup_user_by_name($name);
      $validuser = empty($user)?false:true;

      // is the user authentic?
      $checkpass = $this->_checkpass($name,$pass);

      /*
       * we are concerned with these three possibilities:
       * 1. there is no valid user or no valid password
       * 2. there is no valid user but a valid password
       * 3. there is a valid user and a valid password
       */

      // 1. there is no valid user or no valid password: error
      if (!$validuser || !$checkpass) {
        $form->login->inputs["name"]->add_error("invalid_login", 1);
        $name = $form->login->inputs["name"]->value;
        log::warning("user", t("Failed login for %name", array("name" => $name)));
        module::event("user_auth_failed", $name);
      }

      // 2. there is no valid user but a valid password: create account if allowed
      if (!$validuser && $checkpass && $this->create_account) {
        $account = $this->pam_auth->getAccount();
        if ($account){
          $password = md5(uniqid(mt_rand(), true));
          $new_user = identity::create_user($account->name, $account->full_name, $password, $account->email);
          $new_user->url = '';
          $new_user->admin = false;
          $new_user->guest = false;
          $new_user->save();
          $user = identity::lookup_user_by_name($account->name);
          $validuser = empty($user)?false:true;
        }
      }

      // 3. there is a valid user and a valid password: load user account
      if ($validuser && $checkpass) {
        auth::login($user);
        $valid = true;
      }
    }

    // regenerate the session id to avoid session trapping
    Session::instance()->regenerate();

    return array($valid, $form);
  }

  /**
   * check the login name/pass pair against registered services
   *
   * @param string $name
   * @param string $pass
   * @return boolean
   *
   */
  private function _checkpass($name, $pass)
  {
    // assume failure
    $result = false;

    // maybe this should be moved to _setup()?
    if ($this->plugins) {
      $plugins = $this->plugins;
    }
    else {
      $plugins = $this->_read_plugins();
    }

    $path_template = $this->plugin_path . '/%s/%s.php';

    // loop over the plugins
    foreach ($plugins as $plugin){
      // load and instantiate the class
      require sprintf($path_template,$plugin,$plugin);
      $class = 'pam_'.$plugin;
      $this->pam_auth = new $class($name, $pass);
      $result = $this->pam_auth->isAuth();
      /*
       * if user is authenticated then leave the loop/method.
       * the $this->pam_auth class is used to create a new account
       */
      if ($result) return $result;
    }

    return $result;
  }

  /**
   * scan the plugin directory and build a list of names
   *
   * @return array
   */
  private function _read_plugins() {
    $plugins = array();
    $plugin_path = MODPATH .'pam/plugins';

    // build plugin list from plugin folder
    $d = dir($plugin_path);
    while (false !== ($entry = $d->read())) {
      if ($entry != "." && $entry != "..")  $plugins[] = $entry ;
    }
    $d->close();
    return $plugins;
  }

  /**
   * load the config and set some values
   */
  private function _setup() {

    $default_plugin_path = MODPATH .'pam/plugins';

    if (file_exists(MODPATH .'pam/config/config.php')) {
      include MODPATH .'pam/config/config.php';
    }

    $this->plugins = isset($plugins)?$plugins:false;
    $this->plugin_path = isset($plugin_path)?$plugin_path:$default_plugin_path;
    $this->create_account = isset($create_account)?$create_account:false;

  }

}