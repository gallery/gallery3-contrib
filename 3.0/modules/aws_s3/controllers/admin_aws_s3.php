<?php

class Admin_Aws_S3_Controller extends Admin_Controller {

    public function index() {
        // require_once(MODPATH . "aws_s3/lib/s3.php");
        
        $form = $this->_get_s3_form();

        if (request::method() == "post") {
            access::verify_csrf();

            if ($form->validate()) {
                module::set_var("aws_s3", "enabled",     (isset($_POST['enabled']) ? true : false));
                module::set_var("aws_s3", "access_key",  $_POST['access_key']);
                module::set_var("aws_s3", "secret_key",  $_POST['secret_key']);
                module::set_var("aws_s3", "bucket_name", $_POST['bucket_name']);
                module::set_var("aws_s3", "g3id",        $_POST['g3id']);

                module::set_var("aws_s3", "url_str",     $_POST['url_str']);
                module::set_var("aws_s3", "sig_exp",     $_POST['sig_exp']);

                module::set_var("aws_s3", "use_ssl",     (isset($_POST['use_ssl']) ? true : false));

                if (module::get_var("aws_s3", "enabled") && !module::get_var("aws_s3", "synced", false))
                        site_status::warning(
                          t('Your site has not yet been syncronised with your Amazon S3 bucket. Content will not appear correctly until you perform syncronisation. <a href="%url" class="g-dialog-link">Fix this now</a>',
                            array("url" => html::mark_clean(url::site("admin/maintenance/start/aws_s3_task::sync?csrf=__CSRF__")))
                          ), "aws_s3_not_synced");


                message::success(t("Settings have been saved"));
                url::redirect("admin/aws_s3");
            }
            else {
                message::error(t("There was a problem with the submitted form. Please check your values and try again."));
            }
        }
        
        $v = new Admin_View("admin.html");
        $v->page_title = t("Amazon S3 Configuration");
        $v->content = new View("admin_aws_s3.html");
        $v->content->form = $form;
        $v->content->end = "";

        echo $v;
    }

    private function _get_s3_form() {
        $form = new Forge("admin/aws_s3", "", "post", array("id" => "g-admin-s3-form"));

        $group = $form->group("aws_s3")->label(t("Amazon S3 Settings"));

        $group  ->checkbox("enabled")
                ->id("s3-enabled")
                ->checked(module::get_var("aws_s3", "enabled"))
                ->label("S3 enabled");

        $group  ->input("access_key")
                ->id("s3-access-key")
                ->label("Access Key ID")
                ->value(module::get_var("aws_s3", "access_key"))
                ->rules("required")
                ->error_messages("required", "This field is required")
                ->message('<a target="_blank" href="https://aws-portal.amazon.com/gp/aws/developer/account/index.html?ie=UTF8&action=access-key">Sign up to Amazon S3</a>');

        $group  ->input("secret_key")
                ->id("s3-secret-key")
                ->label("Secret Access Key")
                ->value(module::get_var("aws_s3", "secret_key"))
                ->rules("required")
                ->error_messages("required", "This field is required");

        $group  ->input("bucket_name")
                ->id("s3-bucket")
                ->label("Bucket Name")
                ->value(module::get_var("aws_s3", "bucket_name"))
                ->rules("required")
                ->error_messages("required", "This field is required")
                ->message("Note: This module will not create a bucket if it does not already exist. Please ensure you have already created the bucket and the bucket has the correct ACL permissions before continuing.");

        $group  ->input("g3id")
                ->id("s3-g3id")
                ->label("G3 ID")
                ->value(module::get_var("aws_s3", "g3id", md5(time())))
                ->rules("required")
                ->error_messages("required", "This field is required")
                ->message("This field allows for multiple G3 instances running off of a single S3 bucket.");

        $group  ->checkbox("use_ssl")
                ->id("s3-use-ssl")
                ->checked(module::get_var("aws_s3", "use_ssl"))
                ->label("Use SSL for S3 transfers");

        $group = $form->group("cdn_settings")->label(t("CDN Settings"));

        $group  ->input("url_str")
                ->id("s3-url-str")
                ->label("URL String")
                ->value(module::get_var("aws_s3", "url_str", "http://{bucket}.s3.amazonaws.com/g3/{guid}/{resource}"))
                ->rules("required")
                ->message("Configure the URL to access uploaded resources on the CDN. Use the following variables to define and build up the URL:<br />
&bull; {bucket} - Bucket Name<br />
&bull; {guid} - Unique identifier for this gallery installation<br />
&bull; {resource} - The end path to the resource/object");

        $group  ->input("sig_exp")
                ->id("sig_exp")
                ->label("Private Content Signature Duration")
                ->value(module::get_var("aws_s3", "sig_exp", 60))
                ->rules("required")
                ->callback("aws_s3::validate_number")
                ->error_messages("not_numeric", "The value provided is not numeric. Please enter a number in this field.")
                ->message("Set the time in seconds for the generated signature for access to permission-restricted S3 objects<br /><br />
Note: this module does not yet support the creation of signatures to access private objects on S3 via CloudFront CDN.");

        $form   ->submit("save")
                ->value("Save Settings");
        

        return $form;
    }

}