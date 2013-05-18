<?php
/*
Plugin Name: WP Policies
Plugin URI: http://offlinemarketingtools.com/plugins/wp-policies-plugin/
Description: Creates preset policy pages on your blog. <a href="options-general.php?page=static.php">Configure Options Here.</a>
Version: 1.0
Author: Offline Marketing Tools
Author URI: http://offlinemarketingtools.com
*/

/*
1. Upload entire wp-policies folder to /wp-content/plugins/
2. Set permission of folder /wp-policies/wp-static/ to 777.
3. Activate the plugin from your Wordpress PLUGINS page.
4. Go to SETTINGS > WP POLICIES:
   * Fill in the CONTACT DETAILS section at the bottom of the settings page. Put your Company Name in ALL CAPS.
   * Click IMPORT at the bottom of the page to import the default policies.
   * Read over each default policy as some may not apply to your business. Edit where necessary.
   * Delete or deactivate the pages you do not want to display by setting them to DRAFT in WP Page Manager.

5. Go to the WP Page Manager and make sure that the comments are turned off for your pages.
6. Add <?php static_footer_pages(); ?> to your footer.php file of your theme.

**See the readme.txt file for more detailed usage instructions.

License:

Copyright (c) 2010 www.OfflineMarketingTools.com. All rights reserved.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
 any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

 
$static_ver = '1.0';

$sp_default_sitename = get_bloginfo( 'name' );
$sp_default_email = get_bloginfo( 'admin_email' );
$sp_default_company = '';
$sp_default_address = '';
$sp_default_phone = '';

add_option( 'static_sitename', $sp_default_sitename );
add_option( 'static_company', $sp_default_company );
add_option( 'static_address', $sp_default_address );
add_option( 'static_phone', $sp_default_phone );
add_option( 'static_email', $sp_default_email );

function static_options_setup() {
    if( function_exists( 'add_options_page' ) ){
        add_options_page( 'WP Policies', 'WP Policies', 8, 
                          basename(__FILE__), 'static_options_page');
    }
	
	@mkdir('../wp-content/plugins/wp-policies/wp-static/');
	@chmod('../wp-content/plugins/wp-policies/wp-static/', 0777);
}

function static_options_page(){
    global $static_ver;
	global $sp_default_sitename;
	global $sp_default_email;
	global $sp_default_company;
	global $sp_default_address;
	global $sp_default_phone;
	
	
	if( isset( $_GET[ 'remove' ] ) && file_exists( '../wp-content/plugins/wp-policies/wp-static/' . base64_decode($_GET['remove']) ) ){
		$filename = base64_decode($_GET['remove']);
		
		@unlink('../wp-content/plugins/wp-policies/wp-static/' . $filename);
		
		$data_index = unserialize(base64_decode(@join('', file('../wp-content/plugins/wp-policies/wp-static/data_index.php'))));
		
		wp_delete_post($data_index[$filename][0]);
		
		foreach ($data_index as $k => $v) {
			if ($k != $filename) {
				$new_data_index[$k] = $v;
			}
		}
		$data_index = $new_data_index;
		$handle = @fopen('../wp-content/plugins/wp-policies/wp-static/data_index.php', 'w');
		@fwrite($handle, base64_encode(serialize($data_index)));
		@fclose($handle);

		data_index_reorder();
		
		echo '<script>window.location.href = \'options-general.php?page=static.php\';</script>';
	}
	
    if( isset( $_POST[ 'set_defaults' ] ) ){
    } else if( isset( $_POST[ 'save_page' ] ) ){
		echo '<div id="message" class="updated fade"><p><strong>';
	
		$title = stripslashes( (string) $_POST[ 'editpage_title' ] );
		$filename = base64_decode($_GET['edit']);
		$content = stripslashes( (string) $_POST[ 'editpage_content' ] );
		
		$handle = @fopen('../wp-content/plugins/wp-policies/wp-static/' . $filename, 'w');
		@fwrite($handle, $content);
		@fclose($handle);
		
		data_index_reorder($filename, $title);
		
		echo 'Page content updated.';	
		echo '</strong></p></div>';
    } 
	else if( isset( $_POST[ 'import_files' ] ) ){
	echo '<div id="message" class="updated fade"><p><strong>';
	$imported_files=scandir('../wp-content/plugins/wp-policies/wp-static/source/');
	
	foreach ($imported_files as $imported_file)
	{
	if ($imported_file<>".." && $imported_file<>".")
	{
	$title=explode(".",$imported_file);
	$title=$title[0];
	$filename=$imported_file;
		$data_index = unserialize(base64_decode(@join('', file('../wp-content/plugins/wp-policies/wp-static/data_index.php'))));
	if (!is_array($data_index[$title.".dat"]))
	{
	

/*	
	if (is_array($data_index))
		{
		wp_delete_post($data_index[$filename][0], true);
		
		foreach ($data_index as $k => $v) {
			if ($k != $filename) {
				$new_data_index[$k] = $v;
			}
		}
		$data_index = $new_data_index;
		$handle = @fopen('../wp-content/plugins/wp-policies/wp-static/data_index.php', 'w');
		@fwrite($handle, base64_encode(serialize($data_index)));
		@fclose($handle);

		data_index_reorder();
		}

	*/
		
	$order=0;
	$content=file_get_contents('../wp-content/plugins/wp-policies/wp-static/source/'.$filename);
	
	$handle = @fopen('../wp-content/plugins/wp-policies/wp-static/' . $title.'.dat', 'w');
		@fwrite($handle, $content);
		@fclose($handle);
	
	$post_title = ucwords(str_replace("_"," ",$title));
		$post_content = '<!-- ' . $title.".dat" . ' -->';
		/*
		if ($_POST["attrition_link"]=="yes")
		{
		$post_content .= "<br /><p><b><small>Policy generated by the WP Privacy Plugin.</small></b></p>";
		}
		*/
		
		$post_status = 'publish';
		$post_author = 1;
		$post_name = $title;
		$post_type = 'page';
		
		$post_data = compact( 'post_title', 'post_content', 'post_status',
						  'post_author', 'post_name', 'post_type' );
	
	
		$postID = wp_insert_post( $post_data );
		data_index_reorder($title.".dat", $title, $postID);
	}
	}
	}
	echo "Files successfully imported!";
	echo '</strong></p></div>';
	}
	else if( isset( $_POST[ 'create_page' ] ) ){
		echo '<div id="message" class="updated fade"><p><strong>';
	
		$title = stripslashes( (string) $_POST[ 'newpage_title' ] );
		$filename = stripslashes( (string) $_POST[ 'newpage_filename' ] );
		$order = stripslashes( (string) $_POST[ 'newpage_order' ] );
		$content = stripslashes( (string) $_POST[ 'newpage_content' ] );
		
		if (file_exists('../wp-content/plugins/wp-policies/wp-static/' . $filename)) {
			$filename .= 'wp';
		}
		
		$filename .= '.dat';
		
		$post_title = $title;
		$post_content = '<!-- ' . $filename . ' -->';
		$post_status = 'publish';
		$post_author = 1;
		$post_name = preg_replace("/[^a-zA-Z0-9]/", "-", $title);
		$post_type = 'page';
			
		$handle = @fopen('../wp-content/plugins/wp-policies/wp-static/' . $filename, 'w');
		@fwrite($handle, $content);
		@fclose($handle);
		
		$post_data = compact( 'post_title', 'post_content', 'post_status',
						  'post_author', 'post_name', 'post_type' );
	
		$postID = wp_insert_post( $post_data );
	
		if( !$postID ){
			echo 'Static Pages page could not be created.';
		} else {
			data_index_reorder($filename, $title, $postID);
			echo 'Static Page (ID ' . $postID . ') was created';
		}
		
		echo '</strong></p></div>';
	} else if( isset( $_POST[ 'info_update' ] ) ){
	
			echo '<div id="message" class="updated fade"><p><strong>';
	
		update_option( 'static_sitename', stripslashes( (string) $_POST['static_sitename' ] ));
		update_option( 'static_company', stripslashes( (string) $_POST['static_company' ] ));
		update_option( 'static_address', stripslashes( (string) $_POST['static_address' ] ));
		update_option( 'static_phone', stripslashes( (string) $_POST['static_phone' ] ));
		update_option( 'static_email', stripslashes( (string) $_POST['static_email' ] ));
	
		echo 'Configuration updated.';
		echo '</strong></p></div>';
    }

    ?>

    <div class="wrap">
    <h2>WP Policies <?php echo $static_ver; ?></h2>
    This plugin creates new (hidden) pages under Pages Manger of Wordpress each time you create a new policy page. 

    You can edit or modify the content of those pages in <a href="edit-pages.php">Pages Manager</a> just like normal content pages. <br><br>You can also insert the content of a Policy Page into the body of any Wordpress post by putting this piece of code &lt;!-- filename.dat --&gt; into the page content area while in HTML VIEW.<br><br>

	To display all policy links in the footer of your blog automatically, place this code anywhere in your theme's footer.php file:<br> 
	<b style="color: red">&lt;?php</b> <b>static_footer_pages();</b> <b style="color: red">?&gt;</b><br><br>
		
    <strong>NOTE:</strong> All text files are created and saved in <b>wp-static</b> folder. DO NOT MODIFY OR REMOVE FILE data_index.php!<br>
	
