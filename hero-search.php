<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage FAU
 * @since FAU 1.0
 */

global $options;
?>

<section id="hero" class="hero-small">
	<div class="container">
		<div class="row">
			<div class="span8">
			    <?php fau_breadcrumb($options['title_hero_search']); ?>
			 
			</div>
		</div>
		<div class="row">
			<div class="span3">
				<h1><?php echo $options['title_hero_search']; ?></h1>
			</div>
			<div class="span9">
				<?php /* get_search_form(); */ ?>
				
				<form role="search" method="get" class="searchform" action="<?php echo home_url( '/' )?>">
					<input type="text" value="<?php the_search_query(); ?>" name="s" placeholder="<?php _e('Suchen nach...','fau'); ?>">
					<input type="submit" id="searchsubmit" value="<?php _e('Finden','fau'); ?>">
				</form>
				
			</div>
		</div>
	</div>
</section>
