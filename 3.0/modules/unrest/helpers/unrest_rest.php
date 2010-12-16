<?php defined("SYSPATH") or die("No direct script access.");

class unrest_rest_Core {
	private static function resolveLimitOption($string)
	{
		$items = split(',', $string);
		if (count($items) == 1) { return $string; }
		return $items;
	}
	
	private static function getFreetextLimiters($request, $limit = array())
	{
		$likeMapping = array(
			'name' => 'name', 
			'description' => 'description'
		);
		foreach ($likeMapping as $key => $col)
		{
			if (isset($request->params->$key)) { $limit[$col] = array('op' => 'LIKE', 'value' => '%' . $request->params->$key . '%'); }
		}
		
		return $limit;
	}
	
	private static function getBasicLimiters($request, $limit = array())
	{
		$directMapping = array(
			'type' => 'type', 
			'id' => 'items.id',
			'parent' => 'parent_id',
			'mime' => 'mime_type');
		foreach ($directMapping as $key => $col)
		{
			if (isset($request->params->$key)) { $limit[$col] = array('op' => '=', 'value' => unrest_rest::resolveLimitOption($request->params->$key)); }
		}
		
		return $limit;
	}
	
	private static function albumsICanAccess()
	{
		$db = db::build();
      	$gids = identity::group_ids_for_active_user();
      	$q = $db->select('id')->from('items');
      	foreach ($gids as $gid) { $q->or_where('view_' . $gid, '=', 1); }
		
      	$q->where('type', '=', 'album');      	
      	$permitted = array();
      	foreach($q->execute() as $row) { $permitted[] = $row->id; }
      	
      	return $permitted;
	}
	
	static function queryLimitByPermission(&$query, $permitted)
	{
		$query->and_open()->and_open()->where('type', '=', 'album')->and_where('items.id', 'IN', $permitted)->close();
      	$query->or_open()->where('type', '!=', 'album')->and_where('parent_id', 'IN', $permitted)->close()->close();
	}
	
	static function baseItemQuery($db)
	{
		$fields = array(
      		'items.id', 'title', 'album_cover_item_id', 'description', 'height', 'width', 'left_ptr', 'right_ptr', 
      		'level', 'mime_type', 'name', 'owner_id', 'parent_id', 'relative_path_cache', 'relative_url_cache', 
      		'resize_dirty', 'slug', 'sort_column', 'sort_order', 'thumb_dirty','thumb_height', 'view_1', 'type',
      		'resize_height', 'resize_width', 'thumb_height', 'thumb_width', 'slug', 'name', 'relative_path_cache'
      	);
      	
      	$permfields = array('view_', 'view_full_', 'edit_', 'add_');
      	
      	foreach (identity::group_ids_for_active_user() as $album)
      	{
      		foreach ($permfields as $field)
      		{
	      		$fields[] = $field . $album;  		
      		}
      	}
      	
      	return($db->select($fields)->from('items')->join('access_caches', 'access_caches.item_id', 'items.id'));
/*
      	return($db->select(array(
      		'id', 'title', 'album_cover_item_id', 'description', 'height', 'width', 'left_ptr', 'right_ptr', 
      		'level', 'mime_type', 'name', 'owner_id', 'parent_id', 'relative_path_cache', 'relative_url_cache', 
      		'resize_dirty', 'slug', 'sort_column', 'sort_order', 'thumb_dirty','thumb_height', 'view_1', 'type',
      		'resize_height', 'resize_width', 'thumb_height', 'thumb_width', 'slug', 'name', 'relative_path_cache'
      	))->from('items'));
      	*/
	}
	
	static function queryLimitByLimiter(&$query, $limit)
	{
      	foreach ($limit as $key => $block)
      	{
      		if (gettype($block['value']) == 'array') { $query->and_where($key, 'IN', $block['value']); }
      		else { $query->and_where($key, $block['op'], $block['value']); }
      	}
	}
	
	static function getDisplayOptions($request)
	{
		if (isset($request->params->display)) {
			return(split(',', $request->params->display));
		} else {
			return(array('uiimage','uitext','ownership','members'));
		};
	}
	
	static function queryOrder(&$query, $request)
	{
		if (isset($request->params->order)) {
			$order = $request->params->order;
			$direction = 'asc';
			if (isset($request->params->direction))
			{
				if ($request->params->direction == 'desc') { $direction = 'desc'; }
			}
			
			switch ($order)
			{
				case 'tree':
					$query->order_by(array('level' => 'ASC', 'left_ptr' => 'ASC'));
					break;
				case 'created':
					$query->order_by(array('created' => $direction));
					break;
				case 'updated':
					$query->order_by(array('updated' => $direction));
					break;
				case 'views':
					$query->order_by(array('view_count' => $direction));
					break;
				case 'type':
					$query->order_by(array('type' => $direction));
					break;
			}
		}
	}
	
