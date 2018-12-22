<?php
/**
 * Preschool and Kindergarten functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Preschool_and_Kindergarten_pro
 */

//define theme version
if ( ! defined( 'PRESCHOOL_AND_KINDERGARTEN_PRO_THEME_VERSION' ) ) {
	$theme_data = wp_get_theme();	
	define ( 'PRESCHOOL_AND_KINDERGARTEN_PRO_THEME_VERSION', $theme_data->get( 'Version' ) );
}

/**
 * * Custom template function for this theme.
 */
require get_template_directory() . '/inc/custom-functions.php';

/**
 * Implement the WordPress Hooks.
 */
require get_template_directory() . '/inc/wp-hooks.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';
/**
 * Custom Controls 
*/
require get_template_directory() . '/inc/custom-controls/custom-control.php';

/**
 ** Custom template functions for this theme.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Implement the template hooks.
 */
require get_template_directory() . '/inc/template-hooks.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/widgets/widgets.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Demo content
*/
require get_template_directory() . '/inc/demo-content.php';

/**
 * Plugin Recommendation
*/
require get_template_directory() . '/inc/tgmpa/recommended-plugins.php';

/**
 * CPT
*/
require get_template_directory() . '/inc/cpt/cpt.php';

/**
 * Metabox
*/
require get_template_directory() . '/inc/cpt/metabox.php';

/**
 * WooCommerce Related funcitons
*/
if( preschool_and_kindergarten_pro_is_woocommerce_activated() )
require get_template_directory() . '/inc/woocommerce-functions.php';

/**
 * Typography Functions
*/
require get_template_directory() . '/inc/typography-functions.php';

/**
 * Dynamic Styles
*/
require get_template_directory() . '/css/style.php';

/**
 * Theme Updater
*/
require get_template_directory() . '/updater/theme-updater.php';

/**
 * Demo Import
*/
require get_template_directory() . '/inc/import-hooks.php';

function my_theme_remove_plugins_actions() {
remove_action( 'woocommerce_order_status_completed', 'bookacti_turn_temporary_booking_to_permanent', 5 );
}
add_action( 'plugins_loaded', 'my_theme_remove_plugins_actions' );

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

add_filter( 'woocommerce_get_price_html', function( $price ) {
	if ( is_admin() ) return $price;

	return '';
} );

// Remove image from product pages
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

// Remove sale badge from product page
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

// Remove product thumbnail from the cart page
add_filter( 'woocommerce_cart_item_thumbnail', '__return_empty_string' );

/**
 * Défini le status de réservation "En attente" comme non actif (n'occupe plus une place réservée)
 * @param array $active_states
 * @return array
 */
function my_theme_active_booking_states( $active_states ) {
foreach( $active_states as $i => $active_state ) {
if( $active_state === 'pending' ) { unset( $active_states[ $i ] ); break; }
}
return $active_states;
}
add_filter( 'bookacti_active_booking_states', 'my_theme_active_booking_states', 100, 1 );

add_filter ( 'woocommerce_account_menu_items', 'misha_remove_my_account_links' );
function misha_remove_my_account_links( $menu_links ){
 
	unset( $menu_links['edit-address'] ); // Addresses
 
 
	unset( $menu_links['dashboard'] ); // Dashboard
	//unset( $menu_links['payment-methods'] ); // Payment Methods
	//unset( $menu_links['orders'] ); // Orders
	unset( $menu_links['downloads'] ); // Downloads
	//unset( $menu_links['edit-account'] ); // Account details
	unset( $menu_links['customer-logout'] ); // Logout
 
	return $menu_links;
 
}

function create_product_variation( $product_id, $variation_data ){
		if(isset($variation_data['variation_id'])) {
			$variation_id = $variation_data['variation_id'];
		} else {
			// If the variation does not exist create interface
			// Get the Variable product object (parent)
	    $product = wc_get_product($product_id);
	    $variation_post = array(
	        'post_title'  => $product->get_title(),
	        'post_name'   => 'product-'.$product_id.'-variation',
	        'post_status' => 'publish',
	        'post_parent' => $product_id,
	        'post_type'   => 'product_variation',
	        'guid'        => $product->get_permalink()
	    );
	    // Creating the product variation
	    $variation_id = wp_insert_post( $variation_post );
	    // Get an instance of the WC_Product_Variation object
	    $variation = new WC_Product_Variation( $variation_id );
	    // Iterating through the variations attributes
	    foreach ($variation_data['attributes'] as $attribute => $term_name )
	    {
	        $taxonomy = 'pa_'.$attribute; // The attribute taxonomy
	        // If taxonomy doesn't exist we create it 
	        if( ! taxonomy_exists( $taxonomy ) ){
	            register_taxonomy(
	                $taxonomy,
	               'product_variation',
	                array(
	                    'hierarchical' => false,
	                    'label' => ucfirst( $taxonomy ),
	                    'query_var' => true,
	                    'rewrite' => array( 'slug' => '$taxonomy'), // The base slug
	                ),
	            );
	        }
	        // Check if the Term name exists and if not we create it.
	        if( ! term_exists( $term_name, $taxonomy ) )
	            wp_insert_term( $term_name, $taxonomy ); // Create the term
	        $term_slug = get_term_by('name', $term_name, $taxonomy )->slug; // Get the term slug
	        // Get the post Terms names from the parent variable product.
	        $post_term_names =  wp_get_post_terms( $product_id, $taxonomy, array('fields' => 'names') );
	        // Check if the post term exist and if not we set it in the parent variable product.
	        if( ! in_array( $term_name, $post_term_names ) )
	            wp_set_post_terms( $product_id, $term_name, $taxonomy, true );
	        // Set/save the attribute data in the product variation
	        update_post_meta( $variation_id, 'attribute_'.$taxonomy, $term_slug );
	    }
	    ## Set/save all other data
	    // SKU
	    if( ! empty( $variation_data['sku'] ) )
	        $variation->set_sku( $variation_data['sku'] );
	    // Prices
	    if( empty( $variation_data['sale_price'] ) ){
	        $variation->set_price( $variation_data['regular_price'] );
	    } else {
	        $variation->set_price( $variation_data['sale_price'] );
	        $variation->set_sale_price( $variation_data['sale_price'] );
	    }
	    $variation->set_regular_price( $variation_data['regular_price'] );
	    // Stock
	    if( ! empty($variation_data['stock_qty']) ){
	        $variation->set_stock_quantity( $variation_data['stock_qty'] );
	        $variation->set_manage_stock(true);
	        $variation->set_stock_status('');
	    } else {
	        $variation->set_manage_stock(false);
	    }
	    $variation->set_weight(''); // weight (reseting)
	    $variation->save(); // Save the data
		}
}
?>
