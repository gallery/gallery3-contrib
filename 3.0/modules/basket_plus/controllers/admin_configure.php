<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2013 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */

class Admin_Configure_Controller extends Controller
{
  /**
   * the index page of the user homes admin
   */
  public function index()
  {
    $form = basket_plus::get_configure_form();
    if (request::method() == "post") {
      access::verify_csrf();

      if ($form->validate()) {

        basket_plus::extractForm($form);
        message::success(t("Basket Module Configured!"));
      }
    }
    else
    {
      basket_plus::populateForm($form);
    }

    $view = new Admin_View("admin.html");
    $view->content = new View("admin_configure.html");

    $view->content->form = $form;

    print $view;
  }

  /**
   * the index page of the user homes admin
   */
  public function templates()
  {
    $form = basket_plus::get_template_form();
    if (request::method() == "post") {
      access::verify_csrf();

      if ($form->validate()) {

        basket_plus::extractTemplateForm($form);
        message::success(t("Basket Module Configured!"));
      }
    }
    else
    {
      basket_plus::populateTemplateForm($form);
    }

    $view = new Admin_View("admin.html");
    $view->content = new View("admin_templates.html");

    $view->content->form = $form;

    print $view;
  }

  public function translates()
  {
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_translates.html");
    print $view;
  }

  public function paypal_encrypt_wizard_step1()
  {
    $view = new Admin_View("admin.html");
    $view->content = new View("pew1.html");

    $view->content->form = self::keyGenerationForm();

    print $view;

  }

  public function paypal_encrypt_wizard_step2()
  {
    access::verify_csrf();

    $form = self::keyGenerationForm();

    if (!$form->validate()) {

      self::paypal_encrypt_wizard_step1();
      return;
    }

    $ssldir = str_replace('\\','/',VARPATH.'certificate');
    $ssldir= rtrim($ssldir, '/').'/';

    if ( ! is_dir($ssldir))
    {
        // Create the upload directory
        mkdir($ssldir, 0777, TRUE);
      }

    $prkeyfile = $ssldir . "myprvkey.pem";
    $pubcertfile = $ssldir . "mypubcert.pem";
    $certreqfile = $ssldir . "mycertreq.pem";

    $dn = array("countryName" => $form->encrypt->countryName->value,
    "stateOrProvinceName" => $form->encrypt->stateOrProvinceName->value,
    "localityName" => $form->encrypt->localityName->value,
    "organizationName" => $form->encrypt->organizationName->value,
    "organizationalUnitName" => $form->encrypt->organizationalUnitName->value,
    "commonName" => $form->encrypt->commonName->value,
    "emailAddress" => $form->encrypt->emailAddress->value);
    $privkeypass = $form->encrypt->privKeyPass->value;
    $numberofdays = 365;
    $config = array(
      "private_key_bits" => 1024
    );

    $privkey = openssl_pkey_new($config);
    $csr = openssl_csr_new($dn, $privkey);
    $sscert = openssl_csr_sign($csr, null, $privkey, $numberofdays);
    openssl_x509_export($sscert, $publickey);
    openssl_pkey_export($privkey, $privatekey, $privkeypass);
    openssl_csr_export($csr, $csrStr);

    openssl_x509_export_to_file($sscert, $pubcertfile);
    openssl_pkey_export_to_file ($privkey, $prkeyfile, $privkeypass);
    openssl_csr_export_to_file($csr, $certreqfile);

    //echo "Your Public Certificate has been saved to " . $pubcertfile . "<br><br>";
    //echo "Your Private Key has been saved to " . $prkeyfile . "<br><br>";
    //echo "Your Certificate Request has been saved to " . $certreqfile . "<br><br>";

    //echo $privatekey; // Will hold the exported PriKey
    //echo $publickey; // Will hold the exported PubKey
    //echo $csrStr; // Will hold the exported Certificate
  }

  private function keyGenerationForm()
  {
    $form = new Forge("admin/configure/paypal_encrypt_wizard_step2", "", "post", array("id" => "generateKeys", "name" =>"generateKeys"));
    $group = $form->group("encrypt")->label(t("Key Generation Details"));
    $group->input("countryName")->label(t("Country Name"))->id("countryName");
    $group->input("stateOrProvinceName")->label(t("State or Province Name"))->id("stateOrProvinceName");
    $group->input("localityName")->label(t("Locality Name"))->id("localityName");
    $group->input("organizationName")->label(t("Organization Name"))->id("organizationName");
    $group->input("organizationalUnitName")->label(t("Organizational Unit Name"))->id("organizationalUnitName");
    $group->input("commonName")->label(t("Common Name"))->id("commonName");
    $group->input("emailAddress")->label(t("E-Mail Address"))->id("emailAddress");
    $group->input("privKeyPass")->label(t("Private Key Pass"))->id("privkeypass");
    return $form;
  }

}
