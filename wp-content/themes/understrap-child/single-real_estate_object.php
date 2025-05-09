<?php
/**
 * The template for displaying single real estate objects
 *
 * @package Understrap-child
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper" id="single-wrapper">
	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">
		<div class="row">
			<main class="site-main" id="main">
				<?php
				while ( have_posts() ) {
					the_post();

					// Get district and add to description
					$district_text = '';
					$districts = get_the_terms( get_the_ID(), 'district' );
					if ( $districts && ! is_wp_error( $districts ) ) {
						$district_text = $districts[0]->name;
					}

					// Gather building metadata
					$building_name = get_field('building_name');
					$floors = get_field('floors');
					$coordinates = get_field('coordinates');
					$eco_rating = get_field('eco_rating');

					// Get building type in Ukrainian
					$building_type = get_field('building_type');
					$building_type_label = '';
					if ($building_type) {
						switch ($building_type) {
							case 'panel':
								$building_type_label = 'Панель';
								break;
							case 'brick':
								$building_type_label = 'Цегла';
								break;
							case 'foam_block':
								$building_type_label = 'Піноблок';
								break;
						}
					}
					?>

					<article <?php post_class('real-estate-single'); ?> id="post-<?php the_ID(); ?>">
						<!-- Title and description section -->
						<div class="real-estate-header">
							<header class="entry-header">
								<?php the_title('<h1 class="entry-title">', '</h1>'); ?>
							</header>

							<!-- No need for description here as it appears below -->
						</div>

						<!-- Building image section - right after title -->
						<div class="building-image-section">
							<?php
							// First try featured image
							if (has_post_thumbnail()) {
								echo '<div class="building-featured-image">';
								the_post_thumbnail('large', array('class' => 'img-fluid property-image'));
								echo '</div>';
							}
							// Otherwise try ACF image field
							else if (function_exists('get_field') && ($building_image = get_field('building_image'))) {
								echo '<div class="building-acf-image">';
								if (is_array($building_image) && !empty($building_image['url'])) {
									echo '<img src="' . esc_url($building_image['url']) . '" alt="' . esc_attr($building_image['alt'] ?? '') . '" class="img-fluid property-image" />';
								} elseif (is_numeric($building_image)) {
									$image_url = wp_get_attachment_url($building_image);
									if ($image_url) {
										echo '<img src="' . esc_url($image_url) . '" alt="' . __('Зображення будівлі', 'understrap-child') . '" class="img-fluid property-image" />';
									}
								}
								echo '</div>';
							}
							?>
						</div>

						<!-- Building details section -->
						<div class="entry-content">
							<?php the_content(); ?>

							<div class="real-estate-details">
								<h2><?php _e('Інформація про об\'єкт нерухомості', 'understrap-child'); ?></h2>

								<div class="real-estate-meta">
									<table class="real-estate-info-table">
										<tr>
											<th><?php _e('Назва будинку:', 'understrap-child'); ?></th>
											<td><?php echo esc_html($building_name); ?></td>
										</tr>
										<tr>
											<th><?php _e('Координати:', 'understrap-child'); ?></th>
											<td><?php echo esc_html($coordinates); ?></td>
										</tr>
										<tr>
											<th><?php _e('Кількість поверхів:', 'understrap-child'); ?></th>
											<td><?php echo esc_html($floors); ?></td>
										</tr>
										<tr>
											<th><?php _e('Тип будівлі:', 'understrap-child'); ?></th>
											<td><?php echo esc_html($building_type_label); ?></td>
										</tr>
										<tr>
											<th><?php _e('Екологічність:', 'understrap-child'); ?></th>
											<td><?php echo esc_html($eco_rating); ?> / 5</td>
										</tr>
										<?php if ($districts && !is_wp_error($districts)) : ?>
										<tr>
											<th><?php _e('Район:', 'understrap-child'); ?></th>
											<td>
												<?php
												$district_links = array();
												foreach ($districts as $district) {
													$district_links[] = '<a href="' . esc_url(get_term_link($district)) . '">' . esc_html($district->name) . '</a>';
												}
												echo implode(', ', $district_links);
												?>
											</td>
										</tr>
										<?php endif; ?>
									</table>
								</div>

								<?php
								// Display premises
								$premises = get_field('premises');
								if ($premises) : ?>
									<div class="premises">
										<h3><?php _e('Приміщення', 'understrap-child'); ?></h3>

										<div class="premises-list">
											<?php $premise_count = 1; // Initialize counter ?>
											<?php foreach ($premises as $premise) : ?>
												<div class="premise-item">
													<h4><?php printf(__('Приміщення %d', 'understrap-child'), $premise_count); ?></h4>
													<?php $premise_count++; // Increment counter ?>

													<table class="premise-info-table">
														<?php if (isset($premise['area'])) : ?>
														<tr>
															<th><?php _e('Площа:', 'understrap-child'); ?></th>
															<td><?php echo esc_html($premise['area']); ?> м²</td>
														</tr>
														<?php endif; ?>

														<?php if (isset($premise['rooms'])) : ?>
														<tr>
															<th><?php _e('Кількість кімнат:', 'understrap-child'); ?></th>
															<td><?php echo esc_html($premise['rooms']); ?></td>
														</tr>
														<?php endif; ?>

														<?php if (isset($premise['balcony'])) : ?>
														<tr>
															<th><?php _e('Балкон:', 'understrap-child'); ?></th>
															<td><?php echo $premise['balcony'] === 'yes' ? __('Так', 'understrap-child') : __('Ні', 'understrap-child'); ?></td>
														</tr>
														<?php endif; ?>

														<?php if (isset($premise['bathroom'])) : ?>
														<tr>
															<th><?php _e('Санвузол:', 'understrap-child'); ?></th>
															<td><?php echo $premise['bathroom'] === 'yes' ? __('Так', 'understrap-child') : __('Ні', 'understrap-child'); ?></td>
														</tr>
														<?php endif; ?>
													</table>

													<?php
													// Display premise image if available
													if (isset($premise['image']) && $premise['image']) : ?>
														<div class="premise-image">
															<img src="<?php echo esc_url($premise['image']['url']); ?>" alt="<?php echo esc_attr($premise['image']['alt']); ?>" class="img-fluid" />
														</div>
													<?php endif; ?>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								<?php endif; ?>
							</div>

							<!-- Attribution for illustrations -->
							<div class="attribution">
								<a href="https://storyset.com/building" target="_blank" rel="noopener noreferrer">Building illustrations by Storyset</a>
							</div>
						</div><!-- .entry-content -->
					</article><!-- #post-## -->
					<?php
					// If comments are open or we have at least one comment, load up the comment template.
					if (comments_open() || get_comments_number()) {
						comments_template();
					}
				}
				?>
			</main><!-- #main -->

			<?php get_sidebar(); ?>
		</div><!-- .row -->
	</div><!-- #content -->
</div><!-- #single-wrapper -->

<?php
get_footer();
