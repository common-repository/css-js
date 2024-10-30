<?php
/*
Plugin Name: CSS-JS
Plugin URI: http://wordpress.org/extend/plugins/css-js/
Description: Add custom styles for posts and pages
Author: Vladimir Tsvang
Version: 1.1
Author URI: http://tsvang.net.ua
*/

add_action('admin_head', 'add_post_enctype'); 
add_action('wp_head', 'hook_head');
add_action('add_meta_boxes', 'css_js_boxes');
add_action('save_post', 'process_css_js');

function add_styles() {
	global $post;
    if (is_single()) {
    		$links = @unserialize(get_post_meta($post->ID, 'css_js_style', true));
    		if(isset($links[$post->ID]))
    		{
    			foreach ($links[$post->ID] as $current) {
    				echo "\t<link rel='stylesheet' type='text/css' href='".$current."' media='screen' />\n";
    			}
    		}
    } 
}

function add_page_js() {
	global $post;
    if (is_single()) {
    		$links = @unserialize(get_post_meta($post->ID, 'css_js_script', true));
    		if(isset($links[$post->ID]))
    		{
    			foreach ($links[$post->ID] as $current) {
    				echo "\t<script type='text/javascript' src='".$current."'></script>\n";
    			}
    		}
    } 
}

function hook_head() {
	echo "<!--css-js plugin start-->\n";
	add_styles();
	echo "\n\n";
	add_page_js();
	echo "<!--css-js plugin end-->\n\n";
}

function already_css() {
	global $post;
    		$links = unserialize(get_post_meta($post->ID, 'css_js_style', true));
    		if(isset($links[$post->ID]))
    		{
    			foreach ($links[$post->ID] as $current) {
    				echo "<tr align='left'><td><a target='_blank' href='".$current."'>".$current."</a><td/><td><input type='checkbox' name='cj[css][delete][]' value='".$current."'/></td></tr>";
    			}
    		}
}

function already_js() {
	global $post;
    		$links = unserialize(get_post_meta($post->ID, 'css_js_script', true));
    		if(isset($links[$post->ID]))
    		{
    			foreach ($links[$post->ID] as $current) {
    				echo "<tr align='left'><td><a target='_blank' href='".$current."'>".$current."</a><td/><td><input type='checkbox' name='cj[js][delete][]' value='".$current."'/></td></tr>";
    			}
    		} 
}

function ShowCss() {
	$handle = @opendir(ABSPATH.'wp-content/plugins/css-js/styles');
	if ($handle) {
	{
		do {
			$current = readdir($handle);
			if (!is_dir(ABSPATH.'wp-content/plugins/css-js/styles/'.$current)) {
					echo "<option value=\"".$current."\">".$current."</option>";
				}			
			}
		while ($current);
	}
	}
	closedir($handle);
}

function ShowJs() {
	$handle = @opendir(ABSPATH.'wp-content/plugins/css-js/scripts');
	if ($handle) {
	{
		do {
			$current = readdir($handle);
			if (!is_dir(ABSPATH.'wp-content/plugins/css-js/scripts/'.$current)) {
					echo "<option value=\"".$current."\">".$current."</option>";
				}			
			}
		while ($current);
	}
	}
	closedir($handle);
}

function css_js_boxes() {
	if( function_exists( 'add_meta_box' ) ) {
		if(current_user_can('edit_posts')) {
    		add_meta_box( 'css_js_box', __( 'Custom CSS/JS', 'css-js' ), 
                'draw_css_js_box', 'post', 'normal' );
		}
		if(current_user_can('edit_posts')) {
    		add_meta_box( 'css_js_box', __( 'Custom CSS/JS', 'css-js' ), 
                'draw_css_js_box', 'page', 'normal' );
		}
    		
	}
}

