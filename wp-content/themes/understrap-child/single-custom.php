<?php
/**
 * The template for displaying all single posts
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
					?>

					<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

						<header class="entry-header">
							<h1 class="entry-title"><?php the_title(); ?></h1>

							<div class="entry-meta">
								<?php understrap_posted_on(); ?>
							</div><!-- .entry-meta -->
						</header><!-- .entry-header -->

						<?php if ( has_post_thumbnail() ) : ?>
							<div class="entry-featured-image">
								<?php the_post_thumbnail( 'large' ); ?>
							</div>
						<?php endif; ?>

						<div class="entry-content">
							<?php the_content(); ?>
							<?php
							wp_link_pages(
								array(
									'before' => '<div class="page-links">' . __( 'Pages:', 'understrap-child' ),
									'after'  => '</div>',
								)
							);
							?>
						</div><!-- .entry-content -->

						<footer class="entry-footer">
							<?php understrap_entry_footer(); ?>
						</footer><!-- .entry-footer -->

					</article><!-- #post-## -->

					<?php
					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
					?>

					<?php
					// Navigation
					understrap_post_nav();
					?>

				<?php
				}
				?>

			</main><!-- #main -->

			<?php get_sidebar(); ?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #single-wrapper -->

<?php
get_footer();
