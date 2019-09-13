<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Javenist_Theme
 * @since Javenist 1.0
 */

$javenist_opt = get_option( 'javenist_opt' );

get_header();

$javenist_bloglayout = 'nosidebar';
if(isset($javenist_opt['blog_layout']) && $javenist_opt['blog_layout']!=''){
	$javenist_bloglayout = $javenist_opt['blog_layout'];
}
if(isset($_GET['layout']) && $_GET['layout']!=''){
	$javenist_bloglayout = $_GET['layout'];
}
$javenist_blogsidebar = 'right';
if(isset($javenist_opt['sidebarblog_pos']) && $javenist_opt['sidebarblog_pos']!=''){
	$javenist_blogsidebar = $javenist_opt['sidebarblog_pos'];
}
if(isset($_GET['sidebar']) && $_GET['sidebar']!=''){
	$javenist_blogsidebar = $_GET['sidebar'];
}
switch($javenist_bloglayout) {
	case 'sidebar':
		$javenist_blogclass = 'blog-sidebar';
		$javenist_blogcolclass = 9;
		break;
	default:
		$javenist_blogclass = 'blog-nosidebar'; //for both fullwidth and no sidebar
		$javenist_blogcolclass = 12;
		$javenist_blogsidebar = 'none';
}
?>
<div class="main-container page-wrapper">
	<div class="container">
		<div class="title-breadcrumb"> 
			<div class="title-breadcrumb-inner">
				<?php Javenist_Class::javenist_breadcrumb(); ?>
				<header class="entry-header">
					<h1 class="entry-title"><?php if(isset($javenist_opt)) { echo esc_html($javenist_opt['blog_header_text']); } else { esc_html_e('Blog', 'javenist');}  ?></h1>
				</header> 
			</div>
		</div>
		
	</div>
	<div class="container">
		<div class="row">

			<?php
			$customsidebar = get_post_meta( $post->ID, '_javenist_custom_sidebar', true );
			$customsidebar_pos = get_post_meta( $post->ID, '_javenist_custom_sidebar_pos', true );

			if($customsidebar != ''){
				if($customsidebar_pos == 'left' && is_active_sidebar( $customsidebar ) ) {
					echo '<div id="secondary" class="col-xs-12 col-md-3">';
						dynamic_sidebar( $customsidebar );
					echo '</div>';
				} 
			} else {
				if($javenist_blogsidebar=='left') {
					get_sidebar();
				}
			} ?>
			
			<div class="col-xs-12 <?php echo 'col-md-'.$javenist_blogcolclass; ?>">
				<div class="page-content blog-page single <?php echo esc_attr($javenist_blogclass); if($javenist_blogsidebar=='left') {echo ' left-sidebar'; } if($javenist_blogsidebar=='right') {echo ' right-sidebar'; } ?>">
					<?php while ( have_posts() ) : the_post(); ?>

						<?php get_template_part( 'content', get_post_format() ); ?>

						<?php comments_template( '', true ); ?>
						
						<!--<nav class="nav-single">
							<h3 class="assistive-text"><?php esc_html_e( 'Post navigation', 'javenist' ); ?></h3>
							<span class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'javenist' ) . '</span> %title' ); ?></span>
							<span class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'javenist' ) . '</span>' ); ?></span>
						</nav><!-- .nav-single -->
						
					<?php endwhile; // end of the loop. ?>
				</div>
			</div>
			<?php
			if($customsidebar != ''){
				if($customsidebar_pos == 'right' && is_active_sidebar( $customsidebar ) ) {
					echo '<div id="secondary" class="col-xs-12 col-md-3">';
						dynamic_sidebar( $customsidebar );
					echo '</div>';
				} 
			} else {
				if($javenist_blogsidebar=='right') {
					get_sidebar();
				}
			} ?>
		</div>
	</div> 
</div>

<?php get_footer(); ?>