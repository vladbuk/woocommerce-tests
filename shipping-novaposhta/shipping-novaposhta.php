<?php
/**
 * Plugin Name: NovaPoshta Shipping Method
 * Plugin URI: http://vladbuk.com/woocommerce/plugins/shipping-novaposhta
 * Description: Плагин добавляет в магазин метод доставки "Новая почта".
 * Version: 1.0.1
 * Author: Vladbuk
 * Author URI: http://vb.biz.ua/
 * Requires at least: 3.8.1
 * Tested up to: 4.4.2
 *
 * Text Domain: shipping-novaposhta
 * Domain Path: /languages/
 *
 * @package Shipping_Novaposhta
 * @category Core
 * @author Vladbuk
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	function novaposhta_shipping_method_init() {
		if ( ! class_exists( 'WC_Novaposhta_Shipping_Method' ) ) {
			class WC_Novaposhta_Shipping_Method extends WC_Shipping_Method {
				/**
				 * Constructor for your shipping class
				 *
				 * @access public
				 * @return void
				 */
				public function __construct() {
					$this->id                 = 'novaposhta_shipping'; // Id for your shipping method. Should be uunique.
					$this->method_title       = __( 'Новая почта' );  // Title shown in admin
					$this->method_description = __( 'Доставка товаров по Украине Новой Почтой с фиксированной стоимостью доставки' ); // Description shown in admin

					//$this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
					//$this->title              = "Доставка Укрпочтой"; // This can be added as an setting but for this example its forced.

					$this->init();
				}

				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				function init() {
					// Load the settings API
					$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
					$this->init_settings(); // This is part of the settings API. Loads settings you previously init.
					
							// Define user set variables
					$this->title        = $this->get_option( 'title' );
					$this->availability = $this->get_option( 'availability' );
					$this->countries    = $this->get_option( 'countries' );
					//$this->tax_status   = $this->get_option( 'tax_status' );
					$this->cost         = $this->get_option( 'cost' );
					//$this->type         = $this->get_option( 'type', 'class' );
					$this->options      = $this->get_option( 'options', false ); // @deprecated in 2.4.0

					// Save settings in admin if you have any defined
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
				}
				
				/**
				* Initialize Settings Form Fields.
				*/
 function init_form_fields() {
     $this->form_fields = array(
	 	'enabled' => array(
		'title' 		=> __( 'Enable/Disable', 'woocommerce' ),
		'type' 			=> 'checkbox',
		'label' 		=> __( 'Enable this shipping method', 'woocommerce' ),
		'default' 		=> 'no',
	),
     'title' => array(
          'title' => __( 'Title', 'woocommerce' ),
          'type' => 'text',
          'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
          'default' => __( 'Nova Poshta', 'woocommerce' ),
		  'desc_tip'		=> true
          ),
     'description' => array(
          'title' => __( 'Description', 'woocommerce' ),
          'type' => 'textarea',
          'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ),
          'default' => __("Nova Poshta shipping method in Ukraine ", 'woocommerce'),
		  'desc_tip'		=> true
           ),
	'availability' => array(
		'title' 		=> __( 'Availability', 'woocommerce' ),
		'type' 			=> 'select',
		'default' 		=> 'all',
		'class'			=> 'availability wc-enhanced-select',
		'options'		=> array(
			'all' 		=> __( 'All allowed countries', 'woocommerce' ),
			'specific' 	=> __( 'Specific Countries', 'woocommerce' ),
		),
	),	
	'countries' => array(
		'title' 		=> __( 'Specific Countries', 'woocommerce' ),
		'type' 			=> 'multiselect',
		'class'			=> 'wc-enhanced-select',
		'css'			=> 'width: 450px;',
		'default' 		=> '',
		'options'		=> WC()->countries->get_shipping_countries(),
		'custom_attributes' => array(
			'data-placeholder' => __( 'Select some countries', 'woocommerce' )
		)
	),
	'cost' => array(
		'title' 		=> __( 'Cost', 'woocommerce' ),
		'type' 			=> 'price',
		'placeholder'	=> '',
		//'description'	=> $cost_desc,
		'description'	=> "Укажите стоимость доставки",
		'default'		=> '',
		'desc_tip'		=> true
	)	
     );
} // End init_form_fields()






				/**
				 * calculate_shipping function.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping( $package ) {
					$rate = array(
						'id' => $this->id,
						'label' => $this->title,
						'cost' => $this->cost
						//'cost' => '10.99',
						//'calc_tax' => 'per_item'
					);

					// Register the rate
					$this->add_rate( $rate );
				}
			}
		}
	}

	add_action( 'woocommerce_shipping_init', 'novaposhta_shipping_method_init' );

	function add_novaposhta_shipping_method( $methods ) {
		$methods[] = 'WC_Novaposhta_Shipping_Method';
		return $methods;
	}

	add_filter( 'woocommerce_shipping_methods', 'add_novaposhta_shipping_method' );
}
?>