function draw_css_js_box() { 
	global $post;
	?>
<table width="100%">
	<tr valign="top">
		<td width="50%" valign="top">
			<?php wp_nonce_field( plugin_basename(__FILE__), 'css-js' );?>
			<b><font color="red">Add CSS stylesheet</font></b> <input name="cj[action_group]" value="add_page_css" type="radio"  checked="checked" />
			<table>
				<tr align="left">
				<td width="5px"><input name="cj[css][action_type]" value="url" type="radio"  checked="checked" /></td><th>URL:</th><td><input name="cj[css][url]" type="text" /></td>
				</tr>
				
				<tr align="left">
				<td width="5px"><input name="cj[css][action_type]" value="upl" type="radio" /></td><th>Upload:</th><td><input name="cj[css][file]" type="file" /></td>
				</tr>
				
				<tr align="left">
				<td width="5px"><input name="cj[css][action_type]" value="col" type="radio" /></td><th>Coll.:</th><td><select name="cj[css][coll]" ><?php ShowCss();?></select></td>
				</tr>
			</table>
			<b><font color="red"><br /><br />Remove CSS stylesheet</font></b> <input name="cj[action_group]" value="remove_css" type="radio" />
			<table>
				<tr align="center"><th>Source</th><th>Del</th></tr>		
				<?php already_css();?>
			</table>
		</td>
		
		<td width="50%" valign="top">
			<b><font color="red">Add JavaScript</font></b> <input name="cj[action_group]" value="add_page_js" type="radio" />
			<table>
				<tr align="left">
				<td width="5px"><input name="cj[js][action_type]" value="url" type="radio"  checked="checked" /></td><th>URL:</th><td><input name="cj[js][url]" type="text" /></td>
				</tr>
				
				<tr align="left">
				<td width="5px"><input name="cj[js][action_type]" value="upl" type="radio" /></td><th>Upload:</th><td><input name="cj[js][file]" type="file" /></td>
				</tr>
				
				<tr align="left">
				<td width="5px"><input name="cj[js][action_type]" value="col" type="radio" /></td><th>Coll.:</th><td><select name="cj[js][coll]" ><?php ShowJs();?></select></td>
				</tr>
			</table>
			<b><font color="red"><br /><br />Remove JavaScript</font></b> <input name="cj[action_group]" value="remove_js" type="radio" />
				<table>
					<tr align="center"><th>Source</th><th>Del</th></tr>					
					<?php already_js();?>
				</table>
		</td>
	</tr>
	<tr align="right">
		<td></td>
		<td><br /><br /><input type="submit" value="Apply" /></td>
	</tr>
</table>
	<script type="text/javascript">
	    jQuery('form#post').attr('enctype','multipart/form-data');
	    jQuery('form#post').attr('encoding','multipart/form-data');
	</script>
<?php }

function ProcessCSS($userdata) {
	global $post;
		if ($userdata[action_type]=='url' && !empty($userdata['url'])) {
			$links = unserialize(get_post_meta($post->ID, 'css_js_style', true));
			$links[$post->ID][] = $userdata['url'];
			$links[$post->ID] = array_unique($links[$post->ID]);
			$data = serialize($links);
			add_post_meta($post->ID, 'css_js_style', $data, true) or update_post_meta($post->ID, 'css_js_style', $data);
		}
		
		if ($userdata[action_type]=='col' && !empty($userdata['coll'])) {
			$links = unserialize(get_post_meta($post->ID, 'css_js_style', true));
			$links[$post->ID][] = get_bloginfo('wpurl').'/wp-content/plugins/css-js/styles/'.$userdata['coll'];
			$links[$post->ID] = array_unique($links[$post->ID]);
			$data = serialize($links);
			add_post_meta($post->ID, 'css_js_style', $data, true) or update_post_meta($post->ID, 'css_js_style', $data);
		}
		
		if ($userdata[action_type]=='upl' && !empty($_FILES)) {
			if ($_FILES['cj']['type']['css']['file'] == 'text/css') {
				move_uploaded_file($_FILES['cj']['tmp_name']['css']['file'], ABSPATH.'wp-content/plugins/css-js/styles/'.$_FILES['cj']['name']['css']['file']);
				$links = unserialize(get_post_meta($post->ID, 'css_js_style', true));
				$links[$post->ID][] = get_bloginfo('wpurl').'/wp-content/plugins/css-js/styles/'.$_FILES['cj']['name']['css']['file'];
				$links[$post->ID] = array_unique($links[$post->ID]);
				$data = serialize($links);
				add_post_meta($post->ID, 'css_js_style', $data, true) or update_post_meta($post->ID, 'css_js_style', $data);
			}	
		}		
}
	
