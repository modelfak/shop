<?php
/**
 * Javenist functions and definitions
 */

/**
* Require files
*/
	//TGM-Plugin-Activation
require_once( get_template_directory().'/class-tgm-plugin-activation.php' );
	//Init the Redux Framework
if ( class_exists( 'ReduxFramework' ) && !isset( $redux_demo ) && file_exists( get_template_directory().'/theme-config.php' ) ) {
	require_once( get_template_directory().'/theme-config.php' );
}
	// Theme files
if ( !class_exists( 'javenist_widgets' ) && file_exists( get_template_directory().'/include/javenistwidgets.php' ) ) {
	require_once( get_template_directory().'/include/javenistwidgets.php' );
}
if ( file_exists( get_template_directory().'/include/styleswitcher.php' ) ) {
	require_once( get_template_directory().'/include/styleswitcher.php' );
}
if ( file_exists( get_template_directory().'/include/wooajax.php' ) ) {
	require_once( get_template_directory().'/include/wooajax.php' );
}
if ( file_exists( get_template_directory().'/include/map_shortcodes.php' ) ) {
	require_once( get_template_directory().'/include/map_shortcodes.php' );
}
if ( file_exists( get_template_directory().'/include/shortcodes.php' ) ) {
	require_once( get_template_directory().'/include/shortcodes.php' );
} 

