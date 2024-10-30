<?php


/**
 * Display Add to Wish List link on single product page.
 *
 */
function wb_wish_link() {
    WB()->getWish()->the_link();
}

/**
 * Display link to Wish List page and number added product.
 * @param string $title Set link text.
 */
function wb_wish_count_link( $title = '' ) {
	echo WB()->getWish()->get_count_link( $title );
}

/**
 * Get link to Wish List page. Setting user.
 *
 * @return false|string
 */
function wb_wish_get_page_url() {
    return WB()->getWish()->get_page_url();
}


/**
 * Get Wish Link items.
 *
 * @param int $uid
 *
 * @return array
 */
function wb_wish_get_items( $uid = 0 ) {
	if ( $uid ) {
		WB()->getWish()->set_data_uid( $uid );
	}

    return WB()->getWish()->get_data();
}

/**
 * Display Add to Bridal List link on single product page. If user enable bridal link functionality.
 */
function wb_bridal_link() {
	if (!WB()->is_bridal_enabled()) return;

	WB()->getBridal()->the_link();

}

/**
 * Display link to Wish List page and number added product. If user enable bridal link functionality.
 * @param string $title Set link text.
 */
function wb_bridal_count_link( $title = '' ) {
	if (!WB()->is_bridal_enabled()) return;

	echo WB()->getBridal()->get_count_link( $title );
}

/**
 * Get link to Wish List page. Setting user.

 * @return false|string
 */
function wb_bridal_get_page_url() {
	if (!WB()->is_bridal_enabled()) return '';

	return WB()->getBridal()->get_page_url();

}


/**
 *
 * Get Bridal Link items. If user enable bridal link functionality.

 * @param int $uid
 *
 * @return array
 */
function wb_bridal_get_items( $uid = 0 ) {
	if (!WB()->is_bridal_enabled()) return array();

	if ( $uid ) {
		WB()->getBridal()->set_data_uid( $uid );
	}

	return WB()->getBridal()->get_data();

}