function ProcessJS($userdata) {
	global $post;
		if ($userdata[action_type]=='url' && !empty($userdata['url'])) {
				$links = unserialize(get_post_meta($post->ID, 'css_js_script', true));
				$links[$post->ID][] = $userdata['url'];
				$links[$post->ID] = array_unique($links[$post->ID]);
				$data = serialize($links);
				add_post_meta($post->ID, 'css_js_script', $data, true) or update_post_meta($post->ID, 'css_js_script', $data);
		}
		if ($userdata[action_type]=='col' && !empty($userdata['coll'])) {
				$links = unserialize(get_post_meta($post->ID, 'css_js_script', true));
				$links[$post->ID][] = get_bloginfo('wpurl').'/wp-content/plugins/css-js/scripts/'.$userdata['coll'];
				$links[$post->ID] = array_unique($links[$post->ID]);
				$data = serialize($links);
				add_post_meta($post->ID, 'css_js_script', $data, true) or update_post_meta($post->ID, 'css_js_script', $data);
		}
		if ($userdata[action_type]=='upl' && !empty($_FILES)) {
			if ($_FILES['cj']['type']['js']['file'] == 'application/x-javascript') {
				move_uploaded_file($_FILES['cj']['tmp_name']['js']['file'], ABSPATH.'wp-content/plugins/css-js/scripts/'.$_FILES['cj']['name']['js']['file']);
				$links = unserialize(get_post_meta($post->ID, 'css_js_script', true));
				$links[$post->ID][] = get_bloginfo('wpurl').'/wp-content/plugins/css-js/scripts/'.$_FILES['cj']['name']['js']['file'];
				$links[$post->ID] = array_unique($links[$post->ID]);
				$data = serialize($links);
				add_post_meta($post->ID, 'css_js_script', $data, true) or update_post_meta($post->ID, 'css_js_script', $data);
			}	
		}	
}

function RemoveCSS($userdata) {
	global $post;
	if (($userdata['0']=='')) {
		return null;
	}
    	$links = unserialize(get_post_meta($post->ID, 'css_js_style', true));
    	   foreach ($userdata as $current) {
		   		$search = array_search($current, $links[$post->ID]);
		   			if ($search === false) {
		   				continue;
		   			}
		   			else 
		   			{
		   				unset($links[$post->ID][$search]);
		   			}
    		}
    	$data = serialize($links);
    	add_post_meta($post->ID, 'css_js_style', $data, true) or update_post_meta($post->ID, 'css_js_style', $data); 	
}

function RemoveJS($userdata) {
	global $post;
	if (($userdata['0']=='')) {
		return null;
	}
    	$links = unserialize(get_post_meta($post->ID, 'css_js_script', true));
    	   foreach ($userdata as $current) {
		   		$search = array_search($current, $links[$post->ID]);
    	   		   	if ($search === false) {
		   				continue;
		   			}
		   			else 
		   			{
		   				unset($links[$post->ID][$search]);
		   			}
    		}
    	$data = serialize($links);
    	add_post_meta($post->ID, 'css_js_script', $data, true) or update_post_meta($post->ID, 'css_js_script', $data); 	
}

function process_css_js($post_id) {
	if (!wp_verify_nonce( $_POST['css-js'], plugin_basename(__FILE__))) {
	return $post_id;
	}
	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
	return $post_id;
	}
	
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
		return $post_id;
		}
	} 
	
	else {
		if ( !current_user_can('edit_post', $post_id))
		return $post_id;
	}
	
	switch ($_POST['cj']['action_group']) {
		case 'add_page_css':
			ProcessCSS($_POST['cj']['css']);
			break;
  	
			case 'remove_css':
				RemoveCSS($_POST['cj']['css']['delete']);
				break;
  	
				case 'add_page_js':
					ProcessJS($_POST['cj']['js']);
					break;
					
					case 'remove_js':
						RemoveJS($_POST['cj']['js']['delete']);
						break;
						
						default: break;
  }
  return null;
}

function add_post_enctype() { ?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('#post').attr('enctype','multipart/form-data');
			jQuery('#post').attr('encoding', 'multipart/form-data');                            
		});
	</script>               
<?php } ?>