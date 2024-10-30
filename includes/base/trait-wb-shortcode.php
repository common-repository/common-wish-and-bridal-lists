<?php


/**
 *
 * Class WB_Shortcode
 */
trait WB_Shortcode
{

	/**
     * Item in loop.
     *
	 * @var array
	 */
	protected $current_item = null;

	/**
     * Product in loop.
     *
	 * @var object Woocommerce Product object
	 */
	protected $current_product = null;

	/**
     * Shortcode [wishlist] [bridallist]
     *
	 * @param $atts array Shortcode attributes
	 *
	 * @return string
	 */
	public function shortcode( $atts ) {

		$list = $this->get_data();

		if ( !empty($list) ) {
			return $this->loop( $list, $atts );
		} else {
			ob_start();
			do_action( "woocommerce_shortcode_before_" . $this->id . "list_products" );
			wc_get_template( 'loop/no-products-found.php' );
			return ob_get_clean();
		}
	}

	/**
     * Loop item in list.
     *
	 * @param $list array User list
	 * @param $atts array Shortcode attributes
	 *
	 * @return string
	 */
	private function loop( $list, $atts) {

		$this->content_hook();



		global $woocommerce_loop, $post, $product;

		$aAttr = shortcode_atts( array(
			'columns' => '4'
		), $atts );

		$columns                     = absint( $aAttr['columns'] );
		$woocommerce_loop['columns'] = $columns;

		ob_start();

		do_action( "woocommerce_shortcode_before_" . $this->id . "list_products" );

		echo '<div class="woocommerce columns-' . $columns . ' ' . $this->id . 'list-products wb-products clear">';

		do_action( "woocommerce_shortcode_before_" . $this->id . "list_loop" );

		woocommerce_product_loop_start();

		foreach ( $list as $key => $item ) {
			$this->current_item = array('key' => $key) + $item;

			$this->current_product = wc_get_product( $this->current_item['pid'] );

			if ('variable' !== $this->current_product->get_type()) {
				$post = $this->current_product->get_post_data();
				$product = $this->current_product;
				wc_get_template_part( 'content', 'product' );
			}

		}

		woocommerce_product_loop_end();

		do_action( "woocommerce_shortcode_after_" . $this->id . "list_loop" );

		echo '</div>';

		woocommerce_reset_loop();

		wp_reset_postdata();

		$this->current_product = $this->current_item = null;

		$output = ob_get_clean();

		return apply_filters($this->id . 'list_shortcode_output', $output, $list);

	}

	/**
     *
     * Change default Woocommerce template content-single-product.php.
	 *
	 */
	protected function content_hook() {
		add_action( 'woocommerce_after_shop_loop_item',  array( $this, 'add_input'), 9 );
		add_filter('woocommerce_loop_add_to_cart_link', array($this, 'woocommerce_loop_add_to_cart_link') );

		add_action( 'woocommerce_before_shop_loop_item', array($this, 'woocommerce_template_loop_product_link_open'), 11);
	}


	/**
	 * Remove default add to cart link.
	 *
	 * @param $link
	 *
	 * @return string
	 */

	public function woocommerce_loop_add_to_cart_link( $link ) {
		if ( $this->current_product ) {
			$link = '';
		}

		return $link;
	}


	/**
	 * Add remove from list button.
	 *
	 */
	public function woocommerce_template_loop_product_link_open() {
		if ( $this->current_product && $this->is_current_user_owner() ) { ?>
            <span
                    class="wb-ajax wb-remove wb-<?php echo $this->id; ?>-remove"
                    title="<?php _e('Remove product', 'wb-list'); ?>"
                    data-key="<?php echo $this->current_item['key']; ?>"
                    data-action="wb_ajax_<?php echo $this->id; ?>"
                    data-wb_action="delete_from_list"
                    data-nonce="<?php echo wp_create_nonce( 'delete_from_list' ); ?>"></span>
		<?php }
	}

	/**
     * Add to cart button.
	 *
	 */
	public function display_add_to_cart_link() {

		$product = $this->current_product;

		$output = '';

	    if ( ($this->current_item['qty'] - $this->current_item['bought']) > 0) {

		    $class = implode( ' ', array_filter( array(
			    'button',
			    'product_type_' . $product->product_type,
			    $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
			    $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : ''
		    ) ) );

		    $output = sprintf( '<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" data-list="%s" data-uid="%s" class="%s">%s</a>',
			    esc_url( $product->add_to_cart_url() ),
			    1,
			    esc_attr( $product->id ),
			    esc_attr( $product->get_sku() ),
			    esc_attr( $this->id ),
			    esc_attr( $this->owner_uid ),
			    esc_attr( $class ),
			    esc_html( $product->add_to_cart_text() )
		    );
	    }

		echo $output;
	}

	/**
     *
     * Add custom element to Woocommerce template content-single-product.php.
	 *
	 */
	public function add_input() {
		if ( !$this->current_product ) return;

		$this->display_setting();

		$this->display_add_to_cart();


	}

	/**
     * Add remove button and number input for changing quantity product.
	 *
	 */
	protected function display_setting() {
		?>
        <div class="wb-item-setting wb-row">

            <div class="wb-item-text wb-col"><?php _e('Need/Done:', 'wb-list'); ?></div>

            <div class="wb-col">

				<?php if ( $this->is_current_user_owner() ) { ?>

                    <div class="quantity">
                        <input type="number"
                               step="1"
                               min="1"
                               name="wb-qty"
                               value="<?php echo esc_attr( $this->current_item['qty'] ); ?>"
                               title="<?php _e( 'Set quantity', 'wb-list' ) ?>"
                               class="input-text qty <?php echo $this->id ?>-set-qty text"
                               size="4"
                               data-action="wb_ajax_<?php echo $this->id; ?>"
                               data-wb_action="change_quantity"
                               data-key="<?php echo $this->current_item['key']; ?>"
                               pattern="[0-9]*"
                               inputmode="numeric"
                               data-nonce="<?php echo wp_create_nonce( 'change_quantity' ); ?>"/>
                    </div>
				<?php } else {
					echo $this->current_item['qty'];
				} ?> / <?php echo $this->current_item['bought']; ?>
            </div>

        </div>


	<?php }

	/**
	 * Display qty input and add to cart button or Already bought message.
	 */
	protected function display_add_to_cart() {
		$need = max($this->current_item['qty'] - $this->current_item['bought'], 0);

		?>
        <div class="wb-item-buy" id="<?php echo $this->current_item['key']; ?>">
			<?php if ( $need ) { ?>
                <div class="wb-row">
                    <div class="wb-item-text wb-col"><?php _e('Quantity:', 'wb-list'); ?></div>
                    <div class="wb-col">
						<?php woocommerce_quantity_input(array('max_value'   => $need, 'min_value' => 1 ), $this->current_product); ?>
                    </div>
                </div>
				<?php $this->display_add_to_cart_link();
				?>
			<?php } else { ?>
                <span class="wb-item-already-buy wb-item-text"><?php _e('Already bought', 'wb-list'); ?></span>
			<?php } ?>
        </div>


	<?php  }

	/**
     * Shortcode [wishlist_count] [bridallist_count]
     *
	 * @return string
	 */
	public function count_shortcode() {
		return $this->get_count_link();
	}

}