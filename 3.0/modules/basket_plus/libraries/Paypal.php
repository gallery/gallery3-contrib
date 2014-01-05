<?php
/*******************************************************************************
 *                      PHP Paypal IPN Integration Class
 *******************************************************************************
 *      Author:     Micah Carrick
 *      Email:      email@micahcarrick.com
 *      Website:    http://www.micahcarrick.com
 *
 *      File:       paypal.class.php
 *      Version:    1.3.0
 *      Copyright:  (c) 2005 - Micah Carrick
 *                  You are free to use, distribute, and modify this software
 *                  under the terms of the GNU General Public License.  See the
 *                  included license.txt file.
 *
 *******************************************************************************
 *  VERION HISTORY:
 *      v1.3.0 [10.10.2005] - Fixed it so that single quotes are handled the
 *                            right way rather than simple stripping them.  This
 *                            was needed because the user could still put in
 *                            quotes.
 *
 *      v1.2.1 [06.05.2005] - Fixed typo from previous fix :)
 *
 *      v1.2.0 [05.31.2005] - Added the optional ability to remove all quotes
 *                            from the paypal posts.  The IPN will come back
 *                            invalid sometimes when quotes are used in certian
 *                            fields.
 *
 *      v1.1.0 [05.15.2005] - Revised the form output in the submit_paypal_post
 *                            method to allow non-javascript capable browsers
 *                            to provide a means of manual form submission.
 *
 *      v1.0.0 [04.16.2005] - Initial Version
 *
 *******************************************************************************
 *  DESCRIPTION:
 *
 *      NOTE: See www.micahcarrick.com for the most recent version of this class
 *            along with any applicable sample files and other documentaion.
 *
 *      This file provides a neat and simple method to interface with paypal and
 *      The paypal Instant Payment Notification (IPN) interface.  This file is
 *      NOT intended to make the paypal integration "plug 'n' play". It still
 *      requires the developer (that should be you) to understand the paypal
 *      process and know the variables you want/need to pass to paypal to
 *      achieve what you want.
 *
 *      This class handles the submission of an order to paypal aswell as the
 *      processing an Instant Payment Notification.
 *
 *      This code is based on that of the php-toolkit from paypal.  I've taken
 *      the basic principals and put it in to a class so that it is a little
 *      easier--at least for me--to use.  The php-toolkit can be downloaded from
 *      http://sourceforge.net/projects/paypal.
 *
 *      To submit an order to paypal, have your order form POST to a file with:
 *
 *          $p = new paypal_class;
 *          $p->add_field('business', 'somebody@domain.com');
 *          $p->add_field('first_name', $_POST['first_name']);
 *          ... (add all your fields in the same manor)
 *          $p->submit_paypal_post();
 *
 *      To process an IPN, have your IPN processing file contain:
 *
 *          $p = new paypal_class;
 *          if ($p->validate_ipn()) {
 *          ... (IPN is verified.  Details are in the ipn_data() array)
 *          }
 *
 *
 *      In case you are new to paypal, here is some information to help you:
 *
 *      1. Download and read the Merchant User Manual and Integration Guide from
 *         http://www.paypal.com/en_US/pdf/integration_guide.pdf.  This gives
 *         you all the information you need including the fields you can pass to
 *         paypal (using add_field() with this class) aswell as all the fields
 *         that are returned in an IPN post (stored in the ipn_data() array in
 *         this class).  It also diagrams the entire transaction process.
 *
 *      2. Create a "sandbox" account for a buyer and a seller.  This is just
 *         a test account(s) that allow you to test your site from both the
 *         seller and buyer perspective.  The instructions for this is available
 *         at https://developer.paypal.com/ as well as a great forum where you
 *         can ask all your paypal integration questions.  Make sure you follow
 *         all the directions in setting up a sandbox test environment, including
 *         the addition of fake bank accounts and credit cards.
 *
 *******************************************************************************
 */

class Paypal_Core {

  var $last_error;                 // holds the last error encountered
  var $ipn_response;               // holds the IPN response from paypal
  public $ipn_data = array();      // array contains the POST values for IPN
  var $fields = array();           // array holds the fields to submit to paypal

  public function __construct()
  {
    // initialization constructor.  Called when class is created.

		if (basket_plus::getBasketVar(PAYPAL_TEST_MODE)){
			// sandbox paypal
			$this->paypal_url =  "https://www.sandbox.paypal.com/cgi-bin/webscr";
			$this->secure_url =  "ssl://www.sandbox.paypal.com";
		}
		else{
    // normal paypal
			$this->paypal_url =  "https://www.paypal.com/cgi-bin/webscr";
			$this->secure_url =  "ssl://www.paypal.com";
		}

    $this->last_error = '';

    //$this->ipn_log_file = Kohana::log_directory().Kohana::config('paypal.ipn_logfile');
    //$this->ipn_log = true;
    $this->ipn_response = '';

    // populate $fields array with a few default values.  See the paypal
    // documentation for a list of fields and their data types. These default
    // values can be overwritten by the calling script.

  }

  function add_field($field, $value) {

    // adds a key=>value pair to the fields array, which is what will be
    // sent to paypal as POST variables.  If the value is already in the
    // array, it will be overwritten.

    $this->fields["$field"] = $value;
  }

