<?php


/**
 * Different actions on buying process.
 *
 * Class WB_Process
 */
class WB_Process {
	/**
	 * WB_Process constructor.
	 */
	public function __construct() {

		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ) );
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );

		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_order_item_data' ), 10, 3 );

		add_action( 'woocommerce_order_status_changed', array( $this, 'change_bought' ), 10, 3 );
	}

	/**
	 * Add list data to cart.
	 *
	 * @param $cart_item_data
	 *
	 * @return array
	 */
	public function add_cart_item_data( $cart_item_data ) {
		if ( isset( $_REQUEST['list'] ) && ! empty( $_REQUEST['list'] ) ) {
			$cart_item_data['_wb_list'] = $_REQUEST['list'];

			if ( isset( $_REQUEST['uid'] ) && ! empty( $_REQUEST['uid'] ) ) {
				$cart_item_data['_wb_uid'] = $_REQUEST['uid'];
			}
		}

		return $cart_item_data;
	}

	/**
	 * Display list data on cart page.
	 *
	 * @param $item_data
	 * @param $cart_item
	 *
	 * @return array
	 */
	public function get_item_data( $item_data, $cart_item ) {
		if ( isset( $cart_item['_wb_list'] ) ) {
			$value = $this->getName( $cart_item['_wb_list'] );

			if ( isset( $cart_item['_wb_uid'] ) ) {
				$value = '<a href="' . $this->getLink( $cart_item['_wb_list'], $cart_item['_wb_uid'] ) . '">' . $value . '</a>';
			}

			$item_data[] = array(
				'name'  => __( 'Added from', 'wb-list' ),
				'value' => $value
			);

		}


		return $item_data;
	}

	/**
	 * Add list data to order.
	 *
	 * @param $item \WC_Order_Item_Product
	 * @param $cart_item_key string
	 * @param $values array
	 */
	public function add_order_item_data( $item, $cart_item_key, $values ) {
		if ( isset( $values['_wb_list'] ) ) {

			$wb_data = array( 'list' => $values['_wb_list'] );

			$output = $this->getName( $values['_wb_list'] );

			if ( isset( $values['_wb_uid'] ) ) {
				$output = '<a href="' . $this->getLink( $values['_wb_list'], $values['_wb_uid'] ) . '">' . $output . '</a>';

				$wb_data['uid'] = $values['_wb_uid'];
			}

			$item->update_meta_data( '_product_from', $output );
			$item->update_meta_data( '_wb_data', $wb_data );

		}


	}

	/**
	 * Get display name.
	 *
	 * @param $id string wish or bridal
	 *
	 * @return string
	 */
	private function getName( $id ) {
		$class = WB()->getClass( $id );

		return $class->getDisplayName();
	}

	/**
	 * Get page List link.
	 *
	 * @param $id
	 * @param $uid
	 *
	 * @return string
	 */
	private function getLink( $id, $uid ) {
		$class = WB()->getClass( $id );

		return add_query_arg( array( 'u' => $uid ), $class->get_page_url() );
	}

	/**
	 * Change bought item's param if order completed or not-completed.
	 *
	 * @param $order_id
	 * @param $old_status
	 * @param $new_status
	 *
	 * @since 1.1.3 Using WC_Order_Item api for CRUD
	 */
	public function change_bought( $order_id, $old_status, $new_status ) {
		if ( ! in_array( 'completed', array( $old_status, $new_status ) ) ) {
			return;
		}

		$increase = ( $new_status == 'completed' );

		$order = wc_get_order( $order_id );
		$items = $order->get_items();

		/* @var $item \WC_Order_Item_Product */
		foreach ( $items as $item ) {
			$wb_data = $item->get_meta( '_wb_data' );
			if ( $wb_data ) {
				$qty = $item->get_quantity();
				$pid = $item->get_product_id();
				$vid = $item->get_variation_id();

				$class = WB()->getClass( $wb_data['list'] );

				if ( $class instanceof WB_Base ) {

					$class->set_data_uid( (int) $wb_data['uid'] );

					$key = $class->generate_id( $pid, $vid );

					$class->change_bought_item_data( $key, $qty, $increase );
				}
			}
		}
	}


}

new WB_Process;