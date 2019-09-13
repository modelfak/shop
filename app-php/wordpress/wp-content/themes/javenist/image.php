<?php
/**
 * The template for displaying image attachments
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
			
				<div class="page-content blog-page single <?php echo esc_attr($javenist_blogclass); if($javenist_blogsidebar=='left') {echo ' left-sidebar'; } if($javenist_blogsidebar=='right') {echo ' right-sidebar'; } ?>">
					<?php while ( have_posts() ) : the_post(); ?>

						<article id="post-<?php the_ID(); ?>" <?php post_class( 'image-attachment' ); ?>>
							<div class="entry-content">
								<div class="post-thumbnail">
									<div class="entry-attachment">
										<div class="attachment">
											<?php
											/*
											 * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
											 * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
											 */
											$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
											foreach ( $attachments as $k => $attachment ) :
												if ( $attachment->ID == $post->ID )
													break;
											endforeach;

											$k++;
											// If there is more than 1 attachment in a gallery
											if ( count( $attachments ) > 1 ) :
												if ( isset( $attachments[ $k ] ) ) :
													// get the URL of the next image attachment
													$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
												else :
													// or get the URL of the first image attachment
													$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
												endif;
											else :
												// or, if there's only 1 image, get the URL of the image
												$next_attachment_url = wp_get_attachment_url();
											endif;
											?>
											<a href="<?php echo esc_url( $next_attachment_url ); ?>" title="<?php the_title_attribute(); ?>" rel="attachment"><?php
											/**
											 * Filter the image attachment size to use.
											 *
											 * @since Javenist 1.0
											 *
											 * @param array $size {
											 *     @type int The attachment height in pixels.
											 *     @type int The attachment width in pixels.
											 * }
											 */
											$attachment_size = apply_filters( 'javenist_attachment_size', array( 960, 960 ) );
											echo wp_get_attachment_image( $post->ID, $attachment_size );
											?></a>

											<?php if ( ! empty( $post->post_excerpt ) ) : ?>
											<div class="entry-caption">
												<?php the_excerpt(); ?>
											</div>
											<?php endif; ?>
										</div><!-- .attachment -->

									</div><!-- .entry-attachment -->
								</div>
								<div class="entry-description">
									<?php the_content(); ?>
									<?php wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'javenist' ), 'after' => '</div>' ) ); ?>
								</div><!-- .entry-description -->

							</div><!-- .entry-content -->
							<div class="postinfo-wrapper">
								<div class="post-date">
									<?php echo '<span class="day">'.get_the_date('d', $post->ID).'</span><span class="month"><span class="separator">/</span>'.get_the_date('M', $post->ID).'</span>' ;?>
								</div>
								<div class="post-info">
									<header class="entry-header">
										<h1 class="entry-title"><?php the_title(); ?></h1>
									</header><!-- .entry-header -->
									
									<footer class="entry-meta">
										<?php
											$metadata = wp_get_attachment_metadata();
											printf( wp_kses(__( '<span class="meta-prep meta-prep-entry-date">Published </span> <span class="entry-date"><time class="entry-date" datetime="%1$s">%2$s</time></span> at <a href="%3$s" title="Link to full-size image">%4$s &times; %5$s</a> in <a href="%6$s" title="Return to %7$s" rel="gallery">%8$s</a>.', 'javenist' ), array('span'=>array('class'=>array()), 'a'=>array('href'=>array(), 'title'=>array(), 'rel'=>array()), 'time'=>array('class'=>array(), 'datetime'=>array()))),
												esc_attr( get_the_date( 'c' ) ),
												esc_html( get_the_date() ),
												esc_url( wp_get_attachment_url() ),
												$metadata['width'],
												$metadata['height'],
												esc_url( get_permalink( $post->post_parent ) ),
												esc_attr( strip_tags( get_the_title( $post->post_parent ) ) ),
												get_the_title( $post->post_parent )
											);
										?>
										<?php edit_post_link( esc_html__( 'Edit', 'javenist' ), '<span class="edit-link">', '</span>' ); ?>
									</footer><!-- .entry-meta -->
								</div>
							</div>
							
						</article><!-- #post -->

						<?php comments_template(); ?>
						
						<!--<nav id="image-navigation" class="navigation nav-single" role="navigation">
							<span class="previous-image nav-previous"><?php previous_image_link( false, esc_html__( '&larr; Previous', 'javenist' ) ); ?></span>
							<span class="next-image nav-next"><?php next_image_link( false, esc_html__( 'Next &rarr;', 'javenist' ) ); ?></span>
						</nav> #image-navigation -->
						
					<?php endwhile; // end of the loop. ?>
				</div>
			</div>
			<?php if( $javenist_blogsidebar=='right') : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>
		</div>
		
	</div>
</div>
<?php get_footer(); ?>