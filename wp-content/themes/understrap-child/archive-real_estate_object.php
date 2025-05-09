<?php
/**
 * The template for displaying archive pages of real estate objects
 *
 * @package Understrap-child
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper" id="archive-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<main class="site-main" id="main">

				<header class="page-header">
					<h1 class="page-title"><?php _e( 'Каталог об\'єктів нерухомості', 'understrap-child' ); ?></h1>
					<p class="lead"><?php _e( 'Перегляньте наші пропозиції або скористайтеся фільтром для пошуку за вашими параметрами', 'understrap-child' ); ?></p>
				</header><!-- .page-header -->

				<?php
				// Display the filter shortcode
				if ( shortcode_exists( 'real_estate_filter' ) ) {
					echo do_shortcode( '[real_estate_filter title="Фільтр об\'єктів нерухомості"]' );
				}
				?>

				<!-- The duplicate listing section has been removed to match the task requirements,
				     which specify that only the Ajax-filtered results should be shown -->

			</main><!-- #main -->

			<?php get_sidebar(); ?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #archive-wrapper -->

<?php
get_footer();
