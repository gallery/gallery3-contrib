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
 
class Ecard_Controller extends Controller {
  /**
   * Send the ecard.
   */
  public function send($id) {
    $item = ORM::factory("item", $id);
    access::required("view", $item);
    if (!ecard::can_send_ecard()) {
      access::forbidden();
    }
    $form = ecard::get_send_form($item);
	try {
      $valid = $form->validate();
    } catch (ORM_Validation_Exception $e) {
      // Translate ORM validation errors into form error messages
      foreach ($e->validation->errors() as $key => $error) {
        $form->edit_item->inputs[$key]->add_error($error, 1);
      }
      $valid = false;
    }
	
	if ($valid) {
	  $to_array = explode(",",$form->send_ecard->inputs["to_email"]->value);
	  foreach($to_array as $to) {
		  $v = new View("ecard_email.html");
		  $v->item = $item;
		  $v->subject = module::get_var("ecard", "subject");
		  $from_name = $form->send_ecard->from_name->value;
		  $bcc = module::get_var("ecard", "bcc");
		  if($form->send_ecard->send_to_self->checked == true) {
			$cc = $form->send_ecard->inputs["from_email"]->value;
		  }	  
		  $v->message = t(module::get_var("ecard", "message"), array("fromname" => $from_name));
		  $v->custom_message = $form->send_ecard->text->value;
		  $v->image = $item->name;
		  $from = $form->send_ecard->inputs["from_email"]->value;
		  $headers = array("from" => $from_name."<".$from.">", "to" => $to, "subject" => module::get_var("ecard", "subject"));
		  require_once(MODPATH. "ecard/lib/mime.php");
		  $mime = new Mail_mime("\n");
		  $mime->setHTMLBody($v->render());
		  if($form->send_ecard->send_fresh->checked == true) {
			  $tmpfile = tempnam(TMPPATH, "clean");
			  if($form->send_ecard->send_thumbnail->checked == true) {
				$options = array("width" => module::get_var("gallery", "thumb_size"), "height" => module::get_var("gallery", "thumb_size"), "master" => Image::AUTO);
				gallery_graphics::resize($item->file_path(), $tmpfile, $options);
				$mime->addHTMLImage($tmpfile,$item->mime_type,$item->name);
			  } else {
				$options = array("width" => module::get_var("gallery", "resize_size"), "height" => module::get_var("gallery", "resize_size"), "master" => Image::AUTO);
				gallery_graphics::resize($item->file_path(), $tmpfile, $options);
				$mime->addHTMLImage($tmpfile,$item->mime_type,$item->name);
			  }
		  } else {				
			  if($form->send_ecard->send_thumbnail->checked == true) {
				$mime->addHTMLImage($item->thumb_path(),$item->mime_type,$item->name);
			  } else {
				$mime->addHTMLImage($item->resize_path(),$item->mime_type,$item->name);
			  }
		  }
		  $body = $mime->get(array('html_charset'  => 'UTF-8', 'text_charset'  => 'UTF-8','text_encoding' => '8bit','head_charset'  => 'UTF-8'));
		  self::_notify($headers['to'], $headers['from'], $headers['subject'], $item, $body, $mime->headers(), $bcc, $cc);
		}
	  unlink($tmpfile);
	  message::success("eCard successfully sent");
	  json::reply(array("result" => "success"));
	} else {
	  json::reply(array("result" => "error", "html" => (string) $form));
	}
  }
  /**
   * Present a form for sending a new ecard.
   */
  public function form_send($item_id) {
    $item = ORM::factory("item", $item_id);
    access::required("view", $item);
    if (!ecard::can_send_ecard()) {
      access::forbidden();
    }
	$v_form = new View("ecard_form.html");
	$v_form->item_id = $item_id;
	print $v_form->render();
  }  
  private static function _notify($to, $from, $subject, $item, $text, $headers, $bcc, $cc) {
      $sendmail = Sendmail::factory();
      $sendmail
        ->to($to)
	->from($from)
	->reply_to($from)
        ->subject($subject);
	  if(isset($bcc)) {
	    $sendmail->header("bcc",$bcc);
	  }
	  if(isset($cc)) {
		$sendmail->header("cc",$cc);
	  }	  
	  foreach($headers as $key => $value) {
		$sendmail->header($key,$value);
	  }
      $sendmail
		->message($text)
        ->send();
	  return;
  }  
}
