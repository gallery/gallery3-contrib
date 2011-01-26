<?php

class Admin_Aws_S3_Controller extends Admin_Controller {

    public function index() {
        $form = $this->_get_s3_form();

        if (request::method() == "post") {
            access::verify_csrf();

            if (($valid_form = $form->validate()) && 
                ($s3_axs_correct = aws_s3::validate_access_details($_POST['access_key'], $_POST['secret_key'], $_POST['bucket_name']))) {

                // get variable values before changes so we can act on certain changes later
                $vars = array();
                foreach (ORM::factory("var")->where("module_name", "=", "aws_s3")->find_all() as $var) {
                    $vars[$var->name] = $var->value;
                }
                
                // set variables from $_POST into module::set_var() to save
                module::set_var("aws_s3", "enabled",          (isset($_POST['enabled']) ? true : false));
                module::set_var("aws_s3", "access_key",       $_POST['access_key']);
                module::set_var("aws_s3", "secret_key",       $_POST['secret_key']);
                module::set_var("aws_s3", "bucket_name",      $_POST['bucket_name']);
                site_status::clear("aws_s3_not_configured");

                module::set_var("aws_s3", "g3id",             $_POST['g3id']);

                module::set_var("aws_s3", "url_str",          $_POST['url_str']);
                module::set_var("aws_s3", "sig_exp",          $_POST['sig_exp']);

                module::set_var("aws_s3", "use_ssl",          (isset($_POST['use_ssl']) ? true : false));

                module::set_var("aws_s3", "upload_thumbs",    (isset($_POST['upload_thumbs']) ? true : false));
                module::set_var("aws_s3", "upload_resizes",   (isset($_POST['upload_resizes']) ? true : false));
                module::set_var("aws_s3", "upload_fullsizes", (isset($_POST['upload_fullsizes']) ? true : false));

                module::set_var("aws_s3", "s3_storage_only",  (isset($_POST['s3_storage_only']) ? true : false));
                
                // post option processing
//                if (module::get_var("aws_s3", "s3_storage_only") && !module::get_var("aws_s3", "enabled")) {
//                    module::set_var("aws_s3", "enabled",          true);
//                    module::set_var("aws_s3", "upload_thumbs",    true);
//                    module::set_var("aws_s3", "upload_resizes",   true);
//                    module::set_var("aws_s3", "upload_fullsizes", true);
//                }
//                if (module::get_var("aws_s3", "s3_storage_only") && !$vars['s3_storage_only']) {
//                    // content needs remove from local storage as it wasn't switched on before this point.
//                    if (!module::get_var("aws_s3", "synced")) {
//                        // force a sync between local storage and S3, as we're about to remove content from local storage.
//                    }
//                }
//                else if (!module::get_var("aws_s3", "s3_storage_only") && $vars['s3_storage_only']) {
//                    // content needs to be downloaded from s3 as it was just switched off. at this point,
//                    // we shouldn't actually have a copy of the gallery content locally.
//                }

                if (module::get_var("aws_s3", "enabled") && !module::get_var("aws_s3", "synced", false)) {
                    if (aws_s3::can_schedule()) {
                        // i can schedule this task
                        aws_s3::schedule_full_sync2();
                        site_status::warning(
                            "Your site has been scheduled for full Amazon S3 re-synchronisation. This message will clear when this has been completed.",
                            "aws_s3_not_synced"
                        );
                    }
                    else {
                        // i CAN'T schedule it..
                        site_status::warning(
                            t('Your site has not been synchronised to Amazon S3. Until it has, your server will continue to serve image content to your visitors. Click <a href="%url" class="g-dialog-link">here</a> to start the synchronisation task.',
                              array("url" => html::mark_clean(url::site("admin/maintenance/start/aws_s3_task::manual_sync?csrf=__CSRF__")))
                            ),
                            "aws_s3_not_synced"
                        );
                    }
                }

                message::success(t("Settings have been saved"));
                url::redirect("admin/aws_s3");
            }
            else {
                if (!$valid_form)
                    message::error(t("There was a problem with the submitted form. Please check your values and try again."));
                if (!$s3_axs_correct) {
                    message::error(t("The Amazon S3 access details provided appear to be incorrect. Please check your values and try again."));
                    $form->aws_s3->access_key->add_error("invalid", true);
                    $form->aws_s3->secret_key->add_error("invalid", true);
                    $form->aws_s3->bucket_name->add_error("invalid", true);
                }
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

        $chkbox =
            $group  ->checkbox("enabled")
                    ->id("s3-enabled")
                    ->checked(module::get_var("aws_s3", "enabled", true))
                    ->label("S3 enabled");
        
        if (module::get_var("aws_s3", "s3_storage_only"))
                $chkbox->disabled(true)
                       ->message("<strong>Warning</strong>:You may not turn this option off as <strong>S3 Storage Only</strong> is enabled. In order to disable using S3, you must first disable <strong>S3 Storage Only</strong> to re-download your content from Amazon S3, since it does not yet exist on the local server.");

        $group  ->input("access_key")
                ->id("s3-access-key")
                ->label("Access Key ID")
                ->value(module::get_var("aws_s3", "access_key"))
                ->rules("required")
                ->error_messages("required", "This field is required")
                ->error_messages("invalid", "Access Key is invalid")
                ->message('<a target="_blank" href="https://aws-portal.amazon.com/gp/aws/developer/account/index.html?ie=UTF8&action=access-key">Click here</a> to sign up to Amazon Web Services.');

        $group  ->input("secret_key")
                ->id("s3-secret-key")
                ->label("Secret Access Key")
                ->value(module::get_var("aws_s3", "secret_key"))
                ->rules("required")
                ->error_messages("required", "This field is required")
                ->error_messages("invalid", "Secret Key is invalid");

        $group  ->input("bucket_name")
                ->id("s3-bucket")
                ->label("Bucket Name")
                ->value(module::get_var("aws_s3", "bucket_name"))
                ->rules("required")
                ->callback("aws_s3::validate_bucket")
                ->error_messages("required", "This field is required")
                ->error_messages("invalid", "Bucket name is invalid")
                ->message('Note: This module will not create a bucket if it does not already exist. Please ensure you have already created the bucket using the <a href="https://console.aws.amazon.com/s3/home" target="_blank">AWS Console</a> before continuing.<br />
<a href="http://docs.amazonwebservices.com/AmazonS3/latest/index.html?UsingBucket.html" tar=get="_blank">Click here</a> for information on Amazon S3 bucket naming conventions/restrictions.');

        $group  ->input("g3id")
                ->id("s3-g3id")
                ->label("G3 ID")
                ->value(module::get_var("aws_s3", "g3id", md5(time())))
                ->rules("required")
                ->error_messages("required", "This field is required")
                ->message("Utilising this field allows for multiple G3 file repositories stored inside the same S3 bucket.");

        $group  ->checkbox("use_ssl")
                ->id("s3-use-ssl")
                ->checked(module::get_var("aws_s3", "use_ssl"))
                ->label("Use SSL for S3 transfers")
                ->message("<span style='font-style: italic'>You may have problems when uploading content to S3 if this option is enabled. If so, turn off this option.</span>");

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
                ->id("s3-sig_exp")
                ->label("Private Content Signature Duration")
                ->value(module::get_var("aws_s3", "sig_exp", 60))
                ->rules("required")
                ->callback("aws_s3::validate_number")
                ->error_messages("not_numeric", "The value provided is not numeric. Please enter a number in this field.")
                ->message("Set the time in seconds until the generated signature expires access to permission-restricted S3 objects (private content on G3 is where the user group 'Everybody' does not have access).<br /><br />
<strong>Note</strong>: this module does not yet support the creation of signatures to access private objects on S3 via CloudFront CDN.");

        $group = $form->group("general_settings")->label(t("General Settings"));

        $chkbox =
            $group  ->checkbox("upload_thumbs")
                    ->id("s3-upload_thumbs")
                    ->label("Upload Thumbnails")
                    ->checked(module::get_var("aws_s3", "upload_thumbs", true));
        if (module::get_var("aws_s3", "s3_storage_only"))
                $chkbox->disabled(true);

        $chkbox =
            $group  ->checkbox("upload_resizes")
                    ->id("s3-upload_resizes")
                    ->label("Upload Resized Images")
                    ->checked(module::get_var("aws_s3", "upload_resizes", true));
        if (module::get_var("aws_s3", "s3_storage_only"))
                $chkbox->disabled(true);

        $chkbox =
            $group  ->checkbox("upload_fullsizes")
                    ->id("s3-upload_fullsizes")
                    ->label("Upload Fullsize Images")
                    ->checked(module::get_var("aws_s3", "upload_fullsizes", true));
        if (module::get_var("aws_s3", "s3_storage_only"))
                $chkbox->disabled(true);

        $chkbox =
            $group  ->checkbox("s3_storage_only")
                    ->id("s3-storage-only")
                    ->label("Use S3 for primary storage of Gallery content (Not yet available)")
                    ->checked(module::get_var("aws_s3", "s3_storage_only", false))
                    ->message("Use this option if your webhost has limited space available on your account. This module will remove content from the local server after it has been uploaded to S3.<br /><br />
<strong>Note</strong>: You must have enough storage on your webhost account to store the images temporarily until they have been uploaded to S3.<br />")
                    ->disabled(true);

        if (!module::get_var("aws_s3", "enabled"))
            $chkbox->disabled(true);

        // done creating form.
        $form   ->submit("save")
                ->value("Save Settings");
        

        return $form;
    }

}