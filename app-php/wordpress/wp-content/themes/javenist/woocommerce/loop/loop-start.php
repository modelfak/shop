<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     30.3.0
 */
global $wp_query, $woocommerce_loop;

$javenist_opt = get_option( 'javenist_opt' );

$shoplayout = 'sidebar';
if(isset($javenist_opt['shop_layout']) && $javenist_opt['shop_layout']!=''){
	$shoplayout = $javenist_opt['shop_layout'];
}
if(isset($_GET['layout']) && $_GET['layout']!=''){
	$shoplayout = $_GET['layout'];
}
$shopsidebar = 'left';
if(isset($javenist_opt['sidebarshop_pos']) && $javenist_opt['sidebarshop_pos']!=''){
	$shopsidebar = $javenist_opt['sidebarshop_pos'];
}
if(isset($_GET['sidebar']) && $_GET['sidebar']!=''){
	$shopsidebar = $_GET['sidebar'];
}
switch($shoplayout) {
	case 'fullwidth':
		Javenist_Class::javenist_shop_class('shop-fullwidth');
		$shopcolclass = 12;
		$shopsidebar = 'none';
		$productcols = 4;
		break;
	default:
		Javenist_Class::javenist_shop_class('shop-sidebar');
		$shopcolclass = 9;
		$productcols = 3;
}
$javenist_viewmode = Javenist_Class::javenist_show_view_mode();
?> 

<div class="shop-products products <?php echo esc_attr($javenist_viewmode);?> <?php echo esc_attr($shoplayout);?>">