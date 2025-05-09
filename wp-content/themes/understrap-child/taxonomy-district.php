<?php
/**
 * The template for displaying district taxonomy pages
 *
 * @package Understrap-child
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper" id="taxonomy-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<main class="site-main" id="main">

				<header class="page-header">
					<?php
					$term = get_queried_object();
					?>
					<h1 class="page-title"><?php printf( __( 'Об\'єкти нерухомості у районі: %s', 'understrap-child' ), single_term_title( '', false ) ); ?></h1>

					<?php if ( $term->description ) : ?>
						<div class="taxonomy-description">
							<?php echo wp_kses_post( $term->description ); ?>
						</div>
					<?php endif; ?>
				</header><!-- .page-header -->

				<?php
				// Display the filter shortcode
				if ( shortcode_exists( 'real_estate_filter' ) ) {
					echo do_shortcode( '[real_estate_filter title="Фільтр об\'єктів нерухомості"]' );
				}
				?>

				<div class="real-estate-objects">
					<?php if ( have_posts() ) : ?>

						<div class="row">
							<?php
							// Start the loop.
							while ( have_posts() ) :
								the_post();
								?>
								<div class="col-md-6 col-lg-4 mb-4">
									<div class="card h-100">
										<?php if ( has_post_thumbnail() ) : ?>
											<a href="<?php the_permalink(); ?>">
												<?php the_post_thumbnail( 'medium', array( 'class' => 'card-img-top' ) ); ?>
											</a>
										<?php elseif ( function_exists( 'get_field' ) && $building_image = get_field( 'building_image' ) ) : ?>
											<a href="<?php the_permalink(); ?>">
												<img src="<?php echo esc_url( $building_image['url'] ); ?>" class="card-img-top" alt="<?php the_title_attribute(); ?>">
											</a>
										<?php endif; ?>

										<div class="card-body">
											<h2 class="card-title h5">
												<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
											</h2>

											<?php if ( function_exists( 'get_field' ) ) : ?>
												<div class="card-meta">
													<?php if ( $building_name = get_field( 'building_name' ) ) : ?>
														<p><strong><?php _e( 'Назва:', 'understrap-child' ); ?></strong> <?php echo esc_html( $building_name ); ?></p>
													<?php endif; ?>

													<?php if ( $floors = get_field( 'floors' ) ) : ?>
														<p><strong><?php _e( 'Поверхів:', 'understrap-child' ); ?></strong> <?php echo esc_html( $floors ); ?></p>
													<?php endif; ?>

													<?php
													$building_type = get_field( 'building_type' );
													if ( $building_type ) :
														$building_type_label = '';
														switch ( $building_type ) {
															case 'panel':
																$building_type_label = __( 'Панель', 'understrap-child' );
																break;
															case 'brick':
																$building_type_label = __( 'Цегла', 'understrap-child' );
																break;
															case 'foam_block':
																$building_type_label = __( 'Піноблок', 'understrap-child' );
																break;
														}
													?>
														<p><strong><?php _e( 'Тип:', 'understrap-child' ); ?></strong> <?php echo esc_html( $building_type_label ); ?></p>
													<?php endif; ?>

													<?php if ( $eco_rating = get_field( 'eco_rating' ) ) : ?>
														<p><strong><?php _e( 'Екологічність:', 'understrap-child' ); ?></strong> <?php echo esc_html( $eco_rating ); ?> / 5</p>
													<?php endif; ?>
												</div>
											<?php endif; ?>

											<div class="card-text">
												<?php the_excerpt(); ?>
											</div>
										</div>

										<div class="card-footer">
											<a href="<?php the_permalink(); ?>" class="btn btn-primary"><?php _e( 'Детальніше', 'understrap-child' ); ?></a>
										</div>
									</div>
								</div>
							<?php endwhile; ?>
						</div>

						<?php
						// Display pagination
						understrap_pagination();
						?>

					<?php else : ?>
						<p><?php _e( 'Об\'єктів нерухомості у цьому районі не знайдено.', 'understrap-child' ); ?></p>
					<?php endif; ?>
				</div>

			</main><!-- #main -->

			<?php get_sidebar(); ?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #taxonomy-wrapper -->

<?php
get_footer();
