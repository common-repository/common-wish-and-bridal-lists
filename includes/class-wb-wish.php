<?php


/**
 * Class WB_Wish
 */
class WB_Wish extends WB_Base
{

	/**
	 * Class id.
	 *
	 * @var string
	 */
	protected $id = 'wish';

	/**
	 * WB_Wish constructor.
	 */
	public function __construct( ) {
        parent::__construct();

	    $this->display_name = __('Wish List', 'wb-list');

        add_action( 'wp_ajax_nopriv_wb_ajax_' . $this->id, array($this, 'do_wb_ajax') );
        add_action( 'wp_ajax_wb_ajax_' . $this->id, array($this, 'do_wb_ajax') );

        
    }

	/**
	 * In wish list always current user.
	 *
	 * @return int
	 */
	protected function get_data_uid() {
		return $this->uid;
	}

	/**
	 *
	 * In wish list always current user.
	 *
	 * @param $uid
	 */
	public function set_data_uid( $uid ){
		$this->uid =  $uid;
	}
}