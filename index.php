<?php
/*
Plugin Name: Smart App Banners
Plugin URI: http://www.clevelandwebdeveloper.com/wordpress-plugins/smartappbanners
Description: Automatically implement Safari's new Smart App Banner feature on your Wordpress site. The banner provides a standardized method of promoting apps on the App Store from any website. The banner will automatically appear on all pages. In order for this to work you have to enter your app id in <strong>Settings > Smart App Banners > Your App ID</strong>
Version: 1.1
Author: Justin Saad
Author URI: http://www.clevelandwebdeveloper.com
License: GPL2
*/


$plugin_label = "Smart App Banners";
$plugin_slug = "smart_app_banners";
	
class smart_app_banners {
	
    public function __construct(){
    	
		global $plugin_label, $plugin_slug;
		$this->plugin_slug = $plugin_slug;
		$this->plugin_label = $plugin_label;
		
		//plugin row links
		add_filter( 'plugin_row_meta', array($this,'plugin_row_links'), 10, 2 );
		
        if(is_admin()){
		    add_action('admin_menu', array($this, 'add_plugin_page'));
		    add_action('admin_init', array($this, 'page_init'));
		}
		
    }

    public function add_plugin_page(){
        // This page will be under "Settings"
		add_options_page('Settings Admin', $this->plugin_label, 'manage_options', $this->plugin_slug.'-setting-admin', array($this, 'create_admin_page'));
    }

	
    public function print_section_info(){ //section summary info goes here
		print 'Enter your <b>nine-digit</b> app ID below. To find your app ID from the <a href="http://itunes.apple.com/linkmaker/" target="_blank">iTunes Link Maker</a>, type the name of your app in the Search field, and select the appropriate country and media type. In the results, find your app and select iPhone App Link in the column on the right. Your app ID is the nine-digit number in between id and ?mt.';
    }
	
    public function create_admin_page(){
        ?>
		<div class="wrap">
		    <?php screen_icon(); ?>
		    <h2><?php echo $this->plugin_label ?></h2>			
		    <form method="post" action="options.php">
		        <?php
	            // This prints out all hidden setting fields
			    settings_fields($this->plugin_slug.'_option_group');	
			    do_settings_sections($this->plugin_slug.'-setting-admin');
			?>
		        <?php submit_button(); ?>
		    </form>
		</div>
	<?php
    }
    
    public function page_init(){
        	
		//create settings section
        add_settings_section(
		    $this->plugin_slug.'_setting_section',
		    'Configuration',
		    array($this, 'print_section_info'),
		    $this->plugin_slug.'-setting-admin'
		);	
		
		//add text input field
		$field_slug = "your_app_id";
		$field_label = "Your App ID";
		$field_id = $this->plugin_slug.'_'.$field_slug;
		register_setting($this->plugin_slug.'_option_group', $field_id);
		add_settings_field(
		    $field_id,
		    $field_label, 
		    array($this, 'create_a_text_input'), //callback function for text input
		    $this->plugin_slug.'-setting-admin',
		    $this->plugin_slug.'_setting_section',
		    array(								// The array of arguments to pass to the callback.
				"id" => $field_id, //sends field id to callback
				//"desc" => 'Enter your full name above', //description of the field (optional)
				//"default" => 'John Doe', //sets the default field value (optional), when grabbing this option value later on remember to use get_option(option_name, default_value) so it will return default value if no value exists yet
				"placeholder" => 'eg: 123456789' //sets the field placeholder which appears when the field is empty (optional)
			)			
		);
		
		//add checkbox field
		$field_slug = "on_all_pages";
		$field_label = "Show banner on all pages?";
		$field_id = $this->plugin_slug.'_'.$field_slug;
		register_setting($this->plugin_slug.'_option_group', $field_id);
		add_settings_field(
		    $field_id,
		    $field_label, 
		    array($this, 'create_a_checkbox'), //callback function for checkbox
		    $this->plugin_slug.'-setting-admin',
		    $this->plugin_slug.'_setting_section',
		    array(								// The array of arguments to pass to the callback.
				"id" => $field_id, //sends field id to callback
				"desc" => 'Uncheck this box to only show smart app banners on specific posts and pages', //description of the field (optional)
				"default" => '1' //sets the default field value (optional), when grabbing this option value later on remember to use get_option(option_name, default_value) so it will return default value if no value exists yet
				
			)			
		);

	
    } //end of page_init function



