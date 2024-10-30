<?php


/**
 * Class WB_Base
 */
abstract class WB_Base
{

	/**
	 * wish or bridal.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Name display on front.
	 *
	 * @var string
	 */
	protected $display_name;

	/**
	 * Current user id.
	 *
	 * @var int
	 */
	protected $uid = 0;

	/**
	 * Bridal user id. Not equal current user id if use sharing link.
	 *
	 * @var int
	 */
	protected $owner_uid = 0;

    use WB_Data, WB_Ajax, WB_Link, WB_Shortcode;

	/**
	 * WB_Base constructor.
	 */
	protected function __construct() {

        $position = get_option('wb_link_position', 'after_add_to_cart');
        $hooks = WB_Setting::get_link_position_hooks();
        
        
        add_action( $hooks[$position]['name'], array( $this, 'the_link' ), $hooks[$position]['priority'] );
        
        add_filter( 'wb_link_'. $this->id .'_title', array( $this, 'link_icons' ), 10, 2 );
        add_filter( 'wb_count_link_'. $this->id .'_title', array( $this, 'link_icons' ), 10, 2 );

	    add_action( 'init', array( $this, 'init' ) );

	    $this->uid = get_current_user_id();

    }

	/**
	 * Get title on front.
	 *
	 * @return string
	 */
	public function getDisplayName() {
    	return $this->display_name;
    }

	/**
	 * On init hook.
	 */
	public function init() {
	    add_shortcode( $this->id . 'list',  array( $this, 'shortcode' )  );
	    add_shortcode( $this->id . 'list_count',  array( $this, 'count_shortcode' )  );

		// Not exist if get post content via REST API
		if ( function_exists( 'wc_print_notices' ) ) {
			add_action( "woocommerce_shortcode_before_" . $this->id . "list_products", 'wc_print_notices', 10 );
		}

	    $this->owner_uid = ( isset($_GET['u']) && !empty($_GET['u']) ) ? (int) $_GET['u'] : $this->uid;
    }

	/**
	 * Is current user owner?
	 *
	 * @return bool
	 */
	public function is_current_user_owner() {
		return ( $this->owner_uid == $this->uid );
	}

	/**
	 * Getter.
	 *
	 * @return int
	 */
	public function get_owner_uid() {
		return $this->owner_uid;
	}

	/**
	 * Return uid used for getting and setting data.
	 *
	 * @return int
	 */
	abstract protected function get_data_uid();

	/**
	 * Set uid used for getting and setting data.
	 *
	 * @param $uid
	 *
	 * @return void
	 */
	abstract public function set_data_uid( $uid );

}