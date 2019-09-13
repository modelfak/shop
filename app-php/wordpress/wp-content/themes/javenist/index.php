<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Javenist_Theme
 * @since Javenist 1.0
 */

$javenist_opt = get_option( 'javenist_opt' );

get_header();

$javenist_bloglayout = 'sidebar';

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
		Javenist_Class::javenist_post_thumbnail_size('javenist-category-thumb');
		break;
	case 'grid':
		$javenist_blogclass = 'grid';
		$javenist_blogcolclass = 9;
		Javenist_Class::javenist_post_thumbnail_size('javenist-category-thumb');
		break;
	default:
		$javenist_blogclass = 'blog-nosidebar';
		$javenist_blogcolclass = 12;
		$javenist_blogsidebar = 'none';
		Javenist_Class::javenist_post_thumbnail_size('javenist-post-thumb');
}
?>

<div class="main-container"> 
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

						<?php /* Start the Loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>
							
							<?php get_template_part( 'content', get_post_format() ); ?>
							
						<?php endwhile; ?>

						<div class="pagination">
							<?php Javenist_Class::javenist_pagination(); ?>
						</div>
						
					<?php else : ?>

						<article id="post-0" class="post no-results not-found">

						<?php if ( current_user_can( 'edit_posts' ) ) :
							// Show a different message to a logged-in user who can add posts.
						?>
							<header class="entry-header">
								<h1 class="entry-title"><?php esc_html_e( 'No posts to display', 'javenist' ); ?></h1>
							</header>

							<div class="entry-content">
								<p><?php printf( wp_kses(__( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'javenist' ), array('a'=>array('href'=>array()))), admin_url( 'post-new.php' ) ); ?></p>
							</div><!-- .entry-content -->

						<?php else :
							// Show the default message to everyone else.
						?>
							<header class="entry-header">
								<h1 class="entry-title"><?php esc_html_e( 'Nothing Found', 'javenist' ); ?></h1>
							</header>

							<div class="entry-content">
								<p><?php esc_html_e( 'Apologies, but no results were found. Perhaps searching will help find a related post.', 'javenist' ); ?></p>
								<?php get_search_form(); ?>
							</div><!-- .entry-content -->
						<?php endif; // end current_user_can() check ?>

						</article><!-- #post-0 -->

					<?php endif; // end have_posts() check ?>
				</div>
				
			</div>
			<?php if( $javenist_blogsidebar=='right') : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>
		</div>
	</div> 
</div>
<?php get_footer(); ?>