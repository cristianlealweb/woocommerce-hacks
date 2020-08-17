// Cambiar direccion de la tienda
function wc_empty_cart_redirect_url() {
    return 'https://cristianleal.com.ar/';
}
add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url' );



// Cambiar dirección boton Seguir comprando
function wc_continue_shopping_redirect( $return_to ) {
    return 'https://cristianleal.com.ar/';
}
add_filter( 'woocommerce_continue_shopping_redirect', 'wc_continue_shopping_redirect', 20 );



// Cambiar texto añadir a carrito

function wc_custom_product_add_to_cart_tex() {
 global $product;
 
 $product_type = $product->product_type;
 
 switch ( $product_type ) {
 case 'external':
 return __( 'QUIERO!', 'woocommerce' );
 break;
 case 'grouped':
 return __( 'Ver productos', 'woocommerce' );
 break;
 case 'simple':
 return __( 'QUIERO!', 'woocommerce' );
 break;
 case 'variable':
 return __( 'QUIERO!', 'woocommerce' );
 break;
 default:
 return __( 'Leer más', 'woocommerce' );
 }
}
add_filter( 'woocommerce_product_add_to_cart_text' , 'wc_custom_product_add_to_cart_text' );




// Cambiar titulo a Pedido confirmado

function wc_title_order_received( $title, $id ) {
	if ( function_exists( 'is_order_received_page' ) && 
	     is_order_received_page() && get_the_ID() === $id ) {
		$title = '¡Pedido Recibido! El tiempo de demora es de 60 minutos aproximadamente!';
	}
	return $title;
}
add_filter( 'the_title', 'wc_title_order_received', 10, 2 );






// Mostrar precio mas bajo

function wc_variation_price_format( $price, $product ) {
    $prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
    $price = $prices[0] !== $prices[1] ? sprintf( __( 'Desde: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
    $prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
    sort( $prices );
    $saleprice = $prices[0] !== $prices[1] ? sprintf( __( 'Desde: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

    if ( $price !== $saleprice ) {
        $price = '<del>' . $saleprice . '</del> <ins>' . $price . '</ins>';
    }
    return $price;
}
add_filter( 'woocommerce_variable_sale_price_html', 'wc_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wc_variation_price_format', 10, 2 );


// Agregar nuevos Estados o Provincias


function wc_add_custom_states_to_country( $states ) {
    $states['AR'] = array(
        'SI' => __('San Isidro', 'woocommerce'),
        'VL' => __('Vicente López', 'woocommerce'),
        'SF' => __('San Fernando', 'woocommerce'),
        'TI' => __('Tigre', 'woocommerce'),
    );
    return $states;
}

add_filter('woocommerce_states', 'wc_add_custom_states_to_country');
add_filter('woocommerce_countries_allowed_country_states', 'wc_add_custom_states_to_country');