	/**
	 * Add admin notices logic
	 */
	
	public function admin_notices() {
		global $current_user;
		$userid = $current_user->ID;
		global $pagenow;
		
		// This notice will only be shown if no data entered for required input
		//check input field based on field slug
		$field_slug = "your_app_id";
		if( (!(get_option($this->plugin_slug.'_'.$field_slug) ) AND (get_option($this->plugin_slug.'_on_all_pages', 1, false) == 1))  ) {
			echo '
				<div class="updated">
					<p><strong>'.$this->plugin_label.' is almost ready.</strong> You must <a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/options-general.php?page='.$this->plugin_slug.'-setting-admin">enter your App ID</a> for it to work.</p>
				</div>';
		}
		
	
	}
	
	//add plugin action links logic
	function add_plugin_action_links( $links ) {
	 
		return array_merge(
			array(
				'settings' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/options-general.php?page='.$this->plugin_slug.'-setting-admin">Settings</a>'
			),
			$links
		);
	 
	}
	
	//function for adding content to head
	function custom_wp_head(){
		
		//check for field value
		global $post;
		$metabox_slug = $this->plugin_slug. "_meta_box";
		$meta = get_post_meta($post->ID,'_'.$metabox_slug,TRUE);
		$field_id = "your_app_id";
		
		
		$page_specific_app_id = $meta[$field_id];
		if (($page_specific_app_id) and ((is_single()) or (is_page()))) { 
			$use_app_id = $page_specific_app_id;
		} else { //no page specific app id
			if (get_option($this->plugin_slug.'_on_all_pages', 1, false) == 1) {
				$use_app_id = get_option($this->plugin_slug.'_'.$field_id);
			}			
		}

		//Close PHP tags ?>
        <?php if ($use_app_id) : ?>
			<meta name="apple-itunes-app" content="app-id=<?php echo $use_app_id;?>">
        <?php endif ?>
		<?php //Open PHP tags
	}
	

	/**
	 * This following set of functions handle all input field creation for plugin page
	 * 
	 */
	function create_a_checkbox($args) {
		$html = '<input type="checkbox" id="'  . $args[id] . '" name="'  . $args[id] . '" value="1" ' . checked(1, get_option($args[id], $args["default"]), false) . '/>'; 
		
		// Here, we will take the desc argument of the array and add it to a label next to the checkbox
		$html .= '<label for="'  . $args[id] . '">&nbsp;&nbsp;'  . $args[desc] . '</label>'; 
		
		echo $html;
		
	} // end create_a_checkbox
	
	function create_a_text_input($args) {
		//grab placeholder if there is one
		if($args[placeholder]) {
			$placeholder_html = "placeholder=\"".$args[placeholder]."\"";
		}		
		// Render the output
		echo '<input type="text" '  . $placeholder_html . ' id="'  . $args[id] . '" name="'  . $args[id] . '" value="' . get_option($args[id], $args["default"]) . '" />';
		if($args[desc]) {
			echo "<p class='description'>".$args[desc]."</p>";
		}
		
	} // end create_a_text_input
	
	function create_a_textarea_input($args) {
		//grab placeholder if there is one
		if($args[placeholder]) {
			$placeholder_html = "placeholder=\"".$args[placeholder]."\"";
		}	
		// Render the output
		echo '<textarea '  . $placeholder_html . ' id="'  . $args[id] . '"  name="'  . $args[id] . '" rows="5" cols="50">' . get_option($args[id], $args["default"]) . '</textarea>';
		if($args[desc]) {
			echo "<p class='description'>".$args[desc]."</p>";
		}		
	}
	
	function create_a_radio_input($args) {
	
		$radio_options = $args[radio_options];
		$html = "";
		if($args[desc]) {
			$html .= $args[desc] . "<br>";
		}
		foreach($radio_options as $radio_option) {
			$html .= '<input type="radio" id="'  . $args[id] . '_' . $radio_option[value] . '" name="'  . $args[id] . '" value="'.$radio_option[value].'" ' . checked($radio_option[value], get_option($args[id], $args["default"]), false) . '/>';
			$html .= '<label for="'  . $args[id] . '_' . $radio_option[value] . '"> '.$radio_option[label].'</label><br>';
		}
		
		echo $html;
	
	} // end create_a_radio_input callback