  public function process($session_basket, $return_url, $cancel_url, $notify_url){

    $this->add_field('rm','2');
    $this->add_field('cmd','_cart');
    $this->add_field('upload','1');

    $this->add_field('currency_code', basket_plus::getCurrency());
    $this->add_field('business', basket_plus::getBasketVar(PAYPAL_ACCOUNT));

    // IPN stuff
    $this->add_field('return', $return_url);
    $this->add_field('cancel_return', $cancel_url);
    $this->add_field('notify_url', $notify_url);

    // postage
		$pickup = $session_basket->pickup;
    if (!$pickup){
      $postage = $session_basket->postage_cost();
      if ($postage > 0) {
        $this->add_field('shipping_1',$postage);
      }
    }

    // basket contents
    $id = 1;
    foreach ($session_basket->contents as $key => $basket_item){
      $this->add_field("item_name_$id", $basket_item->getCode());
      $this->add_field("amount_$id", $basket_item->product_cost_per);
      $this->add_field("quantity_$id",$basket_item->quantity);
      $id++;
    }

    // shipping address
    $this->add_field("payer_email", $session_basket->email);
    $this->add_field("address_name", $session_basket->fname);
    $this->add_field("address_street", $session_basket->house." ".$session_basket->street);
    $this->add_field("address_city", $session_basket->town);
    $this->add_field("address_zip", $session_basket->postalcode);
    $this->add_field("contact_phone", $session_basket->phone);

    $string = "<form method=\"post\" name=\"paypal_form\" "
    ."action=\"".$this->paypal_url."\">\n";

    foreach ($this->fields as $name => $value) {
      $string = $string."<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
    }

    $string = $string."</form>
		<script>
			function s_f(){
				document.forms[\"paypal_form\"].submit();
				}; 
			window.setTimeout(s_f,20);
		</script>";
    return $string;
  }

  function validate_ipn($key) {

    // parse the paypal URL
    $url_parsed = parse_url($this->paypal_url);

    // generate the post string from the _POST vars as well as load the
    // _POST vars into an array so we can play with them from the calling
    // script.
    $post_string = 'cmd=_notify-validate';
    foreach ($_POST as $field=>$value) {
      $this->ipn_data["$field"] = $value;
      $value = urlencode(stripslashes($value));
      $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);
      $post_string .= '&'.$field.'='.$value;
    }

    // open the connection to paypal
    $fp = fsockopen($this->secure_url,443,$err_num,$err_str,30);
    if(!$fp) {

      // could not open the connection.  If logging is on, the error message
      // will be in the log.
      $this->last_error = "fsockopen error no. $errnum: $errstr";
      $this->log_ipn_results($key,false);
      return false;

    } else {

      // Post the data back to paypal
      fputs($fp, "POST ".$url_parsed['path']." HTTP/1.1\r\n");
      fputs($fp, "Host: ".$url_parsed['host']."\r\n");
      fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");

      fputs($fp, "Content-length: ".strlen($post_string)."\r\n\r\n");
      //fputs($fp, "Connection: close\r\n\r\n");
      fputs($fp, $post_string . "\r\n\r\n");

      // loop through the response from the server and append to variable
      while(!feof($fp)) {
        $this->ipn_response .= fgets($fp, 1024);
      }
      fclose($fp); // close connection
    }

    if (stristr($this->ipn_response,"VERIFIED")===false){
      // Invalid IPN transaction.  Check the log for details.
      $this->last_error = 'IPN Validation Failed. '.$url_parsed['host'].'\\'.$url_parsed['path'];
      $this->log_ipn_results($key,false);
      return false;
    }
    else{
      // Valid IPN transaction.

      // check recievers e-mail
      $business = basket_plus::getBasketVar(PAYPAL_ACCOUNT);

      if ($this->ipn_data['receiver_email']!=$business){
        $this->last_error = 'receivers e-mail did not match '.$business;
        $this->log_ipn_results($key,false);
        return false;
      }

      // if confirmed check message has not been received already
      if ($this->ipn_data['payment_status'] == "Completed"){

        $message = ORM::factory("bp_ipn_message")
          ->where('key',"=",$key)
          ->where('status',"=",'completed')
          ->where('txn_id',"=",$this->ipn_data['txn_id'])->find();

        if ($message->loaded()){
          $this->last_error = 'Message already received.';
          $this->log_ipn_results($key,false);
          return false;
        }
      }

      $this->log_ipn_results($key,true);
      return true;
    }
  }

  function log_ipn_results($key, $success) {

    // Timestamp
    $text = '['.date('m/d/Y g:i A').'] - ';

    $message = ORM::factory("bp_ipn_message");
    $message->date = time();
    $message->key = $key;
    $message->txn_id = $this->ipn_data['txn_id'];
    $message->status = $this->ipn_data['payment_status'];
    $message->success = $success;

    // Success or failure being logged?
    if ($success) $text .= "SUCCESS!\n";
    else $text .= 'FAIL: '.$this->last_error."\n";

    // Log the POST variables
    $text .= "IPN POST Vars from Paypal:\n";
    foreach ($this->ipn_data as $key=>$value) {
      $text .= "$key=$value \n";
    }

    // Log the response from the paypal server
    $text .= "\nIPN Response from Paypal Server:\n ".$this->ipn_response;

    $message->text = $text;
    $message->save();
  }

  function dump_fields() {

    // Used for debugging, this function will output all the field/value pairs
    // that are currently defined in the instance of the class using the
    // add_field() function.

    echo "<h3>paypal_class->dump_fields() Output:</h3>";
    echo "<table width=\"95%\" border=\"1\" cellpadding=\"2\" cellspacing=\"0\">
            <tr>
               <td bgcolor=\"black\"><b><font color=\"white\">Field Name</font></b></td>
               <td bgcolor=\"black\"><b><font color=\"white\">Value</font></b></td>
            </tr>";

    ksort($this->fields);
    foreach ($this->fields as $key => $value) {
      echo "<tr><td>$key</td><td>".urldecode($value)."&nbsp;</td></tr>";
    }

    echo "</table><br>";
  }
}



