<?php


/**
 * Trait WB_Link
 */
trait WB_Link
{
	/**
	 * Output link in single product page.
	 *
	 * @param null $pid Product id
	 * @param null $vid Variation id
	 * @param null $added Is product added in list?
	 *
	 * @return string
	 */
	public function get_link($pid = null, $vid = null, $added = null ) {

        if ( !is_ajax() ) {

	        global $product;

	        if ( empty($product) ) return '';

	        if ( 'variable' == $product->get_type() ) {
		        return '';
	        }

	        $pid = ! is_null( $pid ) ? $pid : $product->get_id();

	        $vid = ! is_null( $vid ) ? $vid : 0;
        }

	    $key = $this->generate_id( $pid, $vid );

	    $added = !is_null($added) ? $added : $this->is_in_list( $key );

        $status = ( $added ) ? 'added' : 'not-added';

        $wish_list_title = array(__('Add to Wish List', 'wb-list'), __('Added to Wish List', 'wb-list'));
        $bridal_list_title = array(__('Add to Bridal List', 'wb-list'), __('Added to Bridal List', 'wb-list'));

        $title = ($this->id == 'wish' ) ? $wish_list_title : $bridal_list_title;

        $title = $title[(bool) $added];

        $title_attribute = $disabled = '';

        if ( ('bridal' == $this->id) && empty($this->uid) ) {
	        $disabled = 'wb-link-disabled';

	        $title_attribute = __( 'Bridal list available only for registered user. Please register.', 'wb-list' );

        }

        $output = sprintf('<a href="#" data-pid="%s" data-vid="%s" class="wb-ajax wb-link %s wb-%s-link %s" data-action="%s" data-wb_action="%s" data-nonce="%s" title="%s">%s</a>',
            $pid,
            $vid,
	        $disabled,
            $this->id,
            apply_filters( 'wb_link_'. $this->id .'_classes', $status, $added, $this->id ),
            'wb_ajax_' . $this->id,
            'toggle',
            wp_create_nonce('toggle'),
	        $title_attribute,
            apply_filters( 'wb_link_'. $this->id .'_title', $title, $added, $this->id )
        );

        return $output;
    }

	/**
	 * Echoing link on single product page.
	 */
	public function the_link() {
        echo $this->get_link();
    }

	/**
	 * Return page on List page.
	 *
	 * @return false|string
	 */
	public function get_page_url() {
        $page_id = get_option( 'wb_' . $this->id . '_page_id' );
        return get_permalink( $page_id );
    }

	/**
	 * Return link for sharing List.
	 *
	 * @return string
	 */
	protected function get_share_link() {
		return add_query_arg( array('u' => $this->owner_uid), $this->get_page_url() );
	}

	/**
	 * Output link on List page and display number items in list.
	 *
	 * @param string $title
	 * @param null $is_not_empty
	 *
	 * @return string
	 */
	public function get_count_link( $title = '', $is_not_empty = null) {
    	$count = $this->count_items_data();

	    $is_not_empty = !is_null($is_not_empty) ? $is_not_empty : ($count > 0);
	    $title = !empty( $title ) ? $title :  $this->display_name;

	    $title .= "<span class='wb-count wb-{$this->id}-count'>$count</span>";

	    $output = sprintf('<a href="%s"  class="wb-count-link wb-%s-count-link %s">%s</a>',
		    $this->get_page_url(),
		    $this->id,
		    apply_filters( 'wb_count_link_'. $this->id .'_classes', '', $is_not_empty, $this->id ),
		    apply_filters( 'wb_count_link_'. $this->id .'_title', $title, $is_not_empty, $this->id )
	    );

	    return $output;
    }


	/**
	 * Select link icon based on user setting.
	 *
	 * @param $title
	 * @param $added
	 *
	 * @return string
	 */
	function link_icons( $title, $added ) {
        
        $style = get_option('wb_link_style', 'default');
        if ( $style != 'default' ) {
            
            $option_end = ($added) ? 'ad' : 'na';

            $icon = WB_Setting::get_icon();

            $class = ($style == 'custom') ? get_option('wb_' . $this->id .'_fa_' . $option_end) : $icon[$this->id][$style][(int)$added];
            $title = '<i class="fa ' . $class . '"></i>' . $title;
        }

        return $title;

    }
}