	static function addChildren($request, $db, $filler, $permitted, $display, &$return, $rest_base)
	{
		$children = $db->select('parent_id', 'id')->from('items')->where('parent_id', 'IN', $filler['children_of']);
		if (isset($request->params->childtypes)) 
		{
			$types = split(',', $request->params->childtypes);
			$children->where('type', 'IN', $types);
		}
					
		/* We shouldn't have any albums we don't have access to by default in this query, but just in case.. */
		unrest_rest::queryLimitByPermission(&$children, $permitted);
		
		
		$childBlock = array();
		foreach($children->execute() as $item)
		{
			$childBlock[$item->parent_id][] = intval($item->id);
		}
		
		foreach ($return as &$data)
		{
			if (array_key_exists($data['entity']['id'], $childBlock))
			{
				if (in_array('terse', $display)) {
					$data['members'] = $childBlock[ $data['id'] ];
				}
				else {
					$members = array();
					foreach ($childBlock[ $data['entity']['id'] ] as $child) {
						$members[] = unrest_rest::makeRestURL('item', $child, $rest_base);
					}
					$data['members'] = $members;
				}
			}
			else
			{
					$data['members'] = array();			
			}
		}	
	}
	
	private static function makeRestURL($resource, $identifier, $base)
	{
		return $base . '/' . $resource . '/' . $identifier;
	}
	
	public static function size_url($size, $relative_path_cache, $type, $file_base) {
		$base = $file_base . 'var/' . $size . '/' . $relative_path_cache;
		if ($type == 'photo') {
			return $base;
		} else if ($type == 'album') {
			return $base . "/.album.jpg";
		} else if ($type == 'movie') {
			// Replace the extension with jpg
			return preg_replace("/...$/", "jpg", $base);
		}
	}
	
	
	static function get($request) {
		$db = db::build();
		
		$start = microtime(true);
		$rest_base = url::abs_site("rest");
		$file_base = url::abs_file(''); #'var/' . $size . '/'
		/* Build basic limiters */
		$limit = unrest_rest::getBasicLimiters($request);
		$limit = unrest_rest::getFreetextLimiters($request,$limit);
		
		/* Build numeric limiters */
		/* ...at some point. */
		
		/* Figure out an array of albums we got permissions to access */
		$permitted = unrest_rest::albumsICanAccess();
		
		$display = unrest_rest::getDisplayOptions($request);		
		$items = unrest_rest::baseItemQuery($db);
		
      	/*
      		Introduce some WHERE statements that'll make sure that we don't get to see stuff we
      		shouldn't be seeing.
      	*/
      	unrest_rest::queryLimitByPermission(&$items, $permitted);
      	unrest_rest::queryLimitByLimiter(&$items, $limit);
      	unrest_rest::queryOrder(&$items, $request);
		
		$return = array();
		$filler = array();
		$relationshipCandidates = array();
		
		foreach($items->execute() as $item)
		{
			$data = array(
				'id' => intval($item->id), 
				'parent' => intval($item->parent_id),
				'owner_id' => intval($item->{'owner_id'}),
				'public' => ($item->view_1)?true:false,
				'type' => $item->type // Grmbl
			);
			
			if (in_array('uitext', $display)) {
				$ui = array(
					'title' => $item->title,
					'description' => $item->description,
					'name' => $item->name,
					'slug' => $item->slug
				);
				
				$data = array_merge($data, $ui);
			}
			
			
			if (in_array('uiimage', $display)) {
				$ui = array(
					'height' => $item->height,
					'width' => $item->width,
					'resize_height' => $item->resize_height,
					'resize_width' => $item->resize_width,
					'thumb_height' => $item->resize_height,
					'thumb_width' => $item->resize_width
				);
				
				$ui['thumb_url_public'] = unrest_rest::size_url('thumbs', $item->relative_path_cache, $item->type, $file_base);
				$public = $item->view_1?true:false;
				$fullPublic = $item->view_full_1?true:false;
				
				if ($item->type != 'album')
				{
					$ui['file_url'] = unrest_rest::makeRestURL('data', $item->id . '?size=full', $rest_base);
					$ui['thumb_url'] = unrest_rest::makeRestURL('data', $item->id . '?size=thumb', $rest_base);
					$ui['resize_url'] = unrest_rest::makeRestURL('data', $item->id . '?size=resize', $rest_base);
					
					if ($public) {
						$ui['resize_url_public'] = unrest_rest::size_url('resizes', $item->relative_path_cache, $item->type, $file_base);
						
						if ($fullPublic) {
							$ui['file_url_public'] = unrest_rest::size_url('albums', $item->relative_path_cache, $item->type, $file_base);
						}
					}
				}
				
				$data = array_merge($data, $ui);
			}
			
			if (in_array('members', $display)) {
				$filler['children_of'][] = $item->id;
			}
			
			$return[] = array(
				'url' => unrest_rest::makeRestURL('item', $item->id, $rest_base ),
				'entity' => $data
			);
		}
		
		
		/* Do we need to fetch children? */
		if (array_key_exists('children_of', $filler))
		{
			unrest_rest::addChildren($request, $db, $filler, $permitted, $display, &$return, $rest_base);
		}
		
		$end = microtime(true);
		error_log("Inner " . ($end - $start) . " seconds taken");
		
		return $return;
	}
}


?>