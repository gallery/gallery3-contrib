<?php defined("SYSPATH") or die("No direct script access.");

class allcomments_Controller extends Controller 
{
	public function index()
	{
	    url::redirect('allcomments/page/0');
	/*
	    $this->request->redirect('allcomments/page/0');
	    $comments = ORM::factory("comment")
     		 ->order_by("created", "DESC")
     		 ->where("state", "=", "published")
	         ->find_all();

	    $v = new Theme_View("page.html", "other", "profile");
	    $v->page_title = t("All comments");
   	    $v->content = new View("allcomments.html", array('comments' => $comments));
	    print $v;
	*/
	}


	public function page($page_no)
	{
	    $comments = ORM::factory("comment")
                 ->order_by("created", "DESC")
                 ->where("state", "=", "published")
		 ->limit(30)
		 ->offset($page_no*30)
                 ->find_all();
	
	/*
	    $pagination = new Pagination(array(
                'base_url'    => 'allcomments/page/', // Set our base URL to controller 'items' and method 'page'
                'uri_segment' => 'page', // Our URI will look something like http://domain/items/page/19
                'total_items' => 100 // Total number of items.
                 ));
	*/
            $v = new Theme_View("page.html", "other");
            $v->page_title = t("All comments");
            $v->content = new View("allcomments.html", array('comments' => $comments, 'page' => $page_no));
	    print $v;
	}
}
?>
