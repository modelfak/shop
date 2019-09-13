<?php
/**
 * Template Name: Full Width
 *
 * Description: Full Width page template
 *
 * @package WordPress
 * @subpackage Javenist_Theme
 * @since Javenist 1.0
 */
$javenist_opt = get_option( 'javenist_opt' );

get_header();
?>
<div class="main-container full-width">
	<div class="title-breadcrumb"> 
		<div class="container">
			<div class="title-breadcrumb-inner">
				<?php Javenist_Class::javenist_breadcrumb(); ?> 
				<header class="entry-header"> 
					<h1 class="entry-title"><?php the_title(); ?></h1> 
				</header>  
			</div>  
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
			<?php endwhile; ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>