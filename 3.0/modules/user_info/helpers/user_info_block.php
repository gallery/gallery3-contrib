<?php defined("SYSPATH") or die("No direct script access.");
class user_info_block_Core {
  static function get_admin_list() {
    return array("user_info" => t("User Information"));
  }

  static function get($block_id) {
    $block = new Block();
    switch ($block_id) {
    case "user_info":
      $block->css_id = "g-user_info";
      $block->title = t("User Information");
      $block->content = new View("admin_block_user_info.html");
	  $block->content->number_of_records = ORM::factory("user_info")->count_all();

	// helps build the pagniation
	$page_size = module::get_var("user_info", "per_page");
    $page = Input::instance()->get("page", "1");
    $builder = db::build();
    $user_count = $builder->from("user_infos")->count_records();
    $block->content->pager = new Pagination();
    $block->content->pager->initialize(
      array("query_string" => "page",
            "total_items" => $user_count,
            "items_per_page" => $page_size,
            "style" => "classic"));
    // Make sure that the page references a valid offset
    if ($page < 1) {
// This prevents the admin page from displaying if there are no records in the database, commented out to temp. fix
//      url::redirect(url::merge(array("page" => 1)));
		url::site("admin"); //This should fix the issue I think
    } else if ($page > $block->content->pager->total_pages) {
      url::redirect(url::merge(array("page" => $block->content->pager->total_pages)));
    }
	// Get the user defined settings for sort by and sort order
	$default_sort_column = module::get_var("user_info", "default_sort_column");
	$default_sort_order = module::get_var("user_info", "default_sort_order");
    $block->content->data = ORM::factory("user_info")
        ->order_by($default_sort_column, $default_sort_order)
        ->find_all($page_size, $block->content->pager->sql_offset);

//	  $block->content->data = ORM::factory("user_info")->find_all();

	  $block->content->use_default_gallery_date_format = module::get_var("user_info", "use_default_gallery_date_format");
	  $block->content->date_format = module::get_var("user_info", "date_format");
	  $block->content->color_login = module::get_var("user_info", "color_login");
	  $block->content->color_logout = module::get_var("user_info", "color_logout");
	  $block->content->color_failed_login = module::get_var("user_info", "color_failed_login");
	  $block->content->color_re_authenticate_login = module::get_var("user_info", "color_re_authenticate_login");
	  $block->content->color_user_created = module::get_var("user_info", "color_user_created");

      break;
    }
    return $block;
  }
}
