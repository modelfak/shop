<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Javenist_Theme
 * @since Javenist 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php $javenist_opt = get_option( 'javenist_opt' ); ?>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div class="wrapper <?php if($javenist_opt['page_layout']=='box'){echo 'box-layout';}?>">
	<div class="page-wrapper">
		<?php if(isset($javenist_opt['header_layout']) && $javenist_opt['header_layout']!=''){
			$header_class = str_replace(' ', '-', strtolower($javenist_opt['header_layout']));
		} else {
			$header_class = '';
		} 
		if(class_exists('RevSliderFront')){
			$hasSlider_class = 'rs-active';
		} else {
			$hasSlider_class = '';
		}
		?>
		<div class="header-container <?php echo esc_html($header_class)." ".esc_html($hasSlider_class) ?>">
			<div class="header"> 
				<div class="header-content">
					<?php
					if ( isset($javenist_opt['header_layout']) && $javenist_opt['header_layout']!="") {
						$jscomposer_templates_args = array(
							'orderby'          => 'title',
							'order'            => 'ASC',
							'post_type'        => 'templatera',
							'post_status'      => 'publish',
							'posts_per_page'   => 30,
						);
						$jscomposer_templates = get_posts( $jscomposer_templates_args );

						if(count($jscomposer_templates) > 0) {
							foreach($jscomposer_templates as $jscomposer_template){
								if($jscomposer_template->post_title == $javenist_opt['header_layout']){
									echo do_shortcode($jscomposer_template->post_content);
								}
							}
						}
					} else {
						?>  
						<div class="header-default">
							<div class="container">
								<div class="header-top">
									<?php if( isset($javenist_opt['logo_main']['url']) && $javenist_opt['logo_main']['url']!=''){ ?>
										<div class="logo"><div class="logo-inner"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><img src="<?php echo esc_url($javenist_opt['logo_main']['url']); ?>" alt="" /></a></div></div>
									<?php
									} else { ?>
										<h1 class="logo site-title"><span class="logo-inner"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span></h1>
										<?php
									} ?>
								</div>
								<div class="header-bottom"> 
									<div class="nav-container">
										<div class="horizontal-menu visible-large">
											<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container_class' => 'primary-menu-container', 'menu_class' => 'nav-menu' ) ); ?>
										</div>
									</div>  
								</div>  
									<div class="visible-small mobile-menu"> 
										<div class="mbmenu-toggler"><?php echo esc_html($javenist_opt['mobile_menu_label']);?><span class="mbmenu-icon"><i class="fa fa-bars"></i></span></div>
										<?php wp_nav_menu( array( 'theme_location' => 'mobilemenu', 'container_class' => 'mobile-menu-container', 'menu_class' => 'nav-menu' ) ); ?>
									</div>  
							</div>
						</div>  
						<?php
					} 
					?>
				</div> 
			</div>
			<div class="clearfix"></div>
		</div>