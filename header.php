<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Preschool_and_Kindergarten_pro
 **
 *
     * Doctype Hook
     * 
     * @hooked preschool_and_kindergarten_pro_doctype_cb
    */
    do_action( 'preschool_and_kindergarten_pro_doctype' );
?>




<head itemscope itemtype="http://schema.org/WebSite">

<?php 
    /**
     * Before wp_head
     * 
     * @hooked preschool_and_kindergarten_pro_head
    */
    do_action( 'preschool_and_kindergarten_pro_before_wp_head' );

    wp_head(); 
?>
	<?php
$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "testdatabase";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT dept_name FROM departments";
$result = $conn->query($sql);

$all_names = array ();
if ($result->num_rows > 0)
{
    // output data of each row
    while($row = $result->fetch_assoc())
    {
        array_push($all_names, $row["dept_name"]);
    }
}

$conn->close();

$query = new WC_Product_Query(array('category' => array('holidays')));
$products = $query->get_products();

foreach ($all_names as $kid_name)
{
    foreach ($products as $product)
    {
        // The variation data
        $variation_data =  array('attributes' => array('kids'          => $kid_name),
                                                       'sku'           => '',
                                                       'regular_price' => '1.00',
                                                       'sale_price'    => '1.00',
                                                       'stock_qty'     => 1);

        // check if variation exists
        $meta_query = array();
        foreach ($variation_data['attributes'] as $key => $value)
        {
            $meta_query[] = array('key'   => 'attribute_pa_'.$key,
            'value' =>  str_replace(" ", "-", strtolower($value)));
        }

        $variation_post = get_posts(array('post_type'   => 'product_variation',
                                          'numberposts' => 1,
                                          'meta_query' =>  $meta_query));

        if($variation_post)
        {
            $variation_data['variation_id'] = $variation_post[0]->ID;
        }

        // The function to be run
        create_product_variation( $product->get_id(), $variation_data );
    };
};
?>
</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
		
		 <?php
         /*
         * @hooked preschool_and_kindergarten_pro_page_start 
         */
		 do_action( 'preschool_and_kindergarten_pro_before_page_start' ); 

		 /**
	     * preschool_and_kindergarten_pro Header Top
	     * 
	     * @hooked preschool_and_kindergarten_pro_header_start  - 10
	     * @hooked preschool_and_kindergarten_pro_header_top    - 20
	     * @hooked preschool_and_kindergarten_pro_header_bottom - 30
	     * @hooked preschool_and_kindergarten_pro_header_end    - 40    
	    */	    
		 
		 do_action( 'preschool_and_kindergarten_pro_header' ); 

		 /**
	     * slider
	     * 
	     * @hooked preschool_and_kindergarten_pro_slider - 20 
	    */
	    do_action( 'preschool_and_kindergarten_pro_slide' );
		 
		 /*
		 **
	     * After Header
	     * 
	     * @hooked preschool_and_kindergarten_pro_page_header 
	     *  
	     */

	     do_action( 'preschool_and_kindergarten_pro_after_header' );