	function create_a_select_input($args) {
	
		$select_options = $args[select_options];
		$html = "";
		if($args[desc]) {
			$html .= $args[desc] . "<br>";
		}
		$html .= '<select id="'  . $args[id] . '" name="'  . $args[id] . '">';
			foreach($select_options as $select_option) {
				$html .= '<option value="'.$select_option[value].'" ' . selected( $select_option[value], get_option($args[id], $args["default"]), false) . '>'.$select_option[label].'</option>';
			}
		$html .= '</select>';
		
		echo $html;
	
	} // end create_a_select_input callback
	
	/**
	 * End input creation functions
	 * 
	 */
	
	
	//shortcode action
	function app_store_download($atts) { //parameters stored in atts variable
	
		STATIC $i = 1;
		// default parameters
		extract( shortcode_atts( array(
			'size' => 100,
			'verticalalign' => 'top',
			'id' => get_option("smart_app_banners_your_app_id"),
		), $atts ) ); //returns all parameters as variables. 
		
		$multiply = $size / 100;
		
		$width = 260; //natural width
		$width = $width * $multiply;
		
		$html = "";
		$html .= "<style>";
		$html .= "img.motech_app_store_shortimage".$i." {width: ".$width."px; vertical-align: ".$verticalalign.";}";
		$html .= "</style>";
		if ( $id != '') {
			$html .= '<a  href="http://itunes.apple.com/app/id'.$id.'"><img style="box-shadow: none;" class="motech_app_store_shortimage'.$i.'" src="'.plugins_url( 'images/download_on_the_app_store.png' , __FILE__ ).'"></a>';
		}
		
		return $html;
		$i++;
	}	
	
	//shortcode action
	function android_download($atts) { //parameters stored in atts variable
	
		STATIC $a = 1;
		// default parameters
		extract( shortcode_atts( array(
			'size' => 100,
			'verticalalign' => 'top',
			'id' => '',
		), $atts ) ); //returns all parameters as variables. 
		
		$multiply = $size / 100;
		
		$width = 260; //natural width
		$width = $width * $multiply;
			
		$html = "";
		$html .= "<style>";
		$html .= "img.motech_android_store_shortimage".$a." {width: ".$width."px; vertical-align: ".$verticalalign.";}";
		$html .= "</style>";
		if ( $id != '') {
			$html .= '<a  href="http://play.google.com/store/apps/details?id='.$id.'"><img style="box-shadow: none;" class="motech_android_store_shortimage'.$a.'" src="'.plugins_url( 'images/download_for_android.png' , __FILE__ ).'"></a>';
		}
		
		return $html;
		$a++;
	}	

	//plugin row links
	public function plugin_row_links($links, $file) {
		$plugin = plugin_basename(__FILE__); 
		if ($file == $plugin) // only for this plugin
				return array_merge( $links,
			array( '<a target="_blank" href="http://www.linkedin.com/in/ClevelandWebDeveloper/">' . __('Find me on LinkedIn' ) . '</a>' ),
			array( '<a target="_blank" href="http://twitter.com/ClevelandWebDev">' . __('Follow me on Twitter') . '</a>' )
		);
		return $links;
	}
	
} //end of plugin class

$plugin_instance = new $plugin_slug();	

//add admin notices
add_action( 'admin_notices', array($plugin_instance, 'admin_notices') );

//add Settings link to plugin page
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($plugin_instance, 'add_plugin_action_links') );

//adding content to head
add_action('wp_head', array($plugin_instance, 'custom_wp_head'));

//add shortcodes
add_shortcode('app-store-download', array($plugin_instance, 'app_store_download'));
add_shortcode('android-download', array($plugin_instance, 'android_download'));




//begin meta box
$metabox_label = "Smart App Banners";
$metabox_slug = "smart_app_banners";



//to access a given post meta field, use this structure:

/*
$field_slug = "your_app_id";
$meta = get_post_meta($post->ID,'_'.$metabox_slug.'_meta_box',TRUE);
$value = $meta[$field_slug];*/


$metabox_slug .= "_meta_box";

class smart_app_banners_meta_box {
	function motech_meta_init()
	{
		
		global $metabox_label, $metabox_slug;
		$this->metabox_slug = $metabox_slug;
		$this->metabox_label = $metabox_label;
		
	 
		// add a meta box for each of the wordpress page types: posts and pages
		foreach (array('post','page') as $type)
		{
			add_meta_box($this->metabox_slug.'_all_meta', $this->metabox_label, array($this, 'motech_meta_setup'), $type, 'normal', 'high');
		}
	 
		// add a callback function to save any data a user enters in
		add_action('save_post',array($this, 'motech_meta_save'));
	}
	 
	function motech_meta_setup()
	{
		global $post;
	 
		// using an underscore, prevents the meta variable
		// from showing up in the custom fields section
		$meta = get_post_meta($post->ID,'_'.$this->metabox_slug,TRUE);
		
		//lets write html here
		?>
		<div class="motech_meta_control">
		 
			<?php /*?><p>meta box description goes here.</p><?php */?>
            
            <?php
			
			//add text input field
			$field_slug = "your_app_id";
			$field_label = "Your App ID";
		    $args = array(								// The array of arguments to pass
				"field_type" => "text",
				"field_slug" => $field_slug,
				"field_id" => '_'.$this->metabox_slug."[". $field_slug . "]",
				"label" => $field_label,
				"desc" => 'Enter your app id', //description of the field (optional)
				"placeholder" => 'eg: 123456789', //description of the field (optional)
				"meta" => $meta,
				//"default" => '12342' //sets the default field value (optional), when grabbing this option value later on remember to check use this value if none set
				
			);
			$this->create_field_html($args);
			?>
			
		 
		</div>
        
        <?php
	 
		// create a custom nonce for submit verification later
		 echo '<input type="hidden" name="_noncename_'.$this->metabox_slug.'" value="' . wp_create_nonce(__FILE__) . '" />';
	}
	 
	function motech_meta_save($post_id)
	{
		// authentication checks
	 
		// make sure data came from our meta box
		if (!wp_verify_nonce($_POST['_noncename_'.$this->metabox_slug],__FILE__)) return $post_id;
	 
		// check user permissions
		if ($_POST['post_type'] == 'page')
		{
			if (!current_user_can('edit_page', $post_id)) return $post_id;
		}
		else
		{
			if (!current_user_can('edit_post', $post_id)) return $post_id;
		}
	 
		// authentication passed, save data
	 
		// var types
		// single: _motech_meta[var]
		// array: _motech_meta[var][]
		// grouped array: _motech_meta[var_group][0][var_1], _motech_meta[var_group][0][var_2]
	 
		$current_data = get_post_meta($post_id, '_'.$this->metabox_slug, TRUE);  
	 
		$new_data = $_POST['_'.$this->metabox_slug];
	 
		//$this->motech_meta_clean($new_data);
	 
		if ($current_data)
		{
			if (is_null($new_data)) delete_post_meta($post_id,'_'.$this->metabox_slug);
			else update_post_meta($post_id,'_'.$this->metabox_slug,$new_data);
		}
		elseif (!is_null($new_data))
		{
			add_post_meta($post_id,'_'.$this->metabox_slug,$new_data,TRUE);
		}
	 
		return $post_id;
	}
	 
/*	function motech_meta_clean(&$arr)
	{
		if (is_array($arr))
		{
			foreach ($arr as $i => $v)
			{
				if (is_array($arr[$i]))
				{
					$this->motech_meta_clean($arr[$i]);
	 
					if (!count($arr[$i]))
					{
						unset($arr[$i]);
					}
				}
				else
				{
					if (trim($arr[$i]) == '')
					{
						unset($arr[$i]);
					}
				}
			}
	 
			if (!count($arr))
			{
				$arr = NULL;
			}
		}
	}*/
	
