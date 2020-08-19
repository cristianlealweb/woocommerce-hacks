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



// Actualiza automáticamente el estado de los pedidos a COMPLETADO

function wc_actualiza_estado_pedidos_a_completado( $order_id ) {
    global $woocommerce;
    
    //ID's de las pasarelas de pago a las que afecta
    $paymentMethods = array( 'woo-mercado-pago-basic', 'paypal' );
    
    if ( !$order_id ) return;
    $order = new WC_Order( $order_id );

    if ( !in_array( $order->payment_method, $paymentMethods ) ) return;
    $order->update_status( 'completed' );
}
add_action( 'woocommerce_order_status_processing', 'wc_actualiza_estado_pedidos_a_completado' );
add_action( 'woocommerce_order_status_on-hold', 'wc_actualiza_estado_pedidos_a_completado' );


// Establecer un importe minimo en la compra
function wc_importe_minimo() {
  $minimum = 500;  // Debes cambiar el 20 por el importe mínimo que quieras establecer en tu pedido
  if ( WC()->cart->total < $minimum ) {
    if( is_cart() ) {
      wc_print_notice(
      sprintf( 'Debes realizar un pedido mínimo de %s para finalizar su compra.' ,
        wc_price( $minimum ),
        wc_price( WC()->cart->total )
      ), 'error'
      );
    } else {
      wc_add_notice(
      sprintf( 'No puedes finalizar tu compra. Debes realizar un pedido mínimo de %s para finalizar su compra.' , 
        wc_price( $minimum ), 
        wc_price( WC()->cart->total )
      ), 'error'
      );
    }
  }
}
add_action( 'woocommerce_checkout_process', 'wc_importe_minimo' );
add_action( 'woocommerce_before_cart' , 'wc_importe_minimo' );

// Ocultar tipo de pagos por moneda
// Para usar con Plugin https://es.wordpress.org/plugins/woocommerce-currency-switcher/

function wc_filter_gateways($gateway_list){
    global $WOOCS;
    $exclude = array(
        'paypal' => array('ARS'), //No mostrar PayPal en Pesos Argentinos
        'woo-mercado-pago-basic' => array('USD')//No mostrar Mercado Pago en Dólares
    );
    foreach ($exclude as $gateway_key => $currencies){
        if (isset($gateway_list[$gateway_key]) AND in_array($WOOCS->current_currency, $currencies)){
            unset($gateway_list[$gateway_key]);
        }
    }
    return $gateway_list;
}
add_filter('woocommerce_available_payment_gateways', 'wc_filter_gateways', 1);
