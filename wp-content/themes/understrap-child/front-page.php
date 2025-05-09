<?php
/**
 * The front page template file
 *
 * This is the template for the site's front page
 *
 * @package Understrap-child
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper" id="front-page-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">
			<main class="site-main col-md-12" id="main">
				<header class="page-header">
					<h1 class="page-title">
						<?php _e('Останні новини', 'understrap-child'); ?>
					</h1>
				</header><!-- .page-header -->

				<?php
				// Display the latest posts
				$args = array(
					'post_type'      => 'post',
					'posts_per_page' => 5,
				);

				$query = new WP_Query( $args );

				if ( $query->have_posts() ) :
					?>
					<div class="latest-posts">
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							?>
							<article <?php post_class('mb-4'); ?> id="post-<?php the_ID(); ?>">
								<header class="entry-header">
									<h2 class="entry-title">
										<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
									</h2>

									<div class="entry-meta">
										<?php understrap_posted_on(); ?>
									</div><!-- .entry-meta -->
								</header><!-- .entry-header -->

								<div class="row">
									<?php if ( has_post_thumbnail() ) : ?>
										<div class="col-md-4">
											<a href="<?php the_permalink(); ?>">
												<?php the_post_thumbnail( 'medium', array('class' => 'img-fluid') ); ?>
											</a>
										</div>
										<div class="col-md-8">
									<?php else : ?>
										<div class="col-md-12">
									<?php endif; ?>
											<div class="entry-content">
												<?php the_excerpt(); ?>
												<a class="btn btn-primary btn-sm" href="<?php the_permalink(); ?>"><?php _e( 'Читати далі', 'understrap-child' ); ?></a>
											</div><!-- .entry-content -->
										</div>
								</div>
							</article><!-- #post-## -->
							<hr>
						<?php endwhile; ?>

						<div class="more-posts text-center mt-4 mb-4">
							<a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="btn btn-outline-primary"><?php _e('Переглянути всі новини', 'understrap-child'); ?></a>
						</div>
					</div>
					<?php
					// Reset postdata
					wp_reset_postdata();
				else :
					?>
					<p><?php _e( 'Публікацій не знайдено.', 'understrap-child' ); ?></p>
					<?php
				endif;
				?>
			</main><!-- #main -->
		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #front-page-wrapper -->

<?php
get_footer();
