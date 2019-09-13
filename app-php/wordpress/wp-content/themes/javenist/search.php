<?php
/**
 * The template for displaying Search Results pages
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
		Javenist_Class::javenist_post_thumbnail_size('javenist-category-thumb');
		break;
	case 'largeimage':
		$javenist_blogclass = 'blog-large';
		$javenist_blogcolclass = 9;
		$javenist_postthumb = '';
		break;
	default:
		$javenist_blogclass = 'blog-nosidebar';
		$javenist_blogcolclass = 12;
		$javenist_blogsidebar = 'none';
		Javenist_Class::javenist_post_thumbnail_size('javenist-post-thumb');
}
?>
<div class="main-container">
	<div class="title-breadcrumb">
		<div class="container">
			<?php Javenist_Class::javenist_breadcrumb(); ?>
			<div class="title-breadcrumb-inner">
				<header class="entry-header">
					<h1 class="entry-title"><?php if(isset($javenist_opt)) { echo esc_html($javenist_opt['blog_header_text']); } else { esc_html_e('Blog', 'javenist');}  ?></h1>
				</header> 
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<?php if($javenist_blogsidebar=='left') : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>
			
			<div class="col-xs-12 <?php echo 'col-md-'.$javenist_blogcolclass; ?>">
			
				<div class="page-content blog-page <?php echo esc_attr($javenist_blogclass); if($javenist_blogsidebar=='left') {echo ' left-sidebar'; } if($javenist_blogsidebar=='right') {echo ' right-sidebar'; } ?>">
					<?php if ( have_posts() ) : ?>
						
						<header class="archive-header">
							<h1 class="archive-title"><?php printf( wp_kses(__( 'Search Results for: %s', 'javenist' ), array('span'=>array())), '<span>' . get_search_query() . '</span>' ); ?></h1>
						</header><!-- .archive-header -->

						<?php /* Start the Loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>
							<?php get_template_part( 'content', get_post_format() ); ?>
						<?php endwhile; ?>

						<div class="pagination">
							<?php Javenist_Class::javenist_pagination(); ?>
						</div>

					<?php else : ?>

						<article id="post-0" class="post no-results not-found">
							<header class="entry-header">
								<h1 class="entry-title"><?php esc_html_e( 'Nothing Found', 'javenist' ); ?></h1>
							</header>

							<div class="entry-content">
								<p><?php esc_html_e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'javenist' ); ?></p>
								<?php get_search_form(); ?>
							</div><!-- .entry-content -->
						</article><!-- #post-0 -->

					<?php endif; ?>
				</div>
			</div>
			<?php if( $javenist_blogsidebar=='right') : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>
		</div>
		
	</div>
</div>
<?php get_footer(); ?>