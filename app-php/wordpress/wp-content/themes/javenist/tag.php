<?php
/**
 * The template for displaying Tag pages
 *
 * Used to display archive-type pages for posts in a tag.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
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
			
			<?php if($javenist_blogsidebar=='left') : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>
			
			<div class="col-xs-12 <?php echo 'col-md-'.$javenist_blogcolclass; ?>">
			
				<div class="page-content blog-page <?php echo esc_attr($javenist_blogclass); if($javenist_blogsidebar=='left') {echo ' left-sidebar'; } if($javenist_blogsidebar=='right') {echo ' right-sidebar'; } ?>">
					<?php if ( have_posts() ) : ?>
						<header class="archive-header">
							<h1 class="archive-title"><?php printf( wp_kses(__( 'Tag Archives: %s', 'javenist' ), array('span'=>array())), '<span>' . single_tag_title( '', false ) . '</span>' ); ?></h1>

						<?php if ( tag_description() ) : // Show an optional tag description ?>
							<div class="archive-meta"><?php echo tag_description(); ?></div>
						<?php endif; ?>
						</header><!-- .archive-header -->

						<?php
						/* Start the Loop */
						while ( have_posts() ) : the_post();

							/*
							 * Include the post format-specific template for the content. If you want to
							 * this in a child theme then include a file called called content-___.php
							 * (where ___ is the post format) and that will be used instead.
							 */
							get_template_part( 'content', get_post_format() );

						endwhile;
						?>
						
						<div class="pagination">
							<?php Javenist_Class::javenist_pagination(); ?>
						</div>
						
					<?php else : ?>
						<?php get_template_part( 'content', 'none' ); ?>
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