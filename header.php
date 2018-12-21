<?php

$servername = "";
$username = "";
$password = "";
$dbname = "";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$user = wp_get_current_user()->ID;
$sql = "SELECT `meta_value`
FROM `wp_usermeta`
WHERE `user_id` = '$user AND (`meta_key` = 'enf1_prename' OR `meta_key` = 'prenom_2' OR `meta_key` = 'prenom_2_71' OR `meta_key` = 'prenom_2_71_86' OR `meta_value` = 'prenom_2_71_86')";
$result = $conn->query($sql);

$all_names = array ();
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      array_push($all_names, $row["meta_value"]);
    }
}

$conn->close();

$query = new WC_Product_Query( array(
    'category' => array('vacances'),
) );
$products = $query->get_products();

foreach ($all_names as $kid_name) {
  foreach ($products as $product) {
    // The variation data
    $variation_data =  array(
      'attributes' => array(
          'kids'  => $kid_name
      ),
      'sku'           => '',
      'regular_price' => '1M',
      'sale_price'    => '',
      'stock_qty'     => 20,
    );

    // check if variation exists
    $meta_query = array();
    foreach ($variation_data['attributes'] as $key => $value) {
    $meta_query[] = array(
      'key' => 'attribute_pa_' . $key,
      'value' => $value
      );
    }

    $variation_post = get_posts(array(
      'post_type' => 'product_variation',
      'numberposts' => 1,
      'post_parent'   => $parent_id,
      'meta_query' =>  $meta_query
      ));

    if($variation_post) {
      $variation_data['variation_id'] = $variation_post[0]->ID;
    }

    // The function to be run
    create_product_variation( $product->get_id(), $variation_data );
  };
};
?>