	//the following function handles input field creation
	function create_field_html($args) {
		//grab placeholder if there is one
		if($args[placeholder]) {
			$placeholder_html = "placeholder=\"".$args[placeholder]."\"";
		}			
		
		// prepare variables
		$meta = $args[meta];
		$field_slug = $args[field_slug];
		
		if( ($args['default']) AND (!(isset($meta[$field_slug])))  ) { //set initial default values where applicable. remember to call this on the front end if default values are to be used
			$value = $args['default'];
		} else {
			$value = $meta[$field_slug];	
		}
		
		//render output
		if($args[field_type] == "text") {
			echo '<table><tbody><tr><td>'.$args[label].'</td><td><input type="text" style="vertical-align:middle;margin-right: 5px;" '  . $placeholder_html . ' id="'  . $args[field_id] . '" name="'  . $args[field_id] . '" value="' . $value . '" />';
			if($args[desc]) {
				echo "<span class='description' style='max-width: 60%; display:inline-block;'>".$args[desc]."</span>";
			}
			echo '</td></tr></tbody></table>';
		}
	} // end create_field_html
	
	
} //end class


$metabox = new $metabox_slug();	

//Add Custom Meta Box To Post And Pages
add_action('admin_init', array($metabox, 'motech_meta_init'));


//begin widget
/**
 * Adds Custom widget.
 */

class Smart_App_Banners_Widget extends WP_Widget {
	
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		
		parent::__construct(
	 		$this->slug = "smart_app_banners_widget", // Base ID
			$this->label = "Motech Download on the App Store", // Name
			array( 'description' => __( 'Displays a Download on the App Store badge linking to your app', 'text_domain' ), ) // Args
		);
		
		//DECLARE WIDGET INPUT FIELDS
		
		
		//add text input
/*		$fields[] = array(
					 	"field_title" => "Override App ID:",
						"field_slug" => "override_app_id",
						"field_type" => "text",
					 );*/

		//add text
		$fields[] = array(
					 	"field_title" => "Your App ID",
						"field_slug" => "your_app_id",
						"field_type" => "text",
						"default" => get_option("smart_app_banners_your_app_id")
					 );

		//add text input
		$fields[] = array(
					 	"field_title" => "Badge Size (100 = Full, 50 = half...)",
						"field_slug" => "size",
						"field_type" => "text",
						"default" => 100,
					 );
		
