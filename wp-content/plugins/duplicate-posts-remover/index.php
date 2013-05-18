<?
	/*
	 Plugin Name: Duplicate Posts Remover
	 Plugin URI: http://www.optimum7.com/?utm_source=DuplicatePostPlugin
	 Description: This plugin eliminates the selected-duplicated posts and pages from your blog.
	 Version: 3.2.0
	 Author: Optimum7
	 Author URI: http://www.optimum7.com/?utm_source=DuplicatePostPlugin
	 Copyright 2011  Optimum7 Inc (email : optimum7@optimum7.com)
	 This program is free software; you can redistribute it and/or modify
	 it under the terms of the GNU General Public License, version 2, as 
	 published by the Free Software Foundation.
	 This program is distributed in the hope that it will be useful,
	 but WITHOUT ANY WARRANTY; without even the implied warranty of
	 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 GNU General Public License for more details.	
	 You should have received a copy of the GNU General Public License
	 along with this program; if not, write to the Free Software
	 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/
	 define('OPT7_PDD_PLUGINPATH', (DIRECTORY_SEPARATOR != '/') ? str_replace(DIRECTORY_SEPARATOR, '/', dirname(__FILE__)) : dirname(__FILE__));
	 define('OPT7_PDD_PLUGINNAME', 'Duplicate Posts Remover');
	 define('OPT7_PDD_UTM_SOURCE_CODE', 'DuplicatePostPlugin');
	 define('OPT7_PDD_PLUGINSUPPORT_PATH', 'http://www.optimum7.com/internet-marketing/content/duplicate-content-googles-penalty-and-a-wordpress-solution.html?utm_source='.OPT7_PDD_UTM_SOURCE_CODE);
	 define('OPT7_PDD_RSS_LINKS',10);
	 define('OPT7_PDD_RSS_URL','http://www.optimum7.com/internet-marketing/feed');
	 _opt7_duplicate_posts_remover::bootstrap(); 
	 /********************************************************************************************************************/	
	 class _opt7_duplicate_posts_remover{ 
		 function bootstrap(){ 
			$file = OPT7_PDD_PLUGINPATH . '/' . basename(__FILE__);	
			add_action('admin_menu', array('_opt7_duplicate_posts_remover','add_admin_options'));
			add_action( 'admin_enqueue_scripts', array('_opt7_duplicate_posts_remover','my_admin_enqueue_scripts'));
			add_action('wp_ajax_counters_action',array('_opt7_duplicate_posts_remover','setCounters_action_callback'));
			add_action('wp_ajax_my_action',array('_opt7_duplicate_posts_remover','getData_action_callback'));
			add_action('wp_ajax_filters_action',array('_opt7_duplicate_posts_remover','getFilters_action_callback'));
			add_action('wp_ajax_trashfilters_action',array('_opt7_duplicate_posts_remover','getTrashFilters_action_callback'));
			add_action('wp_ajax_deletePost_action',array('_opt7_duplicate_posts_remover','deletePost_action_callback'));
			add_action('wp_ajax_restorePost_action',array('_opt7_duplicate_posts_remover','restorePost_action_callback'));
		 }
		 function add_admin_options(){	
		  	  add_menu_page ('_opt7_duplicate_posts_remover', 'Duplicate Posts', 8, '_opt7_duplicate_posts_remover', array('_opt7_duplicate_posts_remover','_opt7_duplicate_posts_remover_menu'), get_bloginfo('siteurl').'/wp-content/plugins/duplicate-posts-remover/images/icon.gif');
		 }
		 function _opt7_duplicate_posts_remover_menu(){
			  require(OPT7_PDD_PLUGINPATH . '/admin/opt7-duplicate-posts-remover-settings.php');
		 }
		 function my_admin_enqueue_scripts() {
			if ($_GET['page']=="_opt7_duplicate_posts_remover")
			wp_enqueue_script('pluginscript', plugins_url('/js/duplicate-post-remover.js', __FILE__), array('jquery'));
		 }
		 
		 //Ajax Calls
		 function setCounters_action_callback() {
			$limit = 10000;
			global $wpdb;
			$args = array('numberposts' => $limit,'post_type' => 'post','post_status' => 'publish');
			$all_posts = sizeof(get_posts($args));
			$args = array('numberposts' => $limit,'post_type' => 'post','post_status' => 'trash');
			$posts_trash = sizeof(get_posts($args));
			$args = array('numberposts' => $limit,'post_type' => 'page','post_status' => 'publish');
			$all_pages = sizeof(get_posts($args));
			$args = array('numberposts' => $limit,'post_type' => 'page','post_status' => 'trash');
			$pages_trash = sizeof(get_posts($args));
			
			$query = "SELECT wp_posts.* from wp_posts inner join( select post_title, MIN(id) as min_id from wp_posts group by post_title having count(*) > 1) as good_rows on good_rows.post_title = wp_posts.post_title and good_rows.min_id <> wp_posts.id where 1=1 and wp_posts.post_type ='post' and wp_posts.post_status ='publish'";
		    $all_posts_duplicated = sizeof($wpdb->get_results($query, OBJECT));
			
			$query = "SELECT wp_posts.* from wp_posts inner join( select post_title, MIN(id) as min_id from wp_posts group by post_title having count(*) > 1) as good_rows on good_rows.post_title = wp_posts.post_title and good_rows.min_id <> wp_posts.id where 1=1 and wp_posts.post_type ='page' and wp_posts.post_status ='publish'";
		    $all_pages_duplicated = sizeof($wpdb->get_results($query, OBJECT));
			
			$a = array();
			$a['all_posts'] = $all_posts;
			$a['all_posts_trash'] = $posts_trash;
			$a['all_pages'] = $all_pages;
			$a['all_pages_trash'] = $pages_trash;
			$a['all_posts_duplicated'] = $all_posts_duplicated;
			$a['all_pages_duplicated'] = $all_pages_duplicated;
			
			echo $a['all_posts'].','.$a['all_posts_trash'].','.$a['all_pages'].','.$a['all_pages_trash'].','.$a['all_posts_duplicated'].','.$a['all_pages_duplicated'].',';			
		 }			 
		 function getFilters_action_callback() {
			 $html ='<select name="opt7-action-select" id="opt7-action-select">
			  		   <option value="-1" selected="selected">Actions</option>
					   <option value="trash">Move to Trash</option>
					</select>
					<input type="submit" id="opt7-apply-trash" class="button-primary" value="Apply"/>';
			echo $html;
		 }	
		 function getTrashFilters_action_callback() {
			  $html ='<select name="opt7-action-select" id="opt7-action-select">
			  		   <option value="-1" selected="selected">Actions</option>
					   <option value="restore">Restore</option>
					</select>
					<input type="submit" id="opt7-apply-restore" class="button-primary" value="Apply"/>';
			echo $html;
		 }			 
		 function deletePost_action_callback() {
			 $postID = $_POST['postID'];
			 wp_trash_post($postID);
		 }	
		 function restorePost_action_callback() {
			 $postID = $_POST['postID'];
			 $my_post = array();
			 $my_post['ID'] = $postID;
			 $my_post['post_status'] = 'publish';			
			 wp_update_post( $my_post );
		 }		 		 	 
		 function getData_action_callback() {
			require_once(OPT7_PDD_PLUGINPATH . '/classes/opt7-duplicate-posts-remover-pagination.class.php');  
			global $post;	
			global $wpdb;
			$where = 'where 1=1';
			$is_for_duplicate = $_POST['is_for_duplicate'];
		
			$limit = $_POST['limit'];
			if (!$limit)
			$limit=25;		
			
			$curr_page = $_POST['page'];
			if (!$curr_page)
			$curr_page=1;	
			
			$post_search = $_POST['post_search'];
			if ($post_search)
			$where .=" and $wpdb->posts.post_title like '%".$post_search."%'";			
			
			$post_type = $_POST['post_type'];			
			if (!$post_type)
			$post_type='post';
			if ($post_type)
			$where .=" and $wpdb->posts.post_type ='".$post_type."'";				
			
			$post_status = $_POST['post_status'];			
			if (!$post_status)
			$post_status='publish';			
			if ($post_status)
			$where .=" and $wpdb->posts.post_status ='".$post_status."'";	
			
			if ($is_for_duplicate){
				$query = "SELECT $wpdb->posts.* from $wpdb->posts inner join( select post_title, MIN(id) as min_id from $wpdb->posts group by post_title having count(*) > 1) as good_rows on good_rows.post_title = $wpdb->posts.post_title and good_rows.min_id <> $wpdb->posts.id ".$where;
			}
			else{
				$query ="SELECT $wpdb->posts.* FROM $wpdb->posts ".$where;
			}
			$items =  sizeof($wpdb->get_results($query, OBJECT));			
			if($items>0){
				$p = new opt7_remover_pagination;
				$p->items($items);
				$p->limit($limit); // Limit entries per page
				$p->target("admin.php?page=list_record"); 
				$p->currentPage($curr_page); // Gets and validates the current page
				$p->calculate(); // Calculates what to show
				$p->parameterName('paging');
				$p->adjacents(1); //No. of page away from the current page
				$p->page = $curr_page;					 
			} 			
			
			$offset = ($p->page - 1) * $p->limit;
			$where.=" ORDER BY post_modified DESC LIMIT ".$offset.','.$limit;
			
			if ($is_for_duplicate){
				$query = "SELECT $wpdb->posts.* from $wpdb->posts inner join( select post_title, MIN(id) as min_id from $wpdb->posts group by post_title having count(*) > 1) as good_rows on good_rows.post_title = $wpdb->posts.post_title and good_rows.min_id <> $wpdb->posts.id ".$where;
			}
			else{
				$query ="SELECT $wpdb->posts.* FROM $wpdb->posts ".$where;
			}
			
			$items =  $wpdb->get_results($query, OBJECT);
			$html = '<div class="tablenav">
				<div class="Opt7-actions" style="float:left"></div>
				<div><input type="hidden" id="opt7-rows-total" value="'.sizeof($items).'"/>
                <div class="tablenav-pages" style="width:400px;float:right;text-align: right;">';
					if (sizeof($items)>0)
                    $html .= $p->show();
                $html .='</div>
            	</div>';
			
			$html .='<table id="opt7-data-table" class="widefat">
                  <thead>
				  	  <tr>
					  	  <th colspan="2"><input type="text" style="width:400px;line-height:25px;" placeholder="search by title" id="opt7-posts-search" value="'.$post_search.'"/></th>
						  <th colspan="6">
						  	<select style="width:60px;line-height:25px;float:right" id="opt7-per-page">
								<option value="5"'; if ($limit==5) $html .="selected='selected'"; $html.='>5</option>
								<option value="25"'; if ($limit==25) $html .="selected='selected'"; $html.='>25</option>
								<option value="50"'; if ($limit==50) $html .="selected='selected'"; $html.='>50</option>
								<option value="100"'; if ($limit==100) $html .="selected='selected'"; $html.='>100</option>
							</select>
							<label for="opt7-per-page" style="float:right;margin-right:10px;margin-top:5px;">Rows per page:</label>
						  </th>
					  </tr>
					  <tr>
					  	  <th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox" id="opt7-remover-cb"></th>
                          <th style="width:auto;">Title</th>
						  <th style="width:120px;">Author</th>       
                          <th style="width:80px;">Created</th>
                          <th style="width:80px;">Modified</th>
						  <th style="width:60px;">Status</th>
						  <th style="width:60px;">Type</th>
						  <th style="width:20px;"><img alt="Comments" src="'.get_bloginfo('siteurl').'/wp-admin/images/comment-grey-bubble.png"></th>
						  
                      </tr>
                  </thead>
                  <tbody>';
				     if (sizeof($items)>0){
						 foreach($items as $post):
							 $user_info  = get_userdata($post->post_author);
							 $permalink = get_permalink( $post->post_ID );
							 $html .= "<tr id='".$post->ID."'>
									<td><input type='checkbox'/></td>
									<td style='width:auto;'><a href='".$permalink."' target='_blank'>".$post->post_title."</a></td>
									<td>".$user_info->user_firstname." " .$user_info->user_lastname."</td>
									<td>".date('m/d/Y', strtotime($post->post_date))."</td>
									<td>".date('m/d/Y', strtotime($post->post_modified))."</td>
									<td><code>".$post->post_status."<code></td>
									<td>".$post->post_type."</td>
									<td>".$post->comment_count."</td>
									</tr>";
						 endforeach;
					 }
                 $html .="</tbody></table>";
			echo $html;
			die();
		}		 
   	} 
?>