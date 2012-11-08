<?php

class event_watcher_event_Core {

  static function add_photos_form()
  {
    event_watcher::watch_event("add_photos_form",func_get_args());
  }

  static function add_photos_form_completed()
  {
    event_watcher::watch_event("add_photos_form_completed",func_get_args());
  }

  static function admin_menu()
  {
    event_watcher::watch_event("admin_menu",func_get_args());
  }

  static function album_add_form()
  {
    event_watcher::watch_event("album_add_form",func_get_args());
  }

  static function album_add_form_completed()
  {
    event_watcher::watch_event("album_add_form_completed",func_get_args());
  }

  static function album_menu()
  {
    event_watcher::watch_event("album_menu",func_get_args());
  }

  static function batch_complete()
  {
    event_watcher::watch_event("batch_complete",func_get_args());
  }

  static function captcha_protect_form()
  {
    event_watcher::watch_event("captcha_protect_form",func_get_args());
  }

  static function comment_add_form()
  {
    event_watcher::watch_event("comment_add_form",func_get_args());
  }

  static function comment_created()
  {
    event_watcher::watch_event("comment_created",func_get_args());
  }

  static function comment_updated()
  {
    event_watcher::watch_event("comment_updated",func_get_args());
  }

  static function context_menu()
  {
    event_watcher::watch_event("context_menu",func_get_args());
  }

  static function gallery_ready()
  {
    event_watcher::watch_event("gallery_ready",func_get_args());
  }

  static function gallery_shutdown()
  {
    event_watcher::watch_event("gallery_shutdown",func_get_args());
  }

  static function graphics_composite()
  {
    event_watcher::watch_event("graphics_composite",func_get_args());
  }

  static function graphics_composite_completed()
  {
    event_watcher::watch_event("graphics_composite_completed",func_get_args());
  }

  static function graphics_resize()
  {
    event_watcher::watch_event("graphics_resize",func_get_args());
  }

  static function graphics_resize_completed()
  {
    event_watcher::watch_event("graphics_resize_completed",func_get_args());
  }

  static function graphics_rotate()
  {
    event_watcher::watch_event("graphics_rotate",func_get_args());
  }

  static function graphics_rotate_completed()
  {
    event_watcher::watch_event("graphics_rotate_completed",func_get_args());
  }

  static function group_before_delete()
  {
    event_watcher::watch_event("group_before_delete",func_get_args());
  }

  static function group_deleted()
  {
    event_watcher::watch_event("group_deleted",func_get_args());
  }

  static function group_created()
  {
    event_watcher::watch_event("group_created",func_get_args());
  }

  static function group_updated()
  {
    event_watcher::watch_event("group_updated",func_get_args());
  }

  static function identity_provider_changed()
  {
    event_watcher::watch_event("identity_provider_changed",func_get_args());
  }

  static function item_before_create()
  {
    event_watcher::watch_event("item_before_create",func_get_args());
  }

  static function item_created()
  {
    event_watcher::watch_event("item_created",func_get_args());
  }

  static function item_before_delete()
  {
    event_watcher::watch_event("item_before_delete",func_get_args());
  }

  static function item_deleted()
  {
    event_watcher::watch_event("item_deleted",func_get_args());
  }

  static function item_edit_form()
  {
    event_watcher::watch_event("item_edit_form",func_get_args());
  }

  static function item_edit_form_completed()
  {
    event_watcher::watch_event("item_edit_form_completed",func_get_args());
  }

  static function item_index_data()
  {
    event_watcher::watch_event("item_index_data",func_get_args());
  }

  static function item_moved()
  {
    event_watcher::watch_event("item_moved",func_get_args());
  }

  static function item_related_update()
  {
    event_watcher::watch_event("item_related_update",func_get_args());
  }

  static function item_updated()
  {
    event_watcher::watch_event("item_updated",func_get_args());
  }

  static function item_updated_data_file()
  {
    event_watcher::watch_event("item_updated_data_file",func_get_args());
  }

  static function module_change()
  {
    event_watcher::watch_event("module_change",func_get_args());
  }

  static function movie_menu()
  {
    event_watcher::watch_event("movie_menu",func_get_args());
  }

  static function photo_menu()
  {
    event_watcher::watch_event("photo_menu",func_get_args());
  }

  static function pre_deactivate()
  {
    event_watcher::watch_event("pre_deactivate",func_get_args());
  }

  static function show_user_profile()
  {
    event_watcher::watch_event("show_user_profile",func_get_args());
  }

  static function site_menu()
  {
    event_watcher::watch_event("site_menu",func_get_args());
  }

  static function tag_menu()
  {
    event_watcher::watch_event("tag_menu",func_get_args());
  }

  static function theme_edit_form()
  {
    event_watcher::watch_event("theme_edit_form",func_get_args());
  }

  static function theme_edit_form_completed()
  {
    event_watcher::watch_event("theme_edit_form_completed",func_get_args());
  }

  static function user_add_form_admin()
  {
    event_watcher::watch_event("user_add_form_admin",func_get_args());
  }

  static function user_add_form_admin_completed()
  {
    event_watcher::watch_event("user_add_form_admin_completed",func_get_args());
  }

  static function user_auth()
  {
    event_watcher::watch_event("user_auth",func_get_args());
  }

  static function user_auth_failed()
  {
    event_watcher::watch_event("user_auth_failed",func_get_args());
  }

  static function user_before_delete()
  {
    event_watcher::watch_event("user_before_delete",func_get_args());
  }

  static function user_deleted()
  {
    event_watcher::watch_event("user_deleted",func_get_args());
  }

  static function user_change_email_form_completed()
  {
    event_watcher::watch_event("user_change_email_form_completed",func_get_args());
  }

  static function user_change_password_form()
  {
    event_watcher::watch_event("user_change_password_form",func_get_args());
  }

  static function user_change_password_form_completed()
  {
    event_watcher::watch_event("user_change_password_form_completed",func_get_args());
  }

  static function user_created()
  {
    event_watcher::watch_event("user_created",func_get_args());
  }

  static function user_edit_form_admin()
  {
    event_watcher::watch_event("user_edit_form_admin",func_get_args());
  }

  static function user_edit_form_admin_completed()
  {
    event_watcher::watch_event("user_edit_form_admin_completed",func_get_args());
  }

  static function user_edit_form()
  {
    event_watcher::watch_event("user_edit_form",func_get_args());
  }

  static function user_edit_form_completed()
  {
    event_watcher::watch_event("user_edit_form_completed",func_get_args());
  }

  static function user_login($user)
  {
    event_watcher::watch_event("user_login: ".get_class($user));
  }

  static function user_login_failed()
  {
    event_watcher::watch_event("user_login_failed",func_get_args());
  }

  static function user_logout()
  {
    event_watcher::watch_event("user_logout",func_get_args());
  }

  static function user_menu()
  {
    event_watcher::watch_event("user_menu",func_get_args());
  }

  static function user_password_change()
  {
    event_watcher::watch_event("user_password_change",func_get_args());
  }

  static function user_profile_contact_form()
  {
    event_watcher::watch_event("user_profile_contact_form",func_get_args());
  }

  static function user_updated()
  {
    event_watcher::watch_event("user_updated",func_get_args());
  }
}
