<?php


/**
 * Class WB_Bridal
 */
class WB_Bridal extends WB_Base
{

	/**
     * Class id.
     *
	 * @var string
	 */
	protected $id = 'bridal';

	/**
     * Data for Bridal page.
     *
	 * @var array
	 */
	protected $permitted_meta = array('name', 'date');


	/**
	 * WB_Bridal constructor.
	 */
	public function __construct() {
        parent::__construct();

	    $this->display_name = __('Bridal List', 'wb-list');
        
        add_action( 'wp_ajax_wb_ajax_' . $this->id, array($this, 'do_wb_ajax') );
        add_action( 'wp_ajax_wb_save_bridal_data', array($this, 'save_bridal_data') );

	    add_action( "woocommerce_shortcode_before_" . $this->id . "list_products" , array($this, 'render_bridal_data'), 20);

    }

	/**
     * Display bridal data: name and wedding date.
	 *
	 */
	public function render_bridal_data() {
		if ( $this->owner_uid ) {
			$editable = $this->is_current_user_owner() ? 'js-editable' : '';
			$title = $this->is_current_user_owner() ? __('Edit', 'wb-list') : '';

			?>
            <div class="wb-bridal-data">
            <h2 class="wb-bridal-title">
                <span class="wb-bridal-name <?php echo $editable; ?>" data-editable="text" data-bridal="name" title="<?php echo $title; ?>" ><?php $this->e_bridal_data('_wb_bridal_name', __('Wedding List of Bride and Groom', 'wb-list')); ?></span> &mdash;
                <span class="wb-bridal-date <?php echo $editable; ?>" data-editable="date" data-bridal="date" title="<?php echo $title; ?>"><?php $this->e_bridal_data('_wb_bridal_date', __('soon', 'wb-list')); ?></span>
            </h2>


			<?php if ( $this->is_current_user_owner() ) { ?>

                <div class="wb-bridal-share">
                    <p><?php _e('Link for share:', 'wb-list') ?> <a href="<?php echo $this->get_share_link(); ?>" class="wb-bridal-share-link"><?php echo $this->get_share_link(); ?></a></p>
                </div>
                </div>
			<?php } ?>

		<?php } else { ?>
            <div class="wb-bridal-unregistered"><?php
				echo apply_filters('wb_bridal_unregistered_text', sprintf(
					__( 'Bridal list available only for registered user. Please <a href="%1s">register.</a>', 'wb-list' ),
					get_permalink( wc_get_page_id('myaccount') )
				)); ?></div>
		<?php }
	    }

	/**
     * Echoing user bridal data.
     *
	 * @param $meta string Meta name.
	 * @param $default string
	 */
	protected function e_bridal_data( $meta, $default ) {
		$value = get_user_meta( $this->owner_uid, $meta, true );
		echo $value ? get_user_meta( $this->owner_uid, $meta, true ) : $default;
	}



	/**
	 * Save bridal data as user meta.
     *
	 */
	public function save_bridal_data() {

	    $action = (string)$_POST['action'];

	    if ( false === check_ajax_referer( $action , 'nonce' , false) ) {
		    $this->display_error();
	    };

	    $meta = (string)$_POST['bridal'];

	    if ( !in_array( $meta, $this->permitted_meta )) {
		    $this->display_error();
        }

        $meta_key = '_wb_bridal_' . $meta;

	    $value = (string)$_POST['value'];

	    if ( empty($value) ) {
		    $this->display_error();
        }

        if ( false == update_user_meta($this->owner_uid, $meta_key, $value) ) {
	        $this->display_error( __('Can\'t update value', 'wb-list') );
        }

	    wp_send_json_success(array(
		    'reload' => false
	    ));

    }


	/**
     * For bridal maybe guest.
     *
	 * @return int
	 */
	protected function get_data_uid() {
		return $this->owner_uid;
    }

	/**
     * Set data uid.
     *
	 * @param $uid
	 */
	public function set_data_uid( $uid ){
	    $this->owner_uid =  $uid;
    }





}