<?php


/**
 * Class WB_Setting
 */
class WB_Setting
{

	/**
	 * WB_Setting constructor.
	 */
	public function __construct()
    {
        add_filter('woocommerce_get_sections_products', array($this, 'add_section'));
        add_filter('woocommerce_get_settings_products', array($this, 'all_settings'), 10, 2);

    }


	/**
	 * @param $sections
	 *
	 * @return mixed
	 */
	public function add_section($sections)
    {
        $sections['wishlist'] = __('Wish&Bridal List', 'wb-list');
        return $sections;
    }

	/**
	 * @param $settings
	 * @param $current_section
	 *
	 * @return array
	 */
	public function all_settings($settings, $current_section)
    {

        if ($current_section == 'wishlist') {

            $settings = array();

	        $settings[] = array(
	        	'name' => __('Need more functional and support?', 'wb-list'),
	            'desc' => sprintf('<ol><li>%s</li><li>%s</li><li>%s</li></ol><a href="https://codecanyon.net/item/common-wish-and-bridal-lists/19334566">%s</a>',
		            __('Support variable product', 'wb-list'),
		            __('Add to list from archive page', 'wb-list'),
		            __('Better UX', 'wb-list'),
		            __('Buy PRO version!', 'wb-list')
	            ),
		        'type' => 'title', 'id' => 'wishlist-pro');

            $settings[] = array('name' => __('General settings', 'wb-list'), 'type' => 'title', 'id' => 'wishlist');

            $hook = self::get_link_position_hooks();

            $value = array_keys($hook);
            $option = wp_list_pluck($hook, 'title');

            $settings[] = array(
                'title' => __('"Add to list" link position', 'wb-list'),
                'desc' => __('Choose a position for "Add to Wish List" link. If you decide to use "bridal list" functionality, "Add to Bridal List" link will be added next to "Add to Wish List" link.', 'wb-list'),
                'id' => 'wb_link_position',
                'class' => 'wc-enhanced-select',
                'css' => 'min-width:300px;',
                'default' => 'after_add_to_cart',
                'type' => 'select',
                'options' => array_combine($value, $option)
            );

            $settings[] = array(
                'name' => __('Enable/disable "bridal list" functionality', 'wb-list'),
                'desc' => __('You can enable/disable "bridal list" functionality here.', 'wb-list'),
                'id' => 'wb_bridal_enable',
                'default'         => 'yes',
                'type' => 'checkbox',
                'css' => 'min-width:300px;',
            );

            $settings[] = array('type' => 'sectionend', 'id' => 'wb-list');

            $settings[] = array('title' => __('Pages', 'wb-list'), 'desc' => __('These pages need to be set so that Wish&Bridal List Plugin knows where display added product.', 'wb-list'), 'type' => 'title', 'id' => 'wishlist_page');

            $settings[] = array(
                'title' => __('Wish List Page', 'woocommerce'),
                'desc' => __('Page contents:', 'woocommerce') . ' [wishlist]',
                'id' => 'wb_wish_page_id',
                'type' => 'single_select_page',
                'default' => '',
                'class' => 'wc-enhanced-select-nostd',
                'css' => 'min-width:300px;',
                'desc_tip' => true,
            );

            $settings[] = array(
                'title' => __('Bridal List Page', 'woocommerce'),
                'desc' => __('Page contents:', 'woocommerce') . ' [bridallist]',
                'id' => 'wb_bridal_page_id',
                'type' => 'single_select_page',
                'default' => '',
                'class' => 'wc-enhanced-select-nostd',
                'css' => 'min-width:300px;',
                'desc_tip' => true,
            );

            $settings[] = array('type' => 'sectionend', 'id' => 'wishlist_page');

            $settings[] = array('name' => __('Visual appearance', 'wb-list'), 'type' => 'title', 'id' => 'wishlist_visual');

            $settings[] = array(
                'title' => __('Style of Wish and Bridal Lists links (both states)', 'wb-list'),
                'desc' => sprintf(__('Choose one of prepared styles (icons) or choose "Custom" and define your fontawesome icons (%s a full list of fontawesome icons here %s).', 'wb-list'), '<a href="http://fortawesome.github.io/Font-Awesome/icons/">', '</a>'),
                'id' => 'wb_link_style',
                'class' => 'wc-enhanced-select',
                'css' => 'min-width:300px;',
                'default' => 'default',
                'type' => 'select',
                'options' => array(
                    'default' => __('Default (no icons)', 'wb-list'),
                    'heart-and-gift' => __('Heart and Gift icons', 'wb-list'),
                    'star-and-gift' => __('Star and Gift icons', 'wb-list'),
                    'heart-and-venus-mars' => __('Heart and Venus-Mars icons', 'wb-list'),
                    'custom' => __('Custom (define custom fontawesome icons)', 'wb-list')
                )
            );

            $example = __('Example: "fa-amazon". Another example: "fa-gift"', 'wb-list');

            $settings[] = array(
                'name' => __('Custom fontawesome icon for Wish List link (added)', 'wb-list'),
                'desc_tip' => $example,
                'id' => 'wb_wish_fa_ad',
                'type' => 'text',
            );

            $settings[] = array(
                'name' => __('Custom fontawesome icon for Wish List link (not added)', 'wb-list'),
                'desc_tip' => $example,
                'id' => 'wb_wish_fa_na',
                'type' => 'text',
            );

            $settings[] = array(
                'name' => __('Custom fontawesome icon for Bridal List link (added)', 'wb-list'),
                'desc_tip' => $example,
                'id' => 'wb_bridal_fa_ad',
                'type' => 'text',
            );

            $settings[] = array(
                'name' => __('Custom fontawesome icon for Bridal List link (not added)', 'wb-list'),
                'desc_tip' => $example,
                'id' => 'wb_bridal_fa_na',
                'type' => 'text',
            );

            $settings[] = array('type' => 'sectionend', 'id' => 'wishlist_visual');
        }

        return $settings;
    }

    /**
     *
     */
    public static function get_link_position_hooks()
    {
        $aHooks = apply_filters('wb_link_positions',
            array(
                'none' => array(
                    'title' => __('None (insert by php function by myself)', 'wb-list'),
                    'name' => '', 
                    'priority' => 0),
                'after_add_to_cart' => array(
                    'title' => __('After Add to Cart', 'wb-list'),
                    'name' => 'woocommerce_single_product_summary', 
                    'priority' => 30),
                'after_thumb' => array(
                    'title' => __('After Thumbnail', 'wb-list'),
                    'name' => 'woocommerce_product_thumbnails', 
                    'priority' => 40),
                'after_product_summary' => array(
                    'title' => __('After Product Summary', 'wb-list'),
                    'name' => 'woocommerce_single_product_summary', 
                    'priority' => 40)
            )
        );
        return $aHooks;
    }

	/**
	 * @return array
	 */
	public static function get_icon() {
        $wish = array();

        $wish['heart-and-gift'] = array('fa-heart-o', 'fa-heart');
        $wish['star-and-gift'] = array('fa-star-o', 'fa-star');
        $wish['heart-and-venus-mars'] = array('fa-heart-o', 'fa-heart');

        $bridal = array();

        $bridal['heart-and-gift'] = array('fa-gift', 'fa-gift');
        $bridal['star-and-gift'] = array('fa-gift', 'fa-gift');
        $bridal['heart-and-venus-mars'] = array('fa-venus-mars', 'fa-venus-mars');


        return array('wish' => $wish, 'bridal' => $bridal);
    }



}

new WB_Setting;