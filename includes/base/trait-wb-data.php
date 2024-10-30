<?php


/**
 * Class WB_Data
 */
trait WB_Data
{

	/**
	 * Wish or bridal data need?
	 *
	 * @return string
	 */
	protected function get_data_name() {
        return '_wb_' . $this->id .'_data';
    }

	/**
	 * Get array of user data.
	 *
	 * Structure:
	 * [
	 *   '(string) key' => [
	 *              'pid' => int,
	 *              'vid' => int ,
	 *              'qty' => int,
	 *              'bought' => int
	 *
	 *              ]
	 * ]
	 *
	 * @return array|mixed
	 */
	public function get_data( ) {
        $uid        = $this->get_data_uid();
        $data_name = $this->get_data_name();

        if ( $uid != 0 ) {
            $data = get_user_meta($uid, $data_name, true);
            $list = ( !empty( $data) ) ? $data : array();
        } else {
            $list =  WC()->session->get( $data_name, array() );
        }
        return $list;
    }

	/**
	 * Save user data.
	 *
	 * @param $list array List data
	 *
	 * @return bool|int
	 */
	protected function set_data( $list ) {
	    $uid        = $this->get_data_uid();
        $data_name = $this->get_data_name();

        if ( $uid != 0 ) {
          $result = update_user_meta( $this->uid, $data_name, $list );
        } else {
	        if ( !WC()->session->has_session() ) {
		        WC()->session->set_customer_session_cookie( true );
	        }

	        WC()->session->set($data_name, $list);
	        $result = true;
        }

        return $result;
    }

	/**
	 * Delete item from user list.
	 * @see generate_id
	 *
	 * @param $key string Return by method generate_id.
	 *
	 */
	protected function delete_item_data( $key ) {
        $data = $this->get_data();

        if ( isset($data[$key]) ) {
            unset( $data[$key] );
            $this->set_data( $data );
        }
    }

	/**
	 * Add new item in user list.
	 *
	 * @param $key string
	 * @param $new_item_data string
	 */
	protected function add_item_data( $key, $new_item_data ) {
        $aNewItem = array( $key => $new_item_data);

        $this->set_data( array_merge($this->get_data(), $aNewItem) );
    }

	/**
	 * Change item data in user list.
	 *
	 * @param $key string
	 * @param $data_changed array
	 *
	 * @return bool|int False on failure.
	 */
	protected function change_item_data( $key, $data_changed ) {
      
        $data = $this->get_data();

        if ( isset($data[$key]) ) {
	        $data[$key] = array_merge( $data[$key], $data_changed );
	        return $this->set_data( $data );
        }
        return false;
    }

	/**
	 * Get number items in list.
	 *
	 * @return int Number items.
	 */
	public function count_items_data() {
        return count( $this->get_data() );
    }

	/**
	 * Is item in list?
	 *
	 * @param $key string
	 *
	 * @return bool
	 */
	protected function is_in_list( $key ) {
        $data = $this->get_data();
        return isset($data[$key]) && !empty($data[$key]) ;
    }

	/**
	 * Get item data from user list.
	 *
	 * @param $key string
	 *
	 * @return array|bool False on failure.
	 */
	protected function get_item_data( $key ) {
	    $data = $this->get_data();
	    $item_data = isset($data[$key]) && !empty($data[$key]) ? $data[$key] : false;
	    return $item_data;
    }

	/**
	 * Generate key for item.
	 *
	 * @param $pid int Product id.
	 * @param int $vid Variation id.
	 *
	 * @return string Item key.
	 */
	public function generate_id( $pid, $vid = 0) {
		$id_parts = array( $pid );

		if ( !empty($vid) ) {
			$id_parts[] = $vid;
		}

		return md5( implode( '_', $id_parts ) );
	}

	/**
	 * Increase or decrease items bought data.
	 *
	 * @see WB_Process::change_bought()
	 *
	 * @param $key string
	 * @param $qty int
	 * @param bool $increase Is increase bought?
	 *
	 * @return bool|int False on failure.
	 */
	public function change_bought_item_data( $key, $qty, $increase = true ) {
    	$data = $this->get_item_data( $key );

    	$data['bought'] = ( $increase ) ? ( $data['bought'] + $qty ) : max((  $data['bought'] - $qty ), 0);

    	return $this->change_item_data($key, $data);

	}
  

}