		//add select input
		$fields[] = array(
					 	"field_title" => "Align",
						"field_slug" => "align",
						"field_type" => "select",
						"options" => array(
										   array("title" => "Left", "value" => "left"), 
										   array("title" => "Center", "value" => "center"),  
										   array("title" => "Right", "value" => "right"),
										   ),
					 );

		
		
		
		//add all fields to fields property
		$this->fields = $fields;
	}

	/**
	 * Front-end display of widget. This section is not modified or streamlined because front end display is typically custom
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		  /* Before widget */
		  echo $before_widget;
  
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
			
		?>
        <style>
		.<?php echo $this->id ?> {text-align: <?php echo $instance['align'] ?>;}
		.<?php echo $this->id ?> img {width: <?php echo $instance['size'] ?>%;}
		</style>
        <?php
		echo '<div class="textwidget '.$this->id.'">';
		if ( $instance['your_app_id'] != '') {
			echo '<a  href="http://itunes.apple.com/app/id'.$instance['your_app_id'].'"><img style="box-shadow: none;" class="" src="'.plugins_url( 'images/download_on_the_app_store.png' , __FILE__ ).'"></a>';
		}

		echo '</div>';//close div.textwidget
		echo stripslashes($after_widget);
	
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$fields = $this->fields;
		foreach ($fields as $field) {
			$slug = $field['field_slug'];
			$instance[$slug] = strip_tags($new_instance[$slug]);
			if($slug == "size") {
				if(! ( (is_numeric($instance[$slug])) AND (0 < $instance[$slug] && $instance[$slug] <= 100) ) ) {
					$instance[$slug] = 100;
				}
			}
		}

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {


			$fields = $this->fields;
			foreach ($fields as $field) {
				echo $this->get_field_html($instance, $field);
			}

     

	}


	//this is the engine which powers the widget fields. fancy widget field creating logic goes here
	public function get_field_html($instance, $field) {
		$slug = $field['field_slug'];
		
		if( ($field['default']) AND (!(isset($instance[$slug])) )  ) { //set initial default values where applicable
			$value = $field['default'];
		} else {
			$value = $instance[$slug];	
		}


		if ($field['field_type'] == "text") {
			$html .= "<p>
					<label for=\"". $this->get_field_id( $field['field_slug'] ) ."\">
						"._e( $field['field_title'] )."
					</label>
					<input class=\"widefat\" id=\"". $this->get_field_id( $field['field_slug'] ) ."\" name=\"". $this->get_field_name( $field['field_slug'] ) ."\" type='text' value=\"".esc_attr( $value )."\" />
					</p>			
					";
		} elseif($field['field_type'] == "textarea") {
			$html .= "
					".$field['field_title']."<br />
					<textarea class=\"widefat\" rows=\"5\" name=\"". $this->get_field_name( $field['field_slug'] ) ."\" >".stripslashes($value)."</textarea>
					<br /><br />			
					";
		} elseif($field['field_type'] == "checkbox") {
			$html .= "
					".$field['field_title']."
					<input type=\"checkbox\" class=\"checkbox\" id=\"". $this->get_field_id( $field['field_slug'] ) ."\" name=\"". $this->get_field_name( $field['field_slug'] ) ."\" value=1 ". checked( 1 , $value, false ) ." >
					<br /><br />			
					";
		} elseif($field['field_type'] == "select") {
			$options = $field['options'];
			$html .= "<p>
					<label for=\"". $this->get_field_id( $field['field_slug'] ) ."\">
						"._e( $field['field_title'] )."
					</label>
 			<select id=\"". $this->get_field_id( $field['field_slug'] ) ."\" name=\"". $this->get_field_name( $field['field_slug'] ) ."\" class\"widefat\" style=\"width:100%;\">";
			foreach($options as $option) {
				$html .= "<option value=\"".$option[value]."\" ".selected($option[value], $value, false).">".$option[title]."</option>";
			}
 			$html .= "</select>	</p>";		
		} elseif($field['field_type'] == "radio") {
			$options = $field['options'];
			$html .= $field['field_title']."<br />";
			
			foreach($options as $option) {
				$html .= '<label><input type="radio" id="' . $this->get_field_id( $field['field_slug'] ) . $option[value] . '" name="' . $this->get_field_name( $field['field_slug'] ) .'" value="'.$option[value].'"' . checked( $option[value], $value, false ) . '/>';
				$html .= '<span> '.$option[title].'</span></label><br>';
			}
		}
		return $html;
	}


} // end class


// register custom widget
add_action( 'widgets_init', create_function( '', 'register_widget( smart_app_banners_widget );' ) );



//begin widget
/**
 * Adds Custom widget.
 */

class Smart_App_Android_Widget extends WP_Widget {
	
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		
		parent::__construct(
	 		$this->slug = "smart_app_android_widget", // Base ID
			$this->label = "Motech Download for Android", // Name
			array( 'description' => __( 'Displays a Download for Android badge linking to your app', 'text_domain' ), ) // Args
		);
		
		//DECLARE WIDGET INPUT FIELDS
		
		
		//add text input
/*		$fields[] = array(
					 	"field_title" => "Override App ID:",
						"field_slug" => "override_app_id",
						"field_type" => "text",
					 );*/

		//add text
		$fields[] = array(
					 	"field_title" => "Your Apps <a href='http://developer.android.com/distribute/googleplay/promote/linking.html' target='_blank'><i>Package Name</i></a>",
						"field_slug" => "your_app_id",
						"field_type" => "text",
						//"default" => get_option("smart_app_banners_your_app_id")
					 );

		//add text input
		$fields[] = array(
					 	"field_title" => "Badge Size (100 = Full, 50 = half...)",
						"field_slug" => "size",
						"field_type" => "text",
						"default" => 100,
					 );
		