Class Javenist_Class {
	
	/**
	* Global values
	*/
	static function javenist_post_odd_event(){
		global $wp_session;
		
		if(!isset($wp_session["javenist_postcount"])){
			$wp_session["javenist_postcount"] = 0;
		}
		
		$wp_session["javenist_postcount"] = 1 - $wp_session["javenist_postcount"];
		
		return $wp_session["javenist_postcount"];
	}
	static function javenist_post_thumbnail_size($size){
		global $wp_session;
		
		if($size!=''){
			$wp_session["javenist_postthumb"] = $size;
		}
		
		return $wp_session["javenist_postthumb"];
	}
	static function javenist_shop_class($class){
		global $wp_session;
		
		if($class!=''){
			$wp_session["javenist_shopclass"] = $class;
		}
		
		return $wp_session["javenist_shopclass"];
	}
	static function javenist_show_view_mode(){

		$javenist_opt = get_option( 'javenist_opt' );
		
		$javenist_viewmode = 'grid-view'; //default value
		
		if(isset($javenist_opt['default_view'])) {
			$javenist_viewmode = $javenist_opt['default_view'];
		}
		if(isset($_GET['view']) && $_GET['view']!=''){
			$javenist_viewmode = $_GET['view'];
		}
		
		return $javenist_viewmode;
	}
	static function javenist_shortcode_products_count(){
		global $wp_session;
		
		$javenist_productsfound = 0;
		if(isset($wp_session["javenist_productsfound"])){
			$javenist_productsfound = $wp_session["javenist_productsfound"];
		}
		
		return $javenist_productsfound;
	}

	static function javenist_products_count() {
		global $wp_query;

		$javenist_opt = get_option( 'javenist_opt' );
		$perp = $javenist_opt['product_per_page'];
		$max_page = $wp_query->max_num_pages;
		$total = total_product_count();
		$number_products_page = (($perp*$max_page) - $total );
		$current_page = max( 1, get_query_var( 'paged' ) );
		$javenist_products_count =0;
		if ($current_page == $max_page) {
			
			$javenist_products_count = $perp - $number_products_page;
		}
		return $javenist_products_count;
	}
	
	/**
	* Constructor
	*/
	function __construct() {
		// Register action/filter callbacks
		
			//WooCommerce - action/filter
		add_theme_support( 'woocommerce' );
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
		add_filter( 'get_product_search_form', array($this, 'javenist_woo_search_form'));
		add_filter( 'woocommerce_shortcode_products_query', array($this, 'javenist_woocommerce_shortcode_count'));
		add_action( 'woocommerce_share', array($this, 'javenist_woocommerce_social_share'), 35 );
		add_action( 'woocommerce_archive_description', array($this, 'javenist_woocommerce_category_image'), 2 );

		remove_action('woocommerce_after_shop_loop','woocommerce_pagination',10);
		add_action('woocommerce_pagination','woocommerce_pagination',10);

			//move message to top
		remove_action( 'woocommerce_before_shop_loop', 'wc_print_notices', 10 );
		add_action( 'woocommerce_show_message', 'wc_print_notices', 10 );

		//remove add to cart button after item
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10); 

		 
		
			//Single product organize   
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
		add_action( 'woocommerce_after_single_product_summary' , 'woocommerce_output_product_data_tabs', 15 );

		 
		
			//WooProjects - Project organize
		remove_action( 'projects_before_single_project_summary', 'projects_template_single_title', 10 );
		add_action( 'projects_single_project_summary', 'projects_template_single_title', 5 );
		remove_action( 'projects_before_single_project_summary', 'projects_template_single_short_description', 20 );
		remove_action( 'projects_before_single_project_summary', 'projects_template_single_gallery', 40 );
		add_action( 'projects_single_project_gallery', 'projects_template_single_gallery', 40 );
		
			//WooProjects - projects list
		remove_action( 'projects_loop_item', 'projects_template_loop_project_title', 20 );
		
			//Theme actions
		add_action( 'after_setup_theme', array($this, 'javenist_setup'));
		add_action( 'tgmpa_register', array($this, 'javenist_register_required_plugins')); 
		add_action( 'widgets_init', array($this, 'javenist_override_woocommerce_widgets'), 15 );
		
		add_action( 'wp_enqueue_scripts', array($this, 'javenist_scripts_styles') );
		add_action( 'wp_head', array($this, 'javenist_custom_code_header'));
		add_action( 'widgets_init', array($this, 'javenist_widgets_init'));
		add_action( 'add_meta_boxes', array($this, 'javenist_add_meta_box'));
		add_action( 'save_post', array($this, 'javenist_save_meta_box_data'));
		add_action('comment_form_before_fields', array($this, 'javenist_before_comment_fields'));
		add_action('comment_form_after_fields', array($this, 'javenist_after_comment_fields'));
		add_action( 'customize_register', array($this, 'javenist_customize_register'));
		add_action( 'customize_preview_init', array($this, 'javenist_customize_preview_js'));
		add_action('init', array($this, 'javenist_remove_redux_framework_notification'));
		add_action('admin_enqueue_scripts', array($this, 'javenist_admin_style'));
		
			//Theme filters 
		add_filter( 'loop_shop_per_page', array($this, 'javenist_woo_change_per_page'), 20 );
		add_filter( 'woocommerce_output_related_products_args', array($this, 'javenist_woo_related_products_limit'));
		add_filter( 'get_search_form', array($this, 'javenist_search_form'));
		add_filter('excerpt_more', array($this, 'javenist_new_excerpt_more'));
		add_filter( 'excerpt_length', array($this, 'javenist_change_excerpt_length'), 999 );
		add_filter('wp_nav_menu_objects', array($this, 'javenist_first_and_last_menu_class'));
		add_filter( 'wp_page_menu_args', array($this, 'javenist_page_menu_args'));
		add_filter('dynamic_sidebar_params', array($this, 'javenist_widget_first_last_class'));
		add_filter('dynamic_sidebar_params', array($this, 'javenist_mega_menu_widget_change'));
		add_filter( 'dynamic_sidebar_params', array($this, 'javenist_put_widget_content'));
		
		//Adding theme support
		if ( ! isset( $content_width ) ) {
			$content_width = 625;
		}
	}
	
	/**
	* Filter callbacks
	* ----------------
	*/
	 
	// Change products per page
	function javenist_woo_change_per_page() {
		$javenist_opt = get_option( 'javenist_opt' );
		
		return $javenist_opt['product_per_page'];
	}
	//Change number of related products on product page. Set your own value for 'posts_per_page'
	function javenist_woo_related_products_limit( $args ) {
		global $product;

		$javenist_opt = get_option( 'javenist_opt' );

		$args['posts_per_page'] = $javenist_opt['related_amount'];

		return $args;
	}
	// Count number of products from shortcode
	function javenist_woocommerce_shortcode_count( $args ) {
		global $wp_session;
		
		$javenist_productsfound = new WP_Query($args);
		$javenist_productsfound = $javenist_productsfound->post_count;
		
		$wp_session["javenist_productsfound"] = $javenist_productsfound;
		
		return $args;
	}
	//Change search form
	function javenist_search_form( $form ) {
		if(get_search_query()!=''){
			$search_str = get_search_query();
		} else {
			$search_str = esc_html__( 'Search...', 'javenist' );
		}
		
		$form = '<form role="search" method="get" id="blogsearchform" class="searchform" action="' . esc_url(home_url( '/' ) ). '" >
		<div class="form-input">
			<input class="input_text" type="text" value="'.esc_attr($search_str).'" name="s" id="search_input" />
			<button class="button" type="submit" id="blogsearchsubmit"><i class="ion-ios-search-strong"></i></button>
			</div>
		</form>';

		return $form;
	}
	//Change woocommerce search form
	function javenist_woo_search_form( $form ) {
		global $wpdb;
		
		if(get_search_query()!=''){
			$search_str = get_search_query();
		} else {
			$search_str = esc_html__( 'Search product...', 'javenist' );
		}
		
		$form = '<form role="search" method="get" id="searchform" action="'.esc_url( home_url( '/'  ) ).'">';
			$form .= '<div class="form-input">';
				$form .= '<input type="text" value="'.esc_attr($search_str).'" name="s" id="ws" placeholder="" />';
				$form .= '<button class="btn btn-primary" type="submit" id="wsearchsubmit"><i class="ion-ios-search-strong"></i></button>';
				$form .= '<input type="hidden" name="post_type" value="product" />';
			$form .= '</div>';
		$form .= '</form>';

		return $form;
	}
	// Replaces the excerpt "more" text by a link
	function javenist_new_excerpt_more($more) {
		return '';
	}
	//Change excerpt length
	function javenist_change_excerpt_length( $length ) {
		$javenist_opt = get_option( 'javenist_opt' );
		
		if(isset($javenist_opt['excerpt_length'])){
			return $javenist_opt['excerpt_length'];
		}
		
		return 22;
	}
	//Add 'first, last' class to menu
	function javenist_first_and_last_menu_class($items) {
		$items[1]->classes[] = 'first';
		$items[count($items)]->classes[] = 'last';
		return $items;
	}
	/**
	 * Filter the page menu arguments.
	 *
	 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
	 *
	 * @since Javenist 1.0
	 */
	function javenist_page_menu_args( $args ) {
		if ( ! isset( $args['show_home'] ) )
			$args['show_home'] = true;
		return $args;
	}
	//Add first, last class to widgets
	function javenist_widget_first_last_class($params) {
		global $my_widget_num;
		
		$class = '';
		
		$this_id = $params[0]['id']; // Get the id for the current sidebar we're processing
		$arr_registered_widgets = wp_get_sidebars_widgets(); // Get an array of ALL registered widgets	

		if(!$my_widget_num) {// If the counter array doesn't exist, create it
			$my_widget_num = array();
		}

		if(!isset($arr_registered_widgets[$this_id]) || !is_array($arr_registered_widgets[$this_id])) { // Check if the current sidebar has no widgets
			return $params; // No widgets in this sidebar... bail early.
		}

		if(isset($my_widget_num[$this_id])) { // See if the counter array has an entry for this sidebar
			$my_widget_num[$this_id] ++;
		} else { // If not, create it starting with 1
			$my_widget_num[$this_id] = 1;
		}

		if($my_widget_num[$this_id] == 1) { // If this is the first widget
			$class .= ' widget-first ';
		} elseif($my_widget_num[$this_id] == count($arr_registered_widgets[$this_id])) { // If this is the last widget
			$class .= ' widget-last ';
		}
		
		$params[0]['before_widget'] = str_replace('first_last', ' '.$class.' ', $params[0]['before_widget']);
		
		return $params;
	}
	//Change mega menu widget from div to li tag
	function javenist_mega_menu_widget_change($params) {
		
		$sidebar_id = $params[0]['id'];
		
		$pos = strpos($sidebar_id, '_menu_widgets_area_');
		
		if ( !$pos == false ) {
			$params[0]['before_widget'] = '<li class="widget_mega_menu">'.$params[0]['before_widget'];
			$params[0]['after_widget'] = $params[0]['after_widget'].'</li>';
		}
		
		return $params;
	}
	// Push sidebar widget content into a div
	function javenist_put_widget_content( $params ) {
		global $wp_registered_widgets;

		if( $params[0]['id']=='sidebar-category' ){
			$settings_getter = $wp_registered_widgets[ $params[0]['widget_id'] ]['callback'][0];
			$settings = $settings_getter->get_settings();
			$settings = $settings[ $params[1]['number'] ];
			
			if($params[0]['widget_name']=="Text" && isset($settings['title']) && $settings['text']=="") { // if text widget and no content => don't push content
				return $params;
			}
			if( isset($settings['title']) && $settings['title']!='' ){
				$params[0][ 'after_title' ] .= '<div class="widget_content">';
				$params[0][ 'after_widget' ] = '</div>'.$params[0][ 'after_widget' ];
			} else {
				$params[0][ 'before_widget' ] .= '<div class="widget_content">';
				$params[0][ 'after_widget' ] = '</div>'.$params[0][ 'after_widget' ];
			}
		}
		
		return $params;
	}
	
	/**
	* Action hooks
	* ----------------
	*/
	/**
	 * Javenist setup.
	 *
	 * Sets up theme defaults and registers the various WordPress features that
	 * Javenist supports.
	 *
	 * @uses load_theme_textdomain() For translation/localization support.
	 * @uses add_editor_style() To add a Visual Editor stylesheet.
	 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
	 * 	custom background, and post formats.
	 * @uses register_nav_menu() To add support for navigation menus.
	 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
	 *
	 * @since Javenist 1.0
	 */
	function javenist_setup() {
		/*
		 * Makes Javenist available for translation.
		 *
		 * Translations can be added to the /languages/ directory.
		 * If you're building a theme based on Javenist, use a find and replace
		 * to change 'javenist' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'javenist', get_template_directory() . '/languages' );

		// This theme styles the visual editor with editor-style.css to match the theme style.
		add_editor_style();

		// Adds RSS feed links to <head> for posts and comments.
		add_theme_support( 'automatic-feed-links' );

		// This theme supports a variety of post formats.
		add_theme_support( 'post-formats', array( 'image', 'gallery', 'video', 'audio' ) );

		// Register menus
		register_nav_menu( 'primary', esc_html__( 'Primary Menu', 'javenist' ) );
		register_nav_menu( 'topmenu', esc_html__( 'Top Menu', 'javenist' ) );
		register_nav_menu( 'mobilemenu', esc_html__( 'Mobile Menu', 'javenist' ) );
		register_nav_menu( 'categories', esc_html__( 'Categories Menu', 'javenist' ) );

		/*
		 * This theme supports custom background color and image,
		 * and here we also set up the default background color.
		 */
		add_theme_support( 'custom-background', array(
			'default-color' => 'e6e6e6',
		) );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );
		
		// This theme uses a custom image size for featured images, displayed on "standard" posts.
		add_theme_support( 'post-thumbnails' );

		set_post_thumbnail_size( 1170, 9999 ); // Unlimited height, soft crop
		add_image_size( 'javenist-category-thumb', 888, 532, true ); // (cropped)
		add_image_size( 'javenist-post-thumb', 370, 259, true ); // (cropped)
		add_image_size( 'javenist-post-thumbwide', 423, 253, true ); // (cropped)
	}
	//Override woocommerce widgets
	function javenist_override_woocommerce_widgets() {
		//Show mini cart on all pages
		if ( class_exists( 'WC_Widget_Cart' ) ) {
			unregister_widget( 'WC_Widget_Cart' ); 
			include_once( get_template_directory().'/woocommerce/class-wc-widget-cart.php' );
			register_widget( 'Custom_WC_Widget_Cart' );
		}
	}
	// Add image to category description
	function javenist_woocommerce_category_image() {
		if ( is_product_category() ){
			global $wp_query;
			
			$cat = $wp_query->get_queried_object();
			$thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
			$image = wp_get_attachment_url( $thumbnail_id );
			
			if ( $image ) {
				echo '<p class="category-image-desc"><img src="' . esc_url($image) . '" alt="" /></p>';
			}
		}
	}
	//Display social sharing on product page
	function javenist_woocommerce_social_share(){
		$javenist_opt = get_option( 'javenist_opt' );
	?>
		<?php if ($javenist_opt['share_code']!='') { ?>
			<div class="share_buttons">
				<?php 
					echo wp_kses($javenist_opt['share_code'], array(
						'div' => array(
							'class' => array()
						),
						'span' => array(
							'class' => array(),
							'displayText' => array()
						),
					));
				?>
			</div>
		<?php } ?>
	<?php
	}
	/**
	 * Enqueue scripts and styles for front-end.
	 *
	 * @since Javenist 1.0
	 */
	function javenist_scripts_styles() {
		global $wp_styles, $wp_scripts;

		$javenist_opt = get_option( 'javenist_opt' );
		
		/*
		 * Adds JavaScript to pages with the comment form to support
		 * sites with threaded comments (when in use).
		*/
		
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );
		
		// Add Bootstrap JavaScript
		wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '3.2.0', true );
		
		// Add Slick files
		wp_enqueue_script( 'slick', get_template_directory_uri() . '/js/slick/slick.min.js', array('jquery'), '1.6.0', true );
		wp_enqueue_style( 'slick', get_template_directory_uri() . '/js/slick/slick.css', array(), '1.6.0' );
		
		// Add owl-carousel
		// Add owl-carousel file
		wp_enqueue_script( 'owl-carousel', get_template_directory_uri() . '/js/owl.carousel.js', array('jquery'), '1.3.3', true ); 
		wp_enqueue_script( 'owl-carousel-min', get_template_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '1.3.3', true );

		// Add Chosen js files
		wp_enqueue_script( 'chosen', get_template_directory_uri() . '/js/chosen/chosen.jquery.min.js', array('jquery'), '1.3.0', true );
		wp_enqueue_script( 'chosenproto', get_template_directory_uri() . '/js/chosen/chosen.proto.min.js', array('jquery'), '1.3.0', true );
		wp_enqueue_style( 'chosen', get_template_directory_uri() . '/js/chosen/chosen.min.css', array(), '1.3.0' );
		
		// Add parallax script files
		
		// Add Fancybox
		wp_enqueue_script( 'fancybox', get_template_directory_uri() . '/js/fancybox/jquery.fancybox.pack.js', array('jquery'), '2.1.5', true );
		wp_enqueue_script( 'fancybox-buttons', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-buttons.js', array('jquery'), '1.0.5', true );
		wp_enqueue_script( 'fancybox-media', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-media.js', array('jquery'), '1.0.6', true );
		wp_enqueue_script( 'fancybox-thumbs', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-thumbs.js', array('jquery'), '1.0.7', true );
		wp_enqueue_style( 'fancybox-css', get_template_directory_uri() . '/js/fancybox/jquery.fancybox.css', array(), '2.1.5' );
		wp_enqueue_style( 'fancybox-buttons', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-buttons.css', array(), '1.0.5' );
		wp_enqueue_style( 'fancybox-thumbs', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-thumbs.css', array(), '1.0.7' );
		
		//Superfish
		wp_enqueue_script( 'superfish', get_template_directory_uri() . '/js/superfish/superfish.min.js', array('jquery'), '1.3.15', true );
		
		//Add Shuffle js
		wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/js/modernizr.custom.min.js', array('jquery'), '2.6.2', true );
		wp_enqueue_script( 'shuffle', get_template_directory_uri() . '/js/jquery.shuffle.min.js', array('jquery'), '3.0.0', true );

		//Add mousewheel
		wp_enqueue_script( 'mousewheel', get_template_directory_uri() . '/js/jquery.mousewheel.min.js', array('jquery'), '3.1.12', true );
		
		// Add jQuery countdown file
		wp_enqueue_script( 'countdown', get_template_directory_uri() . '/js/jquery.countdown.min.js', array('jquery'), '2.0.4', true );
		
		// Add jQuery counter files
		wp_enqueue_script( 'waypoints', get_template_directory_uri() . '/js/waypoints.min.js', array('jquery'), '1.0', true );
		wp_enqueue_script( 'counterup', get_template_directory_uri() . '/js/jquery.counterup.min.js', array('jquery'), '1.0', true );
		
		// Add jQuery TimeCircles
		wp_enqueue_script( 'TimeCircles-js', get_template_directory_uri() . '/js/TimeCircles.js', array(), true );
		
		// Add variables.js file
		wp_enqueue_script( 'variables-js', get_template_directory_uri() . '/js/variables.js', array('jquery'), '20140826', true );
		
		// Add theme.js file
		wp_enqueue_script( 'javenist-jquery', get_template_directory_uri() . '/js/theme.js', array('jquery'), '20140826', true );

		$font_url = $this->javenist_get_font_url();
		if ( ! empty( $font_url ) )
			wp_enqueue_style( 'javenist-fonts', esc_url_raw( $font_url ), array(), null );

		// Loads our main stylesheet.
		wp_enqueue_style( 'javenist-style', get_stylesheet_uri() );
		
		// Mega Main Menu
		wp_enqueue_style( 'megamenu-css', get_template_directory_uri() . '/css/megamenu_style.css', array(), '2.0.4' );
	
		// Load fontawesome css
		wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/css/font-awesome.min.css', array(), '4.2.0' );

		// Load Ionicons css
		wp_enqueue_style( 'ionicons', get_template_directory_uri() . '/css/ionicons.css', array(), '2.0.1' );
		 
		// Load simple-line-icons css
		wp_enqueue_style( 'simple-line-icons', get_template_directory_uri() . '/css/simple-line-icons.css', array(), '2.3.2' );
		 
		 
		// Load animate css
		wp_enqueue_style( 'animate', get_template_directory_uri() . '/css/animate.css', array());
		
		// Load owl-carousel css
		wp_enqueue_style( 'owl.carousel', get_template_directory_uri() . '/css/owl.carousel.css', array(), '1.3.3');
		
		// Load bootstrap css
		wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '3.2.0' );
		
		// Compile Less to CSS
		$previewpreset = (isset($_REQUEST['preset']) ? $_REQUEST['preset'] : null);
			//get preset from url (only for demo/preview)
		if($previewpreset){
			$_SESSION["preset"] = $previewpreset;
		}
		$presetopt = 1;
		if(!isset($_SESSION["preset"])){
			$_SESSION["preset"] = 1;
		}
		if($_SESSION["preset"] != 1) {
			$presetopt = $_SESSION["preset"];
		} else { /* if no preset varialbe found in url, use from theme options */
			if(isset($javenist_opt['preset_option'])){
				$presetopt = $javenist_opt['preset_option'];
			}
		}
		if(!isset($presetopt)) $presetopt = 1; /* in case first time install theme, no options found */
		
		if(isset($javenist_opt['enable_less'])){
			if($javenist_opt['enable_less']){
				$themevariables = array(
					'body_font'=> $javenist_opt['bodyfont']['font-family'],
					'text_color'=> $javenist_opt['bodyfont']['color'],
					'text_selected_bg' => $javenist_opt['text_selected_bg'],
					'text_selected_color' => $javenist_opt['text_selected_color'],
					'text_size'=> $javenist_opt['bodyfont']['font-size'],
					'border_color'=> $javenist_opt['border_color']['border-color'],
					
					'heading_font'=> $javenist_opt['headingfont']['font-family'],
					'heading_color'=> $javenist_opt['headingfont']['color'],
					'heading_font_weight'=> $javenist_opt['headingfont']['font-weight'],  
					
					'menu_font'=> $javenist_opt['menufont']['font-family'],
					'menu_color'=> $javenist_opt['menufont']['color'],
					'menu_font_size'=> $javenist_opt['menufont']['font-size'],
					'menu_font_weight'=> $javenist_opt['menufont']['font-weight'],
					'sub_menu_bg' => $javenist_opt['sub_menu_bg'],
					'sub_menu_color' => $javenist_opt['sub_menu_color'],

					'vmenu_font'=> $javenist_opt['vmenufont']['font-family'],
					'vmenu_color'=> $javenist_opt['vmenufont']['color'],
					'vmenu_font_size'=> $javenist_opt['vmenufont']['font-size'],
					'vmenu_font_weight'=> $javenist_opt['vmenufont']['font-weight'],
					'vsub_menu_bg' => $javenist_opt['vsub_menu_bg'],

					'price_font'=> $javenist_opt['pricefont']['font-family'],
					'price_color'=> $javenist_opt['pricefont']['color'], 
					'price_size'=> $javenist_opt['pricefont']['font-size'],
					'price_font_weight'=> $javenist_opt['pricefont']['font-weight'],
					
					'link_color' => $javenist_opt['link_color']['regular'],
					'link_hover_color' => $javenist_opt['link_color']['hover'],
					'link_active_color' => $javenist_opt['link_color']['active'],
					
					'primary_color' => $javenist_opt['primary_color'],
					
					'sale_color' => $javenist_opt['sale_color'],
					'saletext_color' => $javenist_opt['saletext_color'],
					'rate_color' => $javenist_opt['rate_color'],

					'topbar_color' => $javenist_opt['topbar_color'],
					'topbar_link_color' => $javenist_opt['topbar_link_color']['regular'],
					'topbar_link_hover_color' => $javenist_opt['topbar_link_color']['hover'],
					'topbar_link_active_color' => $javenist_opt['topbar_link_color']['active'],

					'header_color' => $javenist_opt['header_color'],
					'header_link_color' => $javenist_opt['header_link_color']['regular'],
					'header_link_hover_color' => $javenist_opt['header_link_color']['hover'],
					'header_link_active_color' => $javenist_opt['header_link_color']['active'],


					'footer_color' => $javenist_opt['footer_color'],
					'footer_link_color' => $javenist_opt['footer_link_color']['regular'],
					'footer_link_hover_color' => $javenist_opt['footer_link_color']['hover'],
					'footer_link_active_color' => $javenist_opt['footer_link_color']['active'],
				);
				
				if(isset($javenist_opt['header_sticky_bg']['rgba']) && $javenist_opt['header_sticky_bg']['rgba']!="") {
					$themevariables['header_sticky_bg'] = $javenist_opt['header_sticky_bg']['rgba'];
				} else {
					$themevariables['header_sticky_bg'] = 'rgba(54,63,77,0.95)';
				}
				if(isset($javenist_opt['header_bg']['background-color']) && $javenist_opt['header_bg']['background-color']!="") {
					$themevariables['header_bg'] = $javenist_opt['header_bg']['background-color'];
				} else {
					$themevariables['header_bg'] = '#ffffff';
				}
				if(isset($javenist_opt['header_bg']['background-image']) && $javenist_opt['header_bg']['background-image']!="") {
					$themevariables['header_bg_image'] = $javenist_opt['header_bg']['background-image'];
				} else {
					$themevariables['header_bg_image'] = 'none';
				}
				if(isset($javenist_opt['footer_bg']['background-color']) && $javenist_opt['footer_bg']['background-color']!="") {
					$themevariables['footer_bg'] = $javenist_opt['footer_bg']['background-color'];
				} else {
					$themevariables['footer_bg'] = '#ffffff';
				}
				 
				switch ($presetopt) { 
					case 2: 
						$themevariables['header_bg'] = '#fff'; 
						$themevariables['topbar_color'] = '#363f4d'; 
						$themevariables['topbar_link_color'] = '#363f4d';
						$themevariables['header_color'] = '#363f4d';   
						$themevariables['header_link_color'] = '#363f4d';   
					break;  
					case 3:   
						$themevariables['header_bg'] = '#fff'; 
						$themevariables['topbar_color'] = '#363f4d'; 
						$themevariables['topbar_link_color'] = '#363f4d';
						$themevariables['header_color'] = '#363f4d';   
						$themevariables['header_link_color'] = '#363f4d'; 
						$themevariables['header_sticky_bg'] = 'rgba(255,255,255,0.95)';  
						$themevariables['menu_color'] = '#363f4d';  
					break;   
					case 4: 
						$themevariables['primary_color'] = '#68b501';  
						$themevariables['header_bg'] = '#68b501';
						$themevariables['price_color'] = '#68b501';  
						$themevariables['footer_link_hover_color'] = '#68b501';   
						$themevariables['footer_link_active_color'] = '#68b501';      
					break;  
					case 5: 
						$themevariables['primary_color'] = '#68b501';  
						$themevariables['header_bg'] = '#fff';
						$themevariables['topbar_color'] = '#363f4d'; 
						$themevariables['topbar_link_color'] = '#363f4d';
						$themevariables['header_color'] = '#363f4d';   
						$themevariables['header_link_color'] = '#363f4d';   
						$themevariables['price_color'] = '#68b501';  
						$themevariables['footer_link_hover_color'] = '#68b501';   
						$themevariables['footer_link_active_color'] = '#68b501';      
					break;  
					case 6:  
						$themevariables['primary_color'] = '#f1b306';  
						$themevariables['header_bg'] = '#20252d';
						$themevariables['price_color'] = '#f1b306';  
						$themevariables['footer_link_hover_color'] = '#f1b306';   
						$themevariables['footer_link_active_color'] = '#f1b306';  
					break;   
				}

				if(function_exists('compileLessFile')){
					compileLessFile('reset.less', 'reset'.$presetopt.'.css', $themevariables);
					compileLessFile('global.less', 'global'.$presetopt.'.css', $themevariables);
					compileLessFile('pages.less', 'pages'.$presetopt.'.css', $themevariables);
					compileLessFile('woocommerce.less', 'woocommerce'.$presetopt.'.css', $themevariables);
					compileLessFile('layouts.less', 'layouts'.$presetopt.'.css', $themevariables);
					compileLessFile('responsive.less', 'responsive'.$presetopt.'.css', $themevariables);
				}
			}
		}
		
		// Load main theme css style files
		wp_enqueue_style( 'javenist-css-reset', get_template_directory_uri() . '/css/reset'.$presetopt.'.css', array('bootstrap'), '1.0.0' );
		wp_enqueue_style( 'javenist-css-global', get_template_directory_uri() . '/css/global'.$presetopt.'.css', array('javenist-css-reset'), '1.0.0' );
		wp_enqueue_style( 'javenist-css-pages', get_template_directory_uri() . '/css/pages'.$presetopt.'.css', array('javenist-css-global'), '1.0.0' );
		wp_enqueue_style( 'javenist-css-woocommerce', get_template_directory_uri() . '/css/woocommerce'.$presetopt.'.css', array('javenist-css-pages'), '1.0.0' );
		wp_enqueue_style( 'javenist-css-layouts', get_template_directory_uri() . '/css/layouts'.$presetopt.'.css', array('javenist-css-woocommerce'), '1.0.0' );
		wp_enqueue_style( 'javenist-css-responsive', get_template_directory_uri() . '/css/responsive'.$presetopt.'.css', array('javenist-css-layouts'), '1.0.0' );
		wp_enqueue_style( 'javenist-css-custom', get_template_directory_uri() . '/css/opt_css.css', array('javenist-css-responsive'), '1.0.0' );
		
		if(function_exists('WP_Filesystem')){
			if ( ! WP_Filesystem() ) {
				$url = wp_nonce_url();
				request_filesystem_credentials($url, '', true, false, null);
			}
			
			global $wp_filesystem;
			//add custom css, sharing code to header
			if($wp_filesystem->exists(get_template_directory(). '/css/opt_css.css')){
				$customcss = $wp_filesystem->get_contents(get_template_directory(). '/css/opt_css.css');
				
				if(isset($javenist_opt['custom_css']) && $customcss!=$javenist_opt['custom_css']){ //if new update, write file content
					$wp_filesystem->put_contents(
						get_template_directory(). '/css/opt_css.css',
						$javenist_opt['custom_css'],
						FS_CHMOD_FILE // predefined mode settings for WP files
					);
				}
			} else {
				$wp_filesystem->put_contents(
					get_template_directory(). '/css/opt_css.css',
					$javenist_opt['custom_css'],
					FS_CHMOD_FILE // predefined mode settings for WP files
				);
			}
		}
		
		if(isset($javenist_opt['enable_sswitcher'])){
			if($javenist_opt['enable_sswitcher']){
				// Add styleswitcher.js file
				wp_enqueue_script( 'styleswitcher-js', get_template_directory_uri() . '/js/styleswitcher.js', array(), '20140826', true );
				// Load styleswitcher css style
				wp_enqueue_style( 'styleswitcher-css', get_template_directory_uri() . '/css/styleswitcher.css', array(), '1.0.0' );
				// Load scroll bar js
				wp_enqueue_script( 'scrollbar-js', get_template_directory_uri() . '/js/jquery.scrollbar.min.js', array('jquery'), '0.2.8', true );
				wp_enqueue_style( 'scrollbar-css', get_template_directory_uri() . '/css/scrollbar.css', array(), '1.0.0' );
			}
		}
		
		//add javascript variables
		ob_start(); ?>
		var javenist_brandnumber = <?php if(isset($javenist_opt['brandnumber'])) { echo esc_js($javenist_opt['brandnumber']); } else { echo '6'; } ?>,
			javenist_brandscrollnumber = <?php if(isset($javenist_opt['brandscrollnumber'])) { echo esc_js($javenist_opt['brandscrollnumber']); } else { echo '2';} ?>,
			javenist_brandpause = <?php if(isset($javenist_opt['brandpause'])) { echo esc_js($javenist_opt['brandpause']); } else { echo '3000'; } ?>,
			javenist_brandanimate = <?php if(isset($javenist_opt['brandanimate'])) { echo esc_js($javenist_opt['brandanimate']); } else { echo '700';} ?>;
		var javenist_brandscroll = false;
			<?php if(isset($javenist_opt['brandscroll'])){ ?>
				javenist_brandscroll = <?php echo esc_js($javenist_opt['brandscroll'])==1 ? 'true': 'false'; ?>;
			<?php } ?>
		var javenist_categoriesnumber = <?php if(isset($javenist_opt['categoriesnumber'])) { echo esc_js($javenist_opt['categoriesnumber']); } else { echo '6'; } ?>,
			javenist_categoriesscrollnumber = <?php if(isset($javenist_opt['categoriesscrollnumber'])) { echo esc_js($javenist_opt['categoriesscrollnumber']); } else { echo '2';} ?>,
			javenist_categoriespause = <?php if(isset($javenist_opt['categoriespause'])) { echo esc_js($javenist_opt['categoriespause']); } else { echo '3000'; } ?>,
			javenist_categoriesanimate = <?php if(isset($javenist_opt['categoriesanimate'])) { echo esc_js($javenist_opt['categoriesanimate']); } else { echo '700';} ?>;
		var javenist_categoriesscroll = false;
			<?php if(isset($javenist_opt['categoriesscroll'])){ ?>
				javenist_categoriesscroll = <?php echo esc_js($javenist_opt['categoriesscroll'])==1 ? 'true': 'false'; ?>;
			<?php } ?>
		var javenist_blogpause = <?php if(isset($javenist_opt['blogpause'])) { echo esc_js($javenist_opt['blogpause']); } else { echo '3000'; } ?>,
			javenist_bljavenistmate = <?php if(isset($javenist_opt['bljavenistmate'])) { echo esc_js($javenist_opt['bljavenistmate']); } else { echo '700'; } ?>;
		var javenist_blogscroll = false;
			<?php if(isset($javenist_opt['blogscroll'])){ ?>
				javenist_blogscroll = <?php echo esc_js($javenist_opt['blogscroll'])==1 ? 'true': 'false'; ?>;
			<?php } ?>
		var javenist_testipause = <?php if(isset($javenist_opt['testipause'])) { echo esc_js($javenist_opt['testipause']); } else { echo '3000'; } ?>,
			javenist_testianimate = <?php if(isset($javenist_opt['testianimate'])) { echo esc_js($javenist_opt['testianimate']); } else { echo '700'; } ?>;
		var javenist_testiscroll = false; 
			<?php if(isset($javenist_opt['testiscroll'])){ ?>
				javenist_testiscroll = <?php echo esc_js($javenist_opt['testiscroll'])==1 ? 'true': 'false'; ?>;
			<?php } ?>
		var javenist_catenumber = <?php if(isset($javenist_opt['catenumber'])) { echo esc_js($javenist_opt['catenumber']); } else { echo '6'; } ?>,
			javenist_catescrollnumber = <?php if(isset($javenist_opt['catescrollnumber'])) { echo esc_js($javenist_opt['catescrollnumber']); } else { echo '2';} ?>,
			javenist_catepause = <?php if(isset($javenist_opt['catepause'])) { echo esc_js($javenist_opt['catepause']); } else { echo '3000'; } ?>,
			javenist_cateanimate = <?php if(isset($javenist_opt['cateanimate'])) { echo esc_js($javenist_opt['cateanimate']); } else { echo '700';} ?>;
		var javenist_catescroll = false;
			<?php if(isset($javenist_opt['catescroll'])){ ?>
				javenist_catescroll = <?php echo esc_js($javenist_opt['catescroll'])==1 ? 'true': 'false'; ?>;
			<?php } ?>
		var javenist_menu_number = <?php if(isset($javenist_opt['categories_menu_items'])) { echo esc_js((int)$javenist_opt['categories_menu_items']+1); } else { echo '9';} ?>; 

		var javenist_sticky_header = false;
			<?php if(isset($javenist_opt['sticky_header'])){ ?>
				javenist_sticky_header = <?php echo esc_js($javenist_opt['sticky_header'])==1 ? 'true': 'false'; ?>;
			<?php } ?>

		var javenist_item_first = <?php if(isset($javenist_opt['item_first'])) { echo esc_js($javenist_opt['item_first']); } else { echo '12'; } ?>,
			javenist_moreless_products = <?php if(isset($javenist_opt['moreless_products'])) { echo esc_js($javenist_opt['moreless_products']); } else { echo '4'; } ?>;

		jQuery(document).ready(function(){
			jQuery("#ws").focus(function(){
				if(jQuery(this).val()=="<?php echo esc_html__( 'Search product...', 'javenist' )?>"){
					jQuery(this).val("");
				}
			});
			jQuery("#ws").focusout(function(){
				if(jQuery(this).val()==""){
					jQuery(this).val("<?php echo esc_html__( 'Search product...', 'javenist' )?>");
				}
			});
			jQuery("#wsearchsubmit").click(function(){
				if(jQuery("#ws").val()=="<?php echo esc_html__( 'Search product...', 'javenist' )?>" || jQuery("#ws").val()==""){
					jQuery("#ws").focus();
					return false;
				}
			});
			jQuery("#search_input").focus(function(){
				if(jQuery(this).val()=="<?php echo esc_html__( 'Search...', 'javenist' )?>"){
					jQuery(this).val("");
				}
			});
			jQuery("#search_input").focusout(function(){
				if(jQuery(this).val()==""){
					jQuery(this).val("<?php echo esc_html__( 'Search...', 'javenist' )?>");
				}
			});
			jQuery("#blogsearchsubmit").click(function(){
				if(jQuery("#search_input").val()=="<?php echo esc_html__( 'Search...', 'javenist' )?>" || jQuery("#search_input").val()==""){
					jQuery("#search_input").focus();
					return false;
				}
			});
		});
		<?php
		$jsvars = ob_get_contents();
		ob_end_clean();
		
		if(function_exists('WP_Filesystem')){
			if($wp_filesystem->exists(get_template_directory(). '/js/variables.js')){
				$jsvariables = $wp_filesystem->get_contents(get_template_directory(). '/js/variables.js');
				
				if($jsvars!=$jsvariables){ //if new update, write file content
					$wp_filesystem->put_contents(
						get_template_directory(). '/js/variables.js',
						$jsvars,
						FS_CHMOD_FILE // predefined mode settings for WP files
					);
				}
			} else {
				$wp_filesystem->put_contents(
					get_template_directory(). '/js/variables.js',
					$jsvars,
					FS_CHMOD_FILE // predefined mode settings for WP files
				);
			}
		}
		//add css for footer, header templates
		$jscomposer_templates_args = array(
			'orderby'          => 'title',
			'order'            => 'ASC',
			'post_type'        => 'templatera',
			'post_status'      => 'publish',
			'posts_per_page'   => 100,
		);
		$jscomposer_templates = get_posts( $jscomposer_templates_args );

		if(count($jscomposer_templates) > 0) {
			foreach($jscomposer_templates as $jscomposer_template){
				if($jscomposer_template->post_title == $javenist_opt['header_layout'] || $jscomposer_template->post_title == $javenist_opt['footer_layout']){
					$jscomposer_template_css = get_post_meta ( $jscomposer_template->ID, '_wpb_shortcodes_custom_css', false );
					if(isset($jscomposer_template_css[0])) {
						wp_add_inline_style( 'javenist-css-custom', $jscomposer_template_css[0] );
					} 
				}
			}
		}
		
		//page width
		wp_add_inline_style( 'javenist-css-custom', '.wrapper.box-layout, .wrapper.box-layout .container, .wrapper.box-layout .row-container {max-width: '.$javenist_opt['box_layout_width'].'px;}' );
	}
	
	//add sharing code to header
	function javenist_custom_code_header() {
		global $javenist_opt;

		if ( isset($javenist_opt['share_head_code']) && $javenist_opt['share_head_code']!='') {
			echo wp_kses($javenist_opt['share_head_code'], array(
				'script' => array(
					'type' => array(),
					'src' => array(),
					'async' => array()
				),
			));
		}
	}
	/**
	 * Register sidebars.
	 *
	 * Registers our main widget area and the front page widget areas.
	 *
	 * @since Javenist 1.0
	 */
	function javenist_widgets_init() {
		$javenist_opt = get_option( 'javenist_opt' );
		
		register_sidebar( array(
			'name' => esc_html__( 'Blog Sidebar', 'javenist' ),
			'id' => 'sidebar-1',
			'description' => esc_html__( 'Sidebar on blog page', 'javenist' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</span></h3>',
		) );
		
		register_sidebar( array(
			'name' => esc_html__( 'Shop Sidebar', 'javenist' ),
			'id' => 'sidebar-shop', 
			'description' => esc_html__( 'Sidebar on shop page (only sidebar shop layout)', 'javenist' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</div></aside>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</span></h3><div class="widget-content">',
		) );

		register_sidebar( array(
			'name' => esc_html__( 'Detail Sidebar', 'javenist' ),
			'id' => 'sidebar-detail',
			'description' => esc_html__( 'Sidebar on detail page (only sidebar detail layout)', 'javenist' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</span></h3>',
		) );

		register_sidebar( array(
			'name' => esc_html__( 'Pages Sidebar', 'javenist' ),
			'id' => 'sidebar-page',
			'description' => esc_html__( 'Sidebar on content pages', 'javenist' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</span></h3>',
		) );
		
		if(isset($javenist_opt['custom-sidebars']) && $javenist_opt['custom-sidebars']!=""){
			foreach($javenist_opt['custom-sidebars'] as $sidebar){
				$sidebar_id = str_replace(' ', '-', strtolower($sidebar));
				
				if($sidebar_id!='') {
					register_sidebar( array(
						'name' => $sidebar,
						'id' => $sidebar_id,
						'description' => $sidebar,
						'before_widget' => '<aside id="%1$s" class="widget %2$s">',
						'after_widget' => '</aside>',
						'before_title' => '<h3 class="widget-title"><span>',
						'after_title' => '</span></h3>',
					) );
				}
			}
		}
	}
	static function javenist_meta_box_callback( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'javenist_meta_box', 'javenist_meta_box_nonce' );

		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		$value = get_post_meta( $post->ID, '_javenist_post_intro', true );

		echo '<label for="javenist_post_intro">';
		esc_html_e( 'This content will be used to replace the featured image, use shortcode here', 'javenist' );
		echo '</label><br />';
		wp_editor( $value, 'javenist_post_intro', $settings = array() );
	}
	static function javenist_custom_sidebar_callback( $post ) {
		global $wp_registered_sidebars;

		$javenist_opt = get_option( 'javenist_opt' );

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'javenist_meta_box', 'javenist_meta_box_nonce' );

		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */

		//show sidebar dropdown select
		$csidebar = get_post_meta( $post->ID, '_javenist_custom_sidebar', true );

		echo '<label for="javenist_custom_sidebar">';
		esc_html_e( 'Select a custom sidebar for this post/page', 'javenist' );
		echo '</label><br />';

		echo '<select id="javenist_custom_sidebar" name="javenist_custom_sidebar">';
			echo '<option value="">'.esc_html('- None -', 'javenist').'</option>';
			foreach($wp_registered_sidebars as $sidebar){
				$sidebar_id = $sidebar['id'];
				if($csidebar == $sidebar_id){
					echo '<option value="'.$sidebar_id.'" selected="selected">'.$sidebar['name'].'</option>';
				} else {
					echo '<option value="'.$sidebar_id.'">'.$sidebar['name'].'</option>';
				}
			}
		echo '</select><br />';

		//show custom sidebar position
		$csidebarpos = get_post_meta( $post->ID, '_javenist_custom_sidebar_pos', true );

		echo '<label for="javenist_custom_sidebar_pos">';
		esc_html_e( 'Sidebar position', 'javenist' );
		echo '</label><br />';

		echo '<select id="javenist_custom_sidebar_pos" name="javenist_custom_sidebar_pos">'; ?>
			<option value="left" <?php if($csidebarpos == 'left') {echo 'selected="selected"';}?>><?php echo esc_html('Left', 'javenist'); ?></option>
			<option value="right" <?php if($csidebarpos == 'right') {echo 'selected="selected"';}?>><?php echo esc_html('Right', 'javenist'); ?></option>
		<?php echo '</select>';
	}

	function javenist_add_meta_box() {

		$screens = array( 'post', 'page' );

		foreach ( $screens as $screen ) {
			if($screen == 'post'){
				add_meta_box(
					'javenist_post_intro_section',
					esc_html__( 'Post featured content', 'javenist' ),
					'Javenist_Class::javenist_meta_box_callback',
					$screen
				);
				add_meta_box(
					'javenist_custom_sidebar',
					esc_html__( 'Custom Sidebar', 'javenist' ),
					'Javenist_Class::javenist_custom_sidebar_callback',
					$screen
				);
			}
			if($screen == 'page'){
				add_meta_box(
					'javenist_custom_sidebar',
					esc_html__( 'Custom Sidebar', 'javenist' ),
					'Javenist_Class::javenist_custom_sidebar_callback',
					$screen
				);
			}
		}
	}
	function javenist_save_meta_box_data( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['javenist_meta_box_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['javenist_meta_box_nonce'], 'javenist_meta_box' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		/* OK, it's safe for us to save the data now. */
		
		// Make sure that it is set.
		if ( ! ( isset( $_POST['javenist_post_intro'] ) || isset( $_POST['javenist_custom_sidebar'] ) ) )  {
			return;
		}

		// Sanitize user input.
		$my_data = sanitize_text_field( $_POST['javenist_post_intro'] );
		// Update the meta field in the database.
		update_post_meta( $post_id, '_javenist_post_intro', $my_data );

		// Sanitize user input.
		$my_data = sanitize_text_field( $_POST['javenist_custom_sidebar'] );
		// Update the meta field in the database.
		update_post_meta( $post_id, '_javenist_custom_sidebar', $my_data );

		// Sanitize user input.
		$my_data = sanitize_text_field( $_POST['javenist_custom_sidebar_pos'] );
		// Update the meta field in the database.
		update_post_meta( $post_id, '_javenist_custom_sidebar_pos', $my_data );
		
	}
	//Change comment form
	function javenist_before_comment_fields() {
		echo '<div class="comment-input">';
	}
	function javenist_after_comment_fields() {
		echo '</div>';
	}
	/**
	 * Register postMessage support.
	 *
	 * Add postMessage support for site title and description for the Customizer.
	 *
	 * @since Javenist 1.0
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer object.
	 */
	function javenist_customize_register( $wp_customize ) {
		$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	}
	/**
	 * Enqueue Javascript postMessage handlers for the Customizer.
	 *
	 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
	 *
	 * @since Javenist 1.0
	 */
	function javenist_customize_preview_js() {
		wp_enqueue_script( 'javenist-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), '20130301', true );
	}
	// Remove Redux Ads, Notification
	function javenist_remove_redux_framework_notification() {
		if ( class_exists('ReduxFrameworkPlugin') ) {
			remove_filter( 'plugin_row_meta', array( ReduxFrameworkPlugin::get_instance(), 'plugin_metalinks'), null, 2 );
		}
		if ( class_exists('ReduxFrameworkPlugin') ) {
			remove_action('admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ) );    
		}
	}
	function javenist_admin_style() {
	  wp_enqueue_style('admin-styles', get_template_directory_uri().'/css/admin.css');
	}
	/**
	* Utility methods
	* ---------------
	*/
	
	//Add breadcrumbs
	static function javenist_breadcrumb() {
		global $post;

		$javenist_opt = get_option( 'javenist_opt' );
		
		$brseparator = '<span class="separator">></span>';
		if (!is_home()) {
			echo '<div class="breadcrumbs">';
			
			echo '<a href="';
			echo esc_url( home_url( '/' ));
			echo '">';
			echo esc_html__('Home', 'javenist');
			echo '</a>'.$brseparator;
			if (is_category() || is_single()) {
				$categories = get_the_category();
				if ( count( $categories ) > 0 ) {
					echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
				}
				if (is_single()) {
					if ( count( $categories ) > 0 ) { echo ''.$brseparator; }
					the_title();
				}
			} elseif (is_page()) {
				if($post->post_parent){
					$anc = get_post_ancestors( $post->ID );
					$title = get_the_title();
					foreach ( $anc as $ancestor ) {
						$output = '<a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a>'.$brseparator;
					}
					echo wp_kses($output, array(
							'a'=>array(
								'href' => array(),
								'title' => array()
							),
							'span'=>array(
								'class'=>array()
							)
						)
					);
					echo '<span title="'.$title.'"> '.$title.'</span>';
				} else {
					echo '<span> '.get_the_title().'</span>';
				}
			}
			elseif (is_tag()) {single_tag_title();}
			elseif (is_day()) {printf( esc_html__( 'Archive for: %s', 'javenist' ), '<span>' . get_the_date() . '</span>' );}
			elseif (is_month()) {printf( esc_html__( 'Archive for: %s', 'javenist' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'javenist' ) ) . '</span>' );}
			elseif (is_year()) {printf( esc_html__( 'Archive for: %s', 'javenist' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'javenist' ) ) . '</span>' );}
			elseif (is_author()) {echo "<span>".esc_html__('Archive for','javenist'); echo'</span>';}
			elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<span>".esc_html__('Blog Archives','javenist'); echo'</span>';}
			elseif (is_search()) {echo "<span>".esc_html__('Search Results','javenist'); echo'</span>';}
			
			echo '</div>';
		} else {
			echo '<div class="breadcrumbs">';
			
			echo '<a href="';
			echo esc_url( home_url( '/' ) );
			echo '">';
			echo esc_html__('Home', 'javenist');
			echo '</a>'.$brseparator;
			
			if(isset($javenist_opt['blog_header_text']) && $javenist_opt['blog_header_text']!=""){
				echo esc_html($javenist_opt['blog_header_text']);
			} else {
				echo esc_html__('Blog', 'javenist');
			}
			
			echo '</div>';
		}
	}
	static function javenist_limitStringByWord ($string, $maxlength, $suffix = '') {

		if(function_exists( 'mb_strlen' )) {
			// use multibyte functions by Iysov
			if(mb_strlen( $string )<=$maxlength) return $string;
			$string = mb_substr( $string, 0, $maxlength );
			$index = mb_strrpos( $string, ' ' );
			if($index === FALSE) {
				return $string;
			} else {
				return mb_substr( $string, 0, $index ).$suffix;
			}
		} else { // original code here
			if(strlen( $string )<=$maxlength) return $string;
			$string = substr( $string, 0, $maxlength );
			$index = strrpos( $string, ' ' );
			if($index === FALSE) {
				return $string;
			} else {
				return substr( $string, 0, $index ).$suffix;
			}
		}
	}
	static function javenist_excerpt_by_id($post, $length = 10, $tags = '<a><em><strong>') {
 
		if(is_int($post)) {
			// get the post object of the passed ID
			$post = get_post($post);
		} elseif(!is_object($post)) {
			return false;
		}
	 
		if(has_excerpt($post->ID)) {
			$the_excerpt = $post->post_excerpt;
			return apply_filters('the_content', $the_excerpt);
		} else {
			$the_excerpt = $post->post_content;
		}
	 
		$the_excerpt = strip_shortcodes(strip_tags($the_excerpt), $tags);
		$the_excerpt = preg_split('/\b/', $the_excerpt, $length * 2+1);
		$excerpt_waste = array_pop($the_excerpt);
		$the_excerpt = implode($the_excerpt);
	 
		return apply_filters('the_content', $the_excerpt);
	}
	/**
	 * Return the Google font stylesheet URL if available.
	 *
	 * The use of Open Sans by default is localized. For languages that use
	 * characters not supported by the font, the font can be disabled.
	 *
	 * @since Javenist 1.2
	 *
	 * @return string Font stylesheet or empty string if disabled.
	 */
	function javenist_get_font_url() {
		$fonts_url = '';
		 
		/* Translators: If there are characters in your language that are not
		* supported by Open Sans, translate this to 'off'. Do not translate
		* into your own language.
		*/
		$open_sans = _x( 'on', 'Open Sans font: on or off', 'javenist' );
		 
		if ( 'off' !== $open_sans ) {
			$font_families = array();

			if ( 'off' !== $open_sans ) {
				$font_families[] = 'Open Sans:700italic,400,800,600';
			}
			
			//default font
			if ( !class_exists('ReduxFrameworkPlugin') ) {
				
				$roboto = _x( 'on', 'Roboto font: on or off', 'javenist' );
				$lobster_two = _x( 'on', 'Lobster Two font: on or off', 'javenist' );
				$playfairdisplay = _x( 'on', 'PlayfairDisplay font: on or off', 'javenist' );
				
				if ( 'off' !== $roboto ) {
					$font_families[] = 'Roboto';
				}
				if ( 'off' !== $lobster_two ) {
					$font_families[] = 'Lobster Two';
				}
				if ( 'off' !== $playfairdisplay ) {
					$font_families[] = 'PlayfairDisplay';
				}
			}

			$query_args = array(
				'family' => urlencode( implode( '|', $font_families ) ),
				'subset' => urlencode( 'latin,latin-ext' ),
			);
			 
			$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		}
		 
		return esc_url_raw( $fonts_url );
	}
	/**
	 * Displays navigation to next/previous pages when applicable.
	 *
	 * @since Javenist 1.0
	 */
	static function javenist_content_nav( $html_id ) {
		global $wp_query;

		$html_id = esc_attr( $html_id );

		if ( $wp_query->max_num_pages > 1 ) : ?>
			<nav id="<?php echo esc_attr($html_id); ?>" class="navigation" role="navigation">
				<h3 class="assistive-text"><?php esc_html_e( 'Post navigation', 'javenist' ); ?></h3>
				<div class="nav-previous"><?php next_posts_link( wp_kses(__( '<span class="meta-nav">&larr;</span> Older posts', 'javenist' ),array('span'=>array('class'=>array())) )); ?></div>
				<div class="nav-next"><?php previous_posts_link( wp_kses(__( 'Newer posts <span class="meta-nav">&rarr;</span>', 'javenist' ), array('span'=>array('class'=>array())) )); ?></div>
			</nav>
		<?php endif;
	}
	/* Pagination */
	static function javenist_pagination() {
		global $wp_query;

		$big = 999999999; // need an unlikely integer
		if($wp_query->max_num_pages > 1) {
			echo '<div class="pagination-inner">';
		}
		
		echo paginate_links( array(
			'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format' => '?paged=%#%',
			'current' => max( 1, get_query_var('paged') ),
			'total' => $wp_query->max_num_pages,
			'prev_text'    => esc_html__('Previous', 'javenist'),
			'next_text'    =>esc_html__('Next', 'javenist'),
		) );
		if($wp_query->max_num_pages > 1) {
			echo '</div>';
		}
	}
	/**
	 * Template for comments and pingbacks.
	 *
	 * To override this walker in a child theme without modifying the comments template
	 * simply create your own javenist_comment(), and that function will be used instead.
	 *
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 *
	 * @since Javenist 1.0
	 */
	static function javenist_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
			// Display trackbacks differently than normal comments.
		?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
			<p><?php esc_html_e( 'Pingback:', 'javenist' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( '(Edit)', 'javenist' ), '<span class="edit-link">', '</span>' ); ?></p>
		<?php
				break;
			default :
			// Proceed with normal comments.
			global $post;
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<article id="comment-<?php comment_ID(); ?>" class="comment">
				<div class="comment-avatar">
					<?php echo get_avatar( $comment, 50 ); ?>
				</div>
				<div class="comment-info">
					<header class="comment-meta comment-author vcard">
						<?php
							
							printf( '<cite><b class="fn">%1$s</b> %2$s</cite>',
								get_comment_author_link(),
								// If current post author is also comment author, make it known visually.
								( $comment->user_id === $post->post_author ) ? '<span>' . esc_html__( 'Post author', 'javenist' ) . '</span>' : ''
							);
							printf( '<time datetime="%1$s">%2$s</time>',
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( esc_html__( '%1$s at %2$s', 'javenist' ), get_comment_date(), get_comment_time() )
							);
						?>
						<div class="reply">
							<?php comment_reply_link( array_merge( $args, array( 'reply_text' => esc_html__( 'Reply', 'javenist' ), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
						</div><!-- .reply -->
					</header><!-- .comment-meta -->
					<?php if ( '0' == $comment->comment_approved ) : ?>
						<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'javenist' ); ?></p>
					<?php endif; ?>

					<section class="comment-content comment">
						<?php comment_text(); ?>
						<?php edit_comment_link( esc_html__( 'Edit', 'javenist' ), '<p class="edit-link">', '</p>' ); ?>
					</section><!-- .comment-content -->
				</div>
			</article><!-- #comment-## -->
		<?php
			break;
		endswitch; // end comment_type check
	}
	/**
	 * Set up post entry meta.
	 *
	 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
	 *
	 * Create your own javenist_entry_meta() to override in a child theme.
	 *
	 * @since Javenist 1.0
	 */
	static function javenist_entry_meta() {
		
		// Translators: used between list items, there is a space after the comma.
		$tag_list = get_the_tag_list( '', ', ' );

		$num_comments = (int)get_comments_number();
		$write_comments = '';
		if ( comments_open() ) {
			if ( $num_comments == 0 ) {
				$comments = esc_html__('0 comments', 'javenist');
			} elseif ( $num_comments > 1 ) {
				$comments = $num_comments . esc_html__(' comments', 'javenist');
			} else {
				$comments = esc_html__('1 comment', 'javenist');
			}
			$write_comments = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
		}

		$utility_text = esc_html__( '%1$s ', 'javenist' );
			printf($utility_text, $write_comments );
		
		if($tag_list != '') { 
			$utility_text1 = esc_html__( 'Tags: %1$s', 'javenist' );
			printf( $utility_text1,  $tag_list);
		}
	}
	static function javenist_entry_meta_small() {
		
		// Translators: used between list items, there is a space after the comma.
		$categories_list = get_the_category_list(', ');

		$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( wp_kses(__( 'View all posts by %s', 'javenist' ), array('a'=>array())), get_the_author() ) ),
			get_the_author()
		);
		
		$utility_text = esc_html__( 'Posted by %1$s / %2$s', 'javenist' );

		printf( $utility_text, $author, $categories_list );
		
	}
	static function javenist_entry_comments() {
		
		$date = sprintf( '<time class="entry-date" datetime="%3$s">%4$s</time>',
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() )
		);

		$num_comments = (int)get_comments_number();
		$write_comments = '';
		if ( comments_open() ) {
			if ( $num_comments == 0 ) {
				$comments = wp_kses(__('<span>0</span> comments', 'javenist'), array('span'=>array()));
			} elseif ( $num_comments > 1 ) {
				$comments = '<span>'.$num_comments .'</span>'. esc_html__(' comments', 'javenist');
			} else {
				$comments = wp_kses(__('<span>1</span> comment', 'javenist'), array('span'=>array()));
			}
			$write_comments = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
		}
		
		$utility_text = esc_html__( '%1$s', 'javenist' );
		
		printf( $utility_text, $write_comments );
	}
	/**
	* TGM-Plugin-Activation
	*/
	function javenist_register_required_plugins() {

		$plugins = array(
			array(
				'name'               => 'Roadthemes Helper',
				'slug'               => 'roadthemes-helper',
				'source'             => get_template_directory() . '/plugins/roadthemes-helper.zip',
				'required'           => true,
				'version'            => '1.0.0',
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
			),
			array(
				'name'               => 'Mega Main Menu',
				'slug'               => 'mega_main_menu',
				'source'             => get_template_directory() . '/plugins/mega_main_menu.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => 'Revolution Slider',
				'slug'               => 'revslider',
				'source'             => get_template_directory() . '/plugins/revslider.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => 'Visual Composer',
				'slug'               => 'js_composer',
				'source'             => get_template_directory() . '/plugins/js_composer.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => 'Templatera',
				'slug'               => 'templatera',
				'source'             => get_template_directory() . '/plugins/templatera.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => 'Essential Grid',
				'slug'               => 'essential-grid',
				'source'             => get_template_directory() . '/plugins/essential-grid.zip',
				'required'           => true,
				'external_url'       => '',
			),
			// Plugins from the WordPress Plugin Repository.
			array(
				'name'               => 'Redux Framework',
				'slug'               => 'redux-framework',
				'required'           => true,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
			array(
				'name'      => 'Contact Form 7',
				'slug'      => 'contact-form-7',
				'required'  => true,
			),
			array(
				'name'      => 'Events Manager',
				'slug'      => 'events-manager',
				'required'  => false,
			),
			array(
				'name'      => 'Instagram Feed',
				'slug'      => 'instagram-feed',
				'required'  => false,
			),
			array(
				'name'      => 'Latest Tweets Widget',
				'slug'      => 'latest-tweets-widget',
				'required'  => false,
			),
			array(
				'name'      => 'WS Facebook Like Box Widget',
				'slug'      => 'ws-facebook-likebox',
				'required'  => false,
			),
			// array(
			// 	'name'      => 'MailPoet Newsletters',
			// 	'slug'      => 'wysija-newsletters',
			// 	'required'  => true,
			// ),
			array(
				'name'      => 'MailChimp for WordPress',
				'slug'      => 'mailchimp-for-wp',
				'required'  => true,
			),
			array(
				'name'      => 'Shortcodes Ultimate',
				'slug'      => 'shortcodes-ultimate',
				'required'  => true,
			),
			array(
				'name'      => 'Simple Local Avatars',
				'slug'      => 'simple-local-avatars',
				'required'  => false,
			),
			array(
				'name'      => 'Testimonials',
				'slug'      => 'testimonials-by-woothemes',
				'required'  => true,
			),
			array(
				'name'      => 'TinyMCE Advanced',
				'slug'      => 'tinymce-advanced',
				'required'  => false,
			),
			array(
				'name'      => 'Widget Importer & Exporter',
				'slug'      => 'widget-importer-exporter',
				'required'  => false,
			),
			array(
				'name'      => 'WooCommerce',
				'slug'      => 'woocommerce',
				'required'  => true,
			),
			array(
				'name'      => 'YITH WooCommerce Compare',
				'slug'      => 'yith-woocommerce-compare',
				'required'  => false,
			),
			array(
				'name'      => 'YITH WooCommerce Wishlist',
				'slug'      => 'yith-woocommerce-wishlist',
				'required'  => false,
			),
			array(
				'name'      => 'YITH WooCommerce Zoom Magnifier',
				'slug'      => 'yith-woocommerce-zoom-magnifier',
				'required'  => false,
			),
		);

		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'default_path' => '',                      // Default absolute path to pre-packaged plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
			'strings'      => array(
				'page_title'                      => esc_html__( 'Install Required Plugins', 'javenist' ),
				'menu_title'                      => esc_html__( 'Install Plugins', 'javenist' ),
				'installing'                      => esc_html__( 'Installing Plugin: %s', 'javenist' ), // %s = plugin name.
				'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'javenist' ),
				'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'javenist' ), // %1$s = plugin name(s).
				'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'javenist' ), // %1$s = plugin name(s).
				'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'javenist' ), // %1$s = plugin name(s).
				'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'javenist' ), // %1$s = plugin name(s).
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'javenist' ), // %1$s = plugin name(s).
				'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'javenist' ), // %1$s = plugin name(s).
				'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'javenist' ), // %1$s = plugin name(s).
				'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'javenist' ), // %1$s = plugin name(s).
				'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'javenist' ),
				'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'javenist' ),
				'return'                          => esc_html__( 'Return to Required Plugins Installer', 'javenist' ),
				'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'javenist' ),
				'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'javenist' ), // %s = dashboard link.
				'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
			)
		);

		tgmpa( $plugins, $config );

	}
}

// Instantiate theme
$Javenist_Class = new Javenist_Class();

//Fix duplicate id of mega menu
function javenist_mega_menu_id_change($params) {
	ob_start('javenist_mega_menu_id_change_call_back');
}
function javenist_mega_menu_id_change_call_back($html){
	$html = preg_replace('/id="mega_main_menu"/', 'id="mega_main_menu_first"', $html, 1);
	$html = preg_replace('/id="mega_main_menu_ul"/', 'id="mega_main_menu_ul_first"', $html, 1);
	
	return $html;
}
function total_product_count() {
    $args = array( 'post_type' => 'product', 'posts_per_page' => -1 );

    $products = new WP_Query( $args );

    return $products->found_posts;
}
function theme_prefix_enqueue_script() {
  	$script = '<!--[if lt IE 9]>
 	<script src="'.get_template_directory_uri().'/js/ie8.js" type="text/javascript"></script>
 	<![endif]-->';
 	$script .= '<script type="text/javascript">
 	var ajaxurl = "'.admin_url('admin-ajax.php').'";
	</script>';
 	echo $script; 
}
add_action( 'wp_enqueue_scripts', 'theme_prefix_enqueue_script' );
add_action('wp_loaded', 'javenist_mega_menu_id_change');