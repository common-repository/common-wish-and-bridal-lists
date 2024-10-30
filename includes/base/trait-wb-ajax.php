<?php

/**
 * Trait WB_Ajax
 *
 * @property int $pid Product id from POST request
 * @property int $vid Variation id from POST request, 0 for simple product
 * @property int $qty Product quantity from POST request
 * @property int $key Key item for list data
 * @see WB_Data::generate_id()
 */

trait WB_Ajax
{
	/**
	 * Data when add to list.
	 *
	 * @var array
	 */
	protected $meta = array ('pid', 'vid', 'qty');

	/**
	 * Storage for magic method.
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Get pid, vid or qty.
	 *
	 * @param $name string
	 *
	 * @return null
	 */
	public function __get($name) {
        return  isset($this->data[$name]) ?  $this->data[$name] : null;
    }

	/**
	 *
	 * Need for correct empty function.
	 *
	 * @param $name string
	 *
	 * @return bool
	 */
	public function __isset($name) {
		return  isset($this->data[$name]);
	}

	/**
	 * Display error notice on reloading page.
	 *
	 * @param string $notice
	 */
	protected function display_error($notice = '' )
    {
        $notice = ( !empty($notice) ) ? $notice : __('Error', 'wb-list');

        wc_add_notice( $notice, 'error' );
        wp_send_json_error(array(
            'reload' => true
        ));
    }

	/**
	 * Display success notice on reloading page.
	 *
	 * @param string $notice
	 */
	protected function display_success($notice = '' )
    {
        $notice = ( !empty($notice) ) ? $notice : __('Success', 'wb-list');

        wc_add_notice( $notice, 'success' );
        wp_send_json_success(array(
            'reload' => true
        ));
    }


	/**
	 *
	 * Select need method for wb_action.
	 * Validate $_POST data.
	 *
	 */
	public function do_wb_ajax() {

        $action = (string)$_POST['wb_action'];

        if ( false === check_ajax_referer( $action , 'nonce' , false) ) {
            $this->display_error();
        };

        foreach ($this->meta as $meta ) {
            $this->data[$meta] = ( isset($_POST[$meta]) && !empty($_POST[$meta]) ) ? (int)$_POST[$meta] : 0;
        }

	    $this->data['key'] = ( isset($_POST['key']) && !empty($_POST['key']) ) ? (string)$_POST['key'] : '';

        if ( empty( $this->key ) && empty( $this->pid ) ) {
            $this->display_error( __('Product ID is not defined', 'wb-list') );
        }

	    $this->data['key'] = !empty( $this->key ) ? $this->key : $this->generate_id( $this->pid, $this->vid );

	    if ( !empty($this->pid) ) {
		    $product = wc_get_product( $this->pid );

		    if (  $product->get_type() == 'variable'  ) {
			    $this->display_error( __( 'Variable product not support', 'wb-list' ) );
		    }

	    }

        if ( method_exists($this, $action) ) {
            $this->$action();
        }
    }

	/**
	 * Add or delete product from list.
	 *
	 */
	private function toggle() {

    	$added = $this->is_in_list( $this->key );
        
        if ( !$added ) {
            $this->add_item_data( $this->key, array (
                    'pid' => $this->pid,
                    'vid' => $this->vid,
                    'qty' => 1,
		            'bought' => 0
                )
            );
        } else {
            $this->delete_item_data( $this->key );
        }

        $output = $this->get_link( $this->pid, $this->vid, !$added );

        if ( $output ) {
            wp_send_json_success(array(
                'output' => $output,
                'trigger' => 'change_' . $this->id .'_count',
		         'update_fragment' => array(
		            	'.wb-' . $this->id . '-count-link' => $this->get_count_link()
		            )
                )
            );

        } else {
            $this->display_error( __('Can\'t process this item', 'wb-list') );
        }

    }

	/**
	 *
	 * Change quantity product added in list.
	 *
	 */
	private function change_quantity() {
        if ( $this->qty < 1  ) {
            $this->display_error( __('Quantity is not defined', 'wb-list') );
        }

       if ( $this->change_item_data($this->key, array('qty' => $this->qty) ) ) {

	       $item_data = $this->get_item_data( $this->key );

	       $this->current_item = array('key' => $this->key) + $item_data;

	       $this->current_product = wc_get_product( $this->current_item['pid'] );

	       ob_start();

	       $this->display_add_to_cart();

	       $add_to_cart = ob_get_clean();

	       wp_send_json_success(array(
		       'reload' => false,
		       'update_fragment' => array(
			       "#{$this->key}" => $add_to_cart
		       )
	       ));
       } else {
           $this->display_error( __('Can\'t change quantity', 'wb-list') );
       }
    }

	/**
	 * Delete product from list.
	 *
	 */
	private function delete_from_list() {
        $this->delete_item_data( $this->key );

        if ( !$this->is_in_list( $this->key ) ) {
            $this->display_success( __('Successfully delete from list', 'wb-list') );
        } else {
            $this->display_error( __('Can\'t delete from list', 'wb-list') );
        }

    }

}