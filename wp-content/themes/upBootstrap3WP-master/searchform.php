<?php
/**
 * The template for displaying search forms in upBootWP
 *
 * @author Matthias Thom | http://upplex.de
 * @package upBootWP 0.1
 */
if( defined( 'SYMFONY_WP' ) ) {
    include( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'twig' . DIRECTORY_SEPARATOR . 'searchform.html.twig' );
} else {
?>
( SEARCH-FORM-PAGE )
<form role="search" method="get" class="search-form form-inline" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="form-group">
		<input type="search" class="search-field form-control" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'upbootwp' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php _ex( 'Search for:', 'label', 'upbootwp' ); ?>">
	</div>
	<input type="submit" class="search-submit btn btn-default" value="<?php echo esc_attr_x( 'Search', 'submit button', 'upbootwp' ); ?>">
</form>
<?php } ?>