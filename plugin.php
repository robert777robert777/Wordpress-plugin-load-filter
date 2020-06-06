<?php
/*
Plugin Name: Selective plugins loading
Author: Robert Kruglyak
*/
defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );



function add_my_stylesheet() 
{
    wp_enqueue_style( 'myCSS', plugins_url( '/backend.css', __FILE__ ),0,6 );
}

add_action('admin_print_styles', 'add_my_stylesheet');


function demo_settings_page()
{
    //add_settings_section("section", "Section", null, "demo");
    //add_settings_section("section", "", null, "demo");
    //add_settings_field("demo-file", "", "demo_file_display", "demo", "section");  
    register_setting("section", "selective_plugin_loading", "handle_file_upload");
}

function handle_file_upload($option)
{
//code to copy/make sure it's there the mu-plugin may go here	
/*	
  //file_put_contents('test3.txt', print_r($_POST,true));
  if (!empty($_POST["selective_plugin_loading"]))
  {
	  file_put_contents(plugin_dir_path(__FILE__) .  "1.json",stripslashes($_POST["selective_plugin_loading"]));
  }
//	file_put_contents(plugin_dir_path(__FILE__) . 'new.txt',print_r($_POST,true));
*/
  return $option;
}


add_action("admin_init", "demo_settings_page");

function demo_page()
{
		include(plugin_dir_path(__FILE__) . 'backend.editor.html.php');
}

function menu_item()
{
  add_submenu_page("plugins.php", 'Selective plugins loading', "Selective plugins loading", "manage_options", "amiram", "demo_page"); 
}
 
add_action("admin_menu", "menu_item");

function myplugin_activate() {
	file_put_contents(__DIR__ . '/log.txt', "activated\n",FILE_APPEND );	
//	echo 'YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY';

    // Activation code here...
	//copy mu plugin and handle issues with it


	$rule_file = WPMU_PLUGIN_DIR . '/' . 'selective-plugin-loading.php';
	
	$err_arr = array();
	// Check directory permissions and write the WPMU_PLUGIN_DIR directory if not exists,
	if ( !file_exists( WPMU_PLUGIN_DIR ) ) {
		if ( is_writable( WP_CONTENT_DIR ) ) {
			mkdir(WPMU_PLUGIN_DIR, 0755);
		} else {
			$error_in1 = substr(WP_CONTENT_DIR, strlen( ABSPATH ));
			$error_in2 = substr(WPMU_PLUGIN_DIR, strlen( ABSPATH ));
			$write_error = '';
			$write_error .= '<div class="update-nag"><p> Your <code>'. $error_in1 .'</code> directory isn&#8217;t <a href="http://codex.wordpress.org/Changing_File_Permissions">writable</a>.<br>';
			$write_error .= 'These are the rules you should write in an PHP-File, create the '. $error_in2 .' directory and place it in the <code>'. $error_in2 .'</code> directory. ';
			$write_error .= 'Click in the field and press <kbd>CTRL + a</kbd> to select all. </p>';
			$write_error .= '<textarea  readonly="readonly" name="rules_txt" rows="7" style="width:100%; padding:11px 15px;">' . $rules . '</textarea></div>';
			$write_error .= '<br><br>';

			$err_arr[] = $write_error;
			$err_arr[] = $rules;
			//return $err_arr;
		}
	}

	// Check directory permissions and write the plugin-logic-rules.php file
	if ( file_exists( WPMU_PLUGIN_DIR ) ) {
		if ( is_writable( WPMU_PLUGIN_DIR ) && copy(__DIR__ . '/mu-plugins/selective-plugin-loading.php',$rule_file)) {
			//file_put_contents( $rule_file, $rules );
			//nothing to do here
		} else {
			$error_in = substr(WPMU_PLUGIN_DIR, strlen( ABSPATH ));
			$write_error = '';
			$write_error .= '<div class="update-nag"><p> Your <code>'. $error_in .'</code> directory isn&#8217;t <a href="http://codex.wordpress.org/Changing_File_Permissions">writable</a>.<br>';
			$write_error .= 'These are the rules you should write in an PHP-File and place it in the <code>'. $error_in .'</code> directory. ';
			$write_error .= 'Click in the field and press <kbd>CTRL + a</kbd> to select all. </p>';
			$write_error .= '<textarea  readonly="readonly" name="rules_txt" rows="7" style="width:100%; padding:11px 15px;">' . $rules . '</textarea></div>';
			$write_error .= '<br><br>';

			$err_arr[] = $write_error;
			$err_arr[] = $rules;
			//return $err_arr;
		}
	}
//if there was an error alert the user somehow 
}
register_activation_hook( __FILE__, 'myplugin_activate' );



function sample_admin_notice__success() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( 'Done!', 'sample-text-domain' ); ?></p>
    </div>
    <?php
}

function myplugin_deactivate() {
//	file_put_contents(__DIR__ . '/log.txt', "deactivated\n",FILE_APPEND );	
	$rule_file = WPMU_PLUGIN_DIR . '/' . 'selective-plugin-loading.php';
//			file_put_contents(__DIR__ . '/log.txt', "rule file: $rule_file\n",FILE_APPEND );	
	if ( file_exists( $rule_file ) )  {
		file_put_contents(__DIR__ . '/log.txt', "file exists $rule_file\n",FILE_APPEND );	
		if ( !unlink( $rule_file ) )  {
			wp_die( wp_sprintf(
						'Error cannot delete the old rule file: <code>' . substr($rule_file, strlen( ABSPATH )) .'</code>'.
						' The directory isn&#8217;t writable.'
					)
			);
		}
	} 
}
register_deactivation_hook( __FILE__, 'myplugin_deactivate' );