<br>

    
    <?
	if (!empty($_GET['edit']) && file_exists('../wp-content/plugins/wp-policies/wp-static/' . base64_decode($_GET['edit']))) {
		$data_index = unserialize(base64_decode(@join('', file('../wp-content/plugins/wp-policies/wp-static/data_index.php'))));
	?>
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        <table cellpadding="6" cellspacing="3" width="100%">
        	<tr>
            	<td colspan="2"><h2>Edit Policy Page:</h2> <?=$data_index[base64_decode($_GET['edit'])][1]?></td>
            </tr>
            <tr>
            	<td align="right" valign="middle">Page Content File:<br></td>
            	<td style="font-weight: bold; color: #FF6600">&lt;!-- <?=base64_decode($_GET['edit'])?> --&gt;</td>
            </tr>
            <tr>
            	<td></td>
                <td style="font-style: italic; font-size: 11px">Insert contents of <?=base64_decode($_GET['edit'])?> file into a post by <br>
					adding the above code into the post using HTML VIEW.
                </td>
            </tr>
            <tr><td><br></td></tr>
        	<tr>
            	<td align="right" valign="middle" width="270"><strong>Page Title:</strong></td>
                <td><input name="editpage_title" type="text" size="40" value="<?=$data_index[base64_decode($_GET['edit'])][1]?>" /></td>
            </tr>
            <!--<tr>
            	<td>Sort Order</td>
                <td><input id="newpage_order" name="newpage_order" type="text" size="2" value="" maxlength="2" onBlur="genFN2()" /></td>
            </tr>-->
            <tr>
            	<td align="right" valign="top"><strong>HTML Content:</strong></td>
                <td><textarea name="editpage_content" style="width: 400px; height:300px"><?=@join('', file('../wp-content/plugins/wp-policies/wp-static/' . base64_decode($_GET['edit'])))?></textarea></td>
            </tr>
            <tr>
            	<td></td>
                <td> <div class="submit" style="padding: 0"><input type="submit" name="save_page" value="Save Page &raquo;" /> </div></td>
            </tr>
        </table>
	</form>
<br>
<? } ?>
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="border-bottom: solid 1px #CCCCCC; padding: 5px"><h2>Current Policy Pages</h2></td>
<!--    <td style="border-bottom: solid 1px #CCCCCC"><b>Order</b></td>-->
    <td style="border-bottom: solid 1px #CCCCCC" width="130"><b>Manage</b></td>
    <td style="border-bottom: solid 1px #CCCCCC" width="100"><b>Edit</b></td>
    <td style="border-bottom: solid 1px #CCCCCC" width="100"><b>Delete</b></td>
</tr>
	<?
	$data_index = unserialize(base64_decode(@join('', file('../wp-content/plugins/wp-policies/wp-static/data_index.php'))));
	if (is_array($data_index) && !empty($data_index)) {
		foreach ($data_index as $k => $v) {
			if (file_exists('../wp-content/plugins/wp-policies/wp-static/' . $k) && substr($k, strlen($k) - 4, 4) == '.dat') {
?>
<tr>
	<td style="border-bottom: solid 1px #CCCCCC; padding: 5px"><?=$v[1]?></td>
<!--    <td style="border-bottom: solid 1px #CCCCCC"><input type="text" value="<?=$v[0]?>" size="2"></td>-->
    <td style="border-bottom: solid 1px #CCCCCC"><a href="page.php?action=edit&post=<?=$v[0]?>">Manage</a></td>
    <td style="border-bottom: solid 1px #CCCCCC"><a href="options-general.php?page=static.php&edit=<?=base64_encode($k)?>">Edit</a></td>
    <td style="border-bottom: solid 1px #CCCCCC"><a href="options-general.php?page=static.php&remove=<?=base64_encode($k)?>">Delete</a></td>
</tr>
<?
				//echo $v[1] . '<br>';
			}
		}
	}
	?>
</table>
    <br>
    <script>
    function genFN() {
		np_title = document.getElementById('newpage_title').value;
		np_title = np_title.replace(/\'/gi, '-');
		np_title = np_title.replace(/\~/gi, '_');
		np_title = np_title.replace(/\!/gi, '_');
		np_title = np_title.replace(/\@/gi, '_');
		np_title = np_title.replace(/\$/gi, '_');
		np_title = np_title.replace(/\%/gi, '_');
		np_title = np_title.replace(/\^/gi, '_');
		np_title = np_title.replace(/\&/gi, '_');
		np_title = np_title.replace(/\*/gi, '_');
		np_title = np_title.replace(/\(/gi, '_');
		np_title = np_title.replace(/\)/gi, '_');
		np_title = np_title.replace(/\[/gi, '_');
		np_title = np_title.replace(/\]/gi, '_');
		np_title = np_title.replace(/ /gi, '_');
		np_title = np_title.replace(/\,/gi, '_');
		np_title = np_title.replace(/\./gi, '_');
		np_title = np_title.replace(/\</gi, '_');
		np_title = np_title.replace(/\>/gi, '_');
		np_title = np_title.replace(/\?/gi, '_');
		np_title = np_title.replace(/\:/gi, '_');
		np_title = np_title.replace(/\;/gi, '_');
		np_title = np_title.replace(/\"/gi, '-');
		np_title = np_title.replace(/\`/gi, '_');
		np_title = np_title.replace(/\{/gi, '_');
		np_title = np_title.replace(/\}/gi, '_');
		np_title = np_title.replace(/\+/gi, '_');
		np_title = np_title.replace(/\=/gi, '_');
		np_title = np_title.replace(/\//gi, '_');
		np_title = np_title.replace(/\\/gi, '_');
		np_title = np_title.replace(/\|/gi, '_');
		
		document.getElementById('newpage_filename').value = np_title.toLowerCase().substr(0, 30);
	}
    function genFN1() {
		np_title = document.getElementById('newpage_filename').value;
		np_title = np_title.replace(/\'/gi, '-');
		np_title = np_title.replace(/\~/gi, '_');
		np_title = np_title.replace(/\!/gi, '_');
		np_title = np_title.replace(/\@/gi, '_');
		np_title = np_title.replace(/\$/gi, '_');
		np_title = np_title.replace(/\%/gi, '_');
		np_title = np_title.replace(/\^/gi, '_');
		np_title = np_title.replace(/\&/gi, '_');
		np_title = np_title.replace(/\*/gi, '_');
		np_title = np_title.replace(/\(/gi, '_');
		np_title = np_title.replace(/\)/gi, '_');
		np_title = np_title.replace(/\[/gi, '_');
		np_title = np_title.replace(/\]/gi, '_');
		np_title = np_title.replace(/ /gi, '_');
		np_title = np_title.replace(/\,/gi, '_');
		np_title = np_title.replace(/\./gi, '_');
		np_title = np_title.replace(/\</gi, '_');
		np_title = np_title.replace(/\>/gi, '_');
		np_title = np_title.replace(/\?/gi, '_');
		np_title = np_title.replace(/\:/gi, '_');
		np_title = np_title.replace(/\;/gi, '_');
		np_title = np_title.replace(/\"/gi, '-');
		np_title = np_title.replace(/\`/gi, '_');
		np_title = np_title.replace(/\{/gi, '_');
		np_title = np_title.replace(/\}/gi, '_');
		np_title = np_title.replace(/\+/gi, '_');
		np_title = np_title.replace(/\=/gi, '_');
		np_title = np_title.replace(/\//gi, '_');
		np_title = np_title.replace(/\\/gi, '_');
		np_title = np_title.replace(/\|/gi, '_');
		
		document.getElementById('newpage_filename').value = np_title.toLowerCase().substr(0, 30);
	}
	function genFN2() {
		intVal = parseInt(document.getElementById('newpage_order').value);
		if (!isNaN(intVal)) {
			document.getElementById('newpage_order').value = intVal;
		} else {
			document.getElementById('newpage_order').value = 0;
		}
	}
    </script>
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        <table cellpadding="6" cellspacing="3" width="100%">
        	<tr>
            	<td colspan="2"><h2>Create New Page</h2></td>
            </tr>
        	<tr>
            	<td align="right" valign="middle" width="270"><strong>Page Title:</strong></td>
                <td><input id="newpage_title" name="newpage_title" type="text" size="40" value="" onKeyPress="genFN()" onKeyDown="genFN()" onKeyUp="genFN()" onChange="genFN()" /></td>
            </tr>
            <tr>
            	<td align="right" valign="middle" width="270"><strong>Filename:</strong></td>
                <td><input id="newpage_filename" name="newpage_filename" type="text" size="40" value="" onKeyPress="genFN1()" onKeyDown="genFN1()" onKeyUp="genFN1()" onChange="genFN1()" />.dat (Cannot be changed)</td>
            </tr>
            <!--<tr>
            	<td>Sort Order</td>
                <td><input id="newpage_order" name="newpage_order" type="text" size="2" value="" maxlength="2" onBlur="genFN2()" /></td>
            </tr>-->
            <tr>
            	<td align="right" valign="top"><strong>HTML Content:</strong></td>
                <td><textarea name="newpage_content" style="width: 400px; height:300px"></textarea></td>
            </tr>
            <tr>
            	<td></td>
                <td> <div class="submit" style="padding: 0"><input type="submit" name="create_page" value="Save New Page &raquo;" /> </div></td>
            </tr>
        </table>
	</form>

<br>

        <table cellpadding="6" cellspacing="3" width="100%">
            <tr>
                <td style="border-top: solid 1px #CCCCCC" colspan="10"><br></td>
            </tr>
        </table>
    
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <input type="hidden" name="info_update" id="info_update" value="true" />

    <fieldset class="options">
    <legend><h2>Contact Details</h2></legend>

    <table width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top">
      <td align="right" width="270" valign="middle"><strong>Site Name:</strong>&nbsp;</td>
      <td align="left" valign="middle">
         <input name="static_sitename" type="text" size="40" value="<?php echo htmlspecialchars( get_option( 'static_sitename' ) ); ?>" /> {sitename}
      </td>
    </tr>
    <tr valign="top">
      <td align="right" valign="middle"><strong>Company Name:</strong>&nbsp;</td>
      <td align="left" valign="middle">
         <input name="static_company" type="text" size="40" value="<?php echo htmlspecialchars( get_option( 'static_company' ) ); ?>" /> {company}
      </td>
    </tr>
    <tr valign="top">
      <td align="right" valign="middle"><strong>Address:</strong>&nbsp;</td>
      <td align="left" valign="middle">
         <input name="static_address" type="text" size="40" value="<?php echo htmlspecialchars( get_option( 'static_address' ) ); ?>" /> {address}
      </td>
    </tr>
    <tr valign="top">
      <td align="right" valign="middle"><strong>Phone:</strong>&nbsp;</td>
      <td align="left" valign="middle">
         <input name="static_phone" type="text" size="40" value="<?php echo htmlspecialchars( get_option( 'static_phone' ) ); ?>" /> {phone}
      </td>
    </tr>
    <tr valign="top">
      <td align="right" valign="middle"><strong>Email:</strong>&nbsp;</td>
      <td align="left" valign="middle">
         <input name="static_email" type="text" size="40" value="<?php echo htmlspecialchars( get_option( 'static_email' ) ); ?>" /> {email}
      </td>
    </tr>

    <tr>
    	<td></td>
        <td><div class="submit" style="padding: 0"><input type="submit" name="info_update" value="<?php _e('Update options' ); ?> &raquo;" /></div></td>
    </tr>
    </table>
    </fieldset>
    </form>
	
	<br />
	
	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<table cellpadding="6" cellspacing="3" width="100%">
<tr>
<td colspan="2">
<h2>Import Default Policies</h2>
<p>Default policies files should be .txt files and located in the /wp-policies/wp-static/source/ folder.</p>
</td>
</tr>
<!--
<tr>
<td width="270">&nbsp;</td>
<td>
<input type="checkbox" name="attrition_link" value="yes" /> Add Attrition Link
</td>
</tr>
-->
<tr>
<td width="270">&nbsp;</td>
<td>
<div class="submit" style="padding: 0"><input type="submit" name="import_files" value="Import files &raquo;" /> </div>
</td>
</tr>
</table>
</form>
    
    </div><?php
}

function static_process($content) {
	$data_index = unserialize(base64_decode(@join('', file('wp-content/plugins/wp-policies/wp-static/data_index.php'))));
	if (is_array($data_index)) {
		foreach ($data_index as $k => $v) {
		    $tag = "<!-- $k -->";
		    if( strpos( $content, $tag ) == false ) { #Do nothing;
			} else {
			return str_replace( $tag, static_html($k), $content );
			}
		}
	}
	
	return $content;
}

function static_html($tag){
	$content = @join('', file('wp-content/plugins/wp-policies/wp-static/' . $tag));
	
	$content = str_replace(
		array(
			'{sitename}',
			'{company}',
			'{address}',
			'{phone}',
			'{email}'
		),
		array(
			htmlspecialchars( get_option( 'static_sitename' )),
			htmlspecialchars( get_option( 'static_company' )),
			htmlspecialchars( get_option( 'static_address' )),
			htmlspecialchars( get_option( 'static_phone' )),
			htmlspecialchars( get_option( 'static_email' )),
		),
		$content
	);
	
	return $content;
}

$footer_exclude = '';
$old_pages;
function static_exclude_pages($pages) {
	global $footer_exclude;
	$bail_out = ( ( defined( 'WP_ADMIN' ) && WP_ADMIN == true ) || ( strpos( $_SERVER[ 'PHP_SELF' ], 'wp-admin' ) !== false ) );
	$bail_out = apply_filters( 'ep_admin_bail_out', $bail_out );
	if ( $bail_out ) return $pages;
	
	$data_index = unserialize(base64_decode(@join('', file('wp-content/plugins/wp-policies/wp-static/data_index.php'))));
	if (is_array($data_index) && !empty($data_index)) {
		foreach ($data_index as $k=>$v) {
			$excluded_ids[] = $v[0];
		}	

		$length = count($pages);
		for ( $i=0; $i<$length; $i++ ) {
			$page = & $pages[$i];
			if ( in_array( $page->ID, $excluded_ids ) ) {
				unset( $pages[$i] );
			}
		}
		
		if ( ! is_array( $pages ) ) $pages = (array) $pages;
		$pages = array_values( $pages );
	}

	return $pages;
}

function static_footer_pages() {
	$data_index = unserialize(base64_decode(@join('', file('wp-content/plugins/wp-policies/wp-static/data_index.php'))));
	if (is_array($data_index) && !empty($data_index)) {
		$ret = '<ul role="navigation">';
		foreach ($data_index as $k=>$v) {
			$post = &get_post($v[0]);
			
			if (strstr($post->post_content, '<!--') && $post->post_status == 'publish') {
				$title = $post->post_title;
				$ret .= '<a href="'.get_bloginfo("url").'/?page_id=' . $v[0] . '">' . $title . '</a> | ';
			
			}
		}
		$ret = substr($ret, 0, strlen($ret) - 3);
	}	
	echo $ret;
}

function get_pages_exclude() {
	global $footer_exclude;
	return substr($footer_exclude, 0, strlen($footer_exclude) - 1);
}

add_filter('get_pages','static_exclude_pages');
add_filter('the_content', 'static_process');
add_action('admin_menu', 'static_options_setup');

function data_index_reorder($filename = '', $title = '', $order = '') {
	$data_index = unserialize(base64_decode(@join('', file('../wp-content/plugins/wp-policies/wp-static/data_index.php'))));
	if (!empty($filename)) {
		if (!empty($order)) {
			$data_index[$filename] = array(intval($order), $title);
		} else {
			$data_index[$filename][1] = $title;
		}
	}
	
    $handle = @fopen('../wp-content/plugins/wp-policies/wp-static/data_index.php', 'w');
    @fwrite($handle, base64_encode(serialize($data_index)));
    @fclose($handle);
}

?>