<?php

namespace tonysong\bulk_price\price_display;

use tonysong\bulk_price\data\Bulk_Data;

class Price_display {
	private $cart_item = array();

	function __construct() {
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'set_item_subtotal' ), 10, 3 );
		add_filter( 'woocommerce_cart_subtotal', array( $this, 'filter_subtotal' ), 10, 3 );
		add_action( 'woocommerce_after_calculate_totals', array( $this, 'recalc_grandtotal' ) );
		add_action('woocommerce_single_product_summary', array($this, 'display_bulk_price_note'),15);
	}

	function display_bulk_price_note(){
		global $post;
		$bulk_enable = get_post_meta($post->ID, '_bulk_enable', true);
		if($bulk_enable){
			echo wpautop(get_post_meta($post->ID, '_bulk_description', true));
		}
	}

	function get_bulk_meta( $product_id, $qty ) {
		$product   = wc_get_product( $product_id );
		$bulk_data = new Bulk_Data( $product_id, 'desc' );
		$in_scope  = false;
		$minqty    = 0;
		$percent   = 0;
		$fixed     = 0;
		$flat      = 0;

		$bulk_enable = get_post_meta( $product_id, '_bulk_enable', true );
		if ( empty( $bulk_enable ) ) {
			$bulk_enable = 0;
		}
		$bulk_type = get_post_meta( $product_id, '_bulk_type', true );
		if ( empty( $bulk_type ) ) {
			$bulk_type = 0;
		}
		foreach ( $bulk_data->bulk_values as $value ) {
			if ( $qty >= $value->bulk_minqty ) {
				$in_scope = true;
				$minqty   = $value->bulk_minqty;
				$percent  = $value->bulk_percent;
				$fixed    = $value->bulk_fixed;
				$flat     = $value->bulk_flat;
			}
		}

		if ( $bulk_enable > 0 ) {
			$in_scope = false;
		}

		return array(
			'product_type' => $product->get_type(),
			'in_scope'     => $in_scope,
			'bulk_type'    => $bulk_type,
			'minqty'       => $minqty,
			'percent'      => $percent,
			'fixed'        => $fixed,
			'flat'         => $flat
		);

	}

	function get_discount( $key, $cart_item ) {

		/**
		 * @var \WC_Product_Simple $product
		 */
		$product = $cart_item['data'];
		$qty = $cart_item['quantity'];
		$this->cart_item[ $key ]['bulk_meta'] = $this->get_bulk_meta( $cart_item['product_id'], $qty );
		$bulk_meta = $this->cart_item[ $key ]['bulk_meta'];
		$percent   = $bulk_meta['percent'];
		$fixed     = $bulk_meta['fixed'];
		$flat      = $bulk_meta['flat'];
		if ( $bulk_meta['in_scope'] ) {
			$price = $product->get_regular_price();
			if ( empty( $price ) ) {
				$price = $product->get_price();
			}
			switch ( $bulk_meta['bulk_type'] ) {
				case '0':
					$subtotal = $qty * ( $price - ( $price * ( $percent / 100 ) ) );
					break;
				case '1':
					$subtotal = $qty * ( $price - $fixed );
					break;
				case '2':
					$subtotal = ( $qty * $price ) - $flat;
					break;
				default:
					$subtotal = $qty * $product->get_price();
			}
		} else {
			$subtotal = $qty * $product->get_price();
		}

		$this->cart_item[ $key ]['subtotal'] = $subtotal;
	}


	function set_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {
		/**
		 * @var \WC_Product_Simple $product
		 *
		 */
		$product_id = $cart_item['product_id'];
		$quantity   = $cart_item['quantity'];
		$this->get_discount( $cart_item_key, $cart_item);
		$bulk_meta = $this->cart_item[ $cart_item_key ]['bulk_meta'];

		if ( $bulk_meta['in_scope'] ) {
			$subtotal          = $this->cart_item[ $cart_item_key ]['subtotal'];
			$product           = $cart_item['data'];
			$original_subtotal = $product->get_regular_price();
			if ( empty( $original_subtotal ) ) {
				$original_subtotal = $product->get_price();
			}
			$original_subtotal = $original_subtotal * $quantity;
			$style = get_option( 'bulk_price_style', 0 );
			$color = get_option( 'bulk_price_color', '#FF0000' );
			if ( $bulk_meta['bulk_type'] == '0' ) {
				if($style==0){
					return '<span style="text-decoration: line-through;">' . wc_price( $original_subtotal ) . '</span> <span style="color: ' . $color . '"><strong>' . wc_price( $subtotal ) . '&nbsp;(' . $bulk_meta['percent'] . '% off)</strong></span>';
				}
				else{
					return '<span style="color: ' . $color . '"><strong>' . wc_price( $subtotal ) . '</strong></span>';
				}


			} else {
				if($style==0){
					return '<span style="text-decoration: line-through;"><strong>' . wc_price( $original_subtotal ) . '</span> <span style="color: ' . $color . '">' . wc_price( $subtotal ) . '</strong></span>';
				}
				else{
					return '<span style="color: ' . $color . '"><strong>' . wc_price( $subtotal ) . '</strong></span>';
				}

			}

		} else {
			return $subtotal;
		}

//		$total = 0;
//		foreach ( $cart_object->get_cart() as $hash => $value ) {
//			$product_data           = $value['data'];
//			$price                  = $this->get_discount( $value['product_id'], $value['quantity'], $product_data->get_price() );
//			$total                  = $price;
//		}

	}


	function filter_subtotal( $cart_subtotal, $compound, $context ) {
		$grandtotal = 0;
		foreach ($this->cart_item as $cart_item){
			$grandtotal += $cart_item['subtotal'];
		}
		return wc_price($grandtotal);
	}

	/**
	 * @var \WC_Cart $cart_object
	 */
	function recalc_grandtotal( \WC_Cart $cart_object ) {
		/**
		 * @var \WC_Product_Simple $product_data
		 */
		$total = 0;
		foreach ( $cart_object->get_cart() as $hash => $value ) {
			$this->get_discount( $hash, $value );
			$subtotal     = $this->cart_item[ $hash ]['subtotal'];
			$total                  += $subtotal;
			$value['line_subtotal'] = $total;
		}
		$cart_object->set_total( $total );
	}


}

return new Price_display();