		//add select input
		$fields[] = array(
					 	"field_title" => "Align",
						"field_slug" => "align",
						"field_type" => "select",
						"options" => array(
										   array("title" => "Left", "value" => "left"), 
										   array("title" => "Center", "value" => "center"),  
										   array("title" => "Right", "value" => "right"),
										   ),
					 );

		
		
		
		//add all fields to fields property
		$this->fields = $fields;
	}

	/**
	 * Front-end display of widget. This section is not modified or streamlined because front end display is typically custom
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		  /* Before widget */
		  echo $before_widget;
  
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
			
		?>
        <style>
		.<?php echo $this->id ?> {text-align: <?php echo $instance['align'] ?>;}
		.<?php echo $this->id ?> img {width: <?php echo $instance['size'] ?>%;}
		</style>
        <?php
		echo '<div class="textwidget '.$this->id.'">';
		if ( $instance['your_app_id'] != '') {
			echo '<a  href="http://play.google.com/store/apps/details?id='.$instance['your_app_id'].'"><img style="box-shadow: none;" class="" src="'.plugins_url( 'images/download_for_android.png' , __FILE__ ).'"></a>';
		}

		echo '</div>';//close div.textwidget
		echo stripslashes($after_widget);
	
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$fields = $this->fields;
		foreach ($fields as $field) {
			$slug = $field['field_slug'];
			$instance[$slug] = strip_tags($new_instance[$slug]);
			if($slug == "size") {
				if(! ( (is_numeric($instance[$slug])) AND (0 < $instance[$slug] && $instance[$slug] <= 100) ) ) {
					$instance[$slug] = 100;
				}
			}
		}

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {


			$fields = $this->fields;
			foreach ($fields as $field) {
				echo $this->get_field_html($instance, $field);
			}

     

	}


	//this is the engine which powers the widget fields. fancy widget field creating logic goes here
	public function get_field_html($instance, $field) {
		$slug = $field['field_slug'];
		
		if( ($field['default']) AND (!(isset($instance[$slug])) )  ) { //set initial default values where applicable
			$value = $field['default'];
		} else {
			$value = $instance[$slug];	
		}


		if ($field['field_type'] == "text") {
			$html .= "<p>
					<label for=\"". $this->get_field_id( $field['field_slug'] ) ."\">
						"._e( $field['field_title'] )."
					</label>
					<input class=\"widefat\" id=\"". $this->get_field_id( $field['field_slug'] ) ."\" name=\"". $this->get_field_name( $field['field_slug'] ) ."\" type='text' value=\"".esc_attr( $value )."\" />
					</p>			
					";
		} elseif($field['field_type'] == "textarea") {
			$html .= "
					".$field['field_title']."<br />
					<textarea class=\"widefat\" rows=\"5\" name=\"". $this->get_field_name( $field['field_slug'] ) ."\" >".stripslashes($value)."</textarea>
					<br /><br />			
					";
		} elseif($field['field_type'] == "checkbox") {
			$html .= "
					".$field['field_title']."
					<input type=\"checkbox\" class=\"checkbox\" id=\"". $this->get_field_id( $field['field_slug'] ) ."\" name=\"". $this->get_field_name( $field['field_slug'] ) ."\" value=1 ". checked( 1 , $value, false ) ." >
					<br /><br />			
					";
		} elseif($field['field_type'] == "select") {
			$options = $field['options'];
			$html .= "<p>
					<label for=\"". $this->get_field_id( $field['field_slug'] ) ."\">
						"._e( $field['field_title'] )."
					</label>
 			<select id=\"". $this->get_field_id( $field['field_slug'] ) ."\" name=\"". $this->get_field_name( $field['field_slug'] ) ."\" class\"widefat\" style=\"width:100%;\">";
			foreach($options as $option) {
				$html .= "<option value=\"".$option[value]."\" ".selected($option[value], $value, false).">".$option[title]."</option>";
			}
 			$html .= "</select>	</p>";		
		} elseif($field['field_type'] == "radio") {
			$options = $field['options'];
			$html .= $field['field_title']."<br />";
			
			foreach($options as $option) {
				$html .= '<label><input type="radio" id="' . $this->get_field_id( $field['field_slug'] ) . $option[value] . '" name="' . $this->get_field_name( $field['field_slug'] ) .'" value="'.$option[value].'"' . checked( $option[value], $value, false ) . '/>';
				$html .= '<span> '.$option[title].'</span></label><br>';
			}
		}
		return $html;
	}


} // end class


// register custom widget
add_action( 'widgets_init', create_function( '', 'register_widget( smart_app_android_widget );' ) );