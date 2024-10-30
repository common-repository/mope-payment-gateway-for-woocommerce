<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/*
Plugin Name: Mopé Payment Gateway
Plugin URI: https://github.com/HKBLab/mope
Description: A Mopé Payment Gateway Plugin for WooCommerce
Version: 2.0.4
Author: Plauto Rafael De Moura Da Silva
Author URI: https://github.com/HKBLab
Requires at least: 5.1
Requires PHP: 7.2
License: GPLv2 or later
*/

add_filter('woocommerce_payment_gateways', 'mope_add_gateway_class');
function mope_add_gateway_class($gateways) {
    $gateways[] = 'WC_Mope_Gateway';
    return $gateways;
}

add_action('plugins_loaded', 'mope_init_gateway_class');
function mope_init_gateway_class() {

    class WC_Mope_Gateway extends WC_Payment_Gateway {

        private $test_mode;
        private $mope_api_key;
        private $mope_api_base_url;
        private $transaction_description;
        private $custom_wc_request_config;

        public function __construct() {
            $this->id = 'mope';
            $this->icon = esc_url(plugins_url('assets/mope_logo.png', __FILE__));
            $this->has_fields = true;
            $this->method_title = 'Mopé Payment Gateway';
            $this->method_description = 'Pay quickly and securely with Mopé Mobile wallets.';

            $this->supports = array('products');

            // Init form fields and settings
            $this->init_form_fields();
            $this->init_settings();

            $this->title = sanitize_text_field($this->get_option('title'));
            $this->description = sanitize_text_field($this->get_option('description'));
            $this->enabled = $this->get_option('enabled');
            $this->test_mode = 'yes' === $this->get_option('test_mode');
            $this->mope_api_key = $this->test_mode ? sanitize_text_field($this->get_option('test_private_key')) : sanitize_text_field($this->get_option('private_key'));
            $this->mope_api_base_url = sanitize_text_field($this->get_option('mope_api_base_url'));
            $this->transaction_description = sanitize_text_field($this->get_option('transaction_description'));

            $this->custom_wc_request_config = array(
                'timeout' => 5,
                'headers' => array(
                    'User-Agent' => 'Mopé Php Client',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . sanitize_text_field($this->mope_api_key),
                ),
            );

            // Save settings in admin
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            // Add listener for the callback
            add_action('woocommerce_api_mope', array($this, 'mope_callback'));
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => 'Enable',
                    'label' => 'Enable/disable Mopé Payment Gateway',
                    'type' => 'checkbox',
                    'description' => 'Make this payment method available to your users.',
                    'default' => 'no',
                ),
                'title' => array(
                    'title' => 'Payment method title',
                    'type' => 'text',
                    'description' => 'Title for this payment method displayed at checkout.',
                    'default' => 'Mopé Mobile Wallet',
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => 'Description',
                    'type' => 'textarea',
                    'description' => 'Description of this payment gateway.',
                    'default' => 'Pay quickly and securely with your Mopé Mobile wallet.',
                ),
                'mope_api_base_url' => array(
                    'title' => 'Mope API Base URL',
                    'type' => 'text',
                    'default' => 'https://api.mope.sr',
                    'description' => 'This Base URL will be used for all Mope API calls.',
                ),
                'private_key' => array(
                    'title' => 'Live Mopé Token Key',
                    'type' => 'text',
                ),
                'test_mode' => array(
                    'title' => 'Test mode',
                    'label' => 'Enable Test Mode',
                    'type' => 'checkbox',
                    'description' => 'Enable Test Mode with test token key.',
                    'default' => 'no',
                    'desc_tip' => true,
                ),
                'test_private_key' => array(
                    'title' => 'Test Mopé Token Key',
                    'type' => 'text',
                ),
                'transaction_description' => array(
                    'title' => 'Transaction Description',
                    'type' => 'text',
                    'default' => 'Purchase at ' . get_bloginfo('name'),
                    'description' => 'Transaction description in buyer\'s Mopé Wallet.',
                ),
            );
        }

        public function process_payment($order_id) {
            $order = wc_get_order($order_id);
            $order_total = number_format(preg_replace("/(?<!\d)([,.])(?!\d)/", "", $order->get_total()), 2, '', '');
            $return_url = site_url() . '?wc-api=mope&order_id=' . $order_id;

            $data = array(
                'description' => $this->transaction_description,
                'amount' => $order_total,
                'order_id' => $order_id,
                'currency' => get_woocommerce_currency(),
                'redirect_url' => $return_url,
            );

            $response = wp_remote_post($this->mope_api_base_url . '/api/shop/payment_request', array_merge(array('body' => wp_json_encode($data)), $this->custom_wc_request_config));

            if (is_wp_error($response)) {
                wc_add_notice("Payment error: " . $response->get_error_message(), 'error');
                return array('result' => 'error', 'redirect' => wc_get_checkout_url());
            }

            $response_code = wp_remote_retrieve_response_code($response);
            if ($response_code != 201) {
                wc_add_notice("An error occurred. Please try again.", 'error');
                return array('result' => 'error', 'redirect' => wc_get_checkout_url());
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body);

            if (empty($data->url)) {
                wc_add_notice("Error processing payment. Try again later.", 'error');
                return array('result' => 'error', 'redirect' => wc_get_checkout_url());
            }

            $order->update_meta_data('mope_payment_id', $data->id);
            $order->save();

            return array('result' => 'success', 'redirect' => $data->url);
        }
		public function mope_callback() {
			if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
				wc_add_notice('Invalid Order ID', 'error');
				$this->redirect_to_cart();
			}

			$order_id = sanitize_text_field($_GET['order_id']);
			$order = wc_get_order($order_id);

			if (!$order) {
				wc_add_notice('Order not found', 'error');
				$this->redirect_to_cart();
			}

			$payment_id = $order->get_meta('mope_payment_id');
			if (!$payment_id) {
				wc_add_notice('Payment ID not found', 'error');
				$this->redirect_to_cart();
			}

			$response = wp_remote_get($this->mope_api_base_url . '/api/shop/payment_request/' . $payment_id, $this->custom_wc_request_config);
			if (is_wp_error($response)) {
				wc_add_notice("Error: " . $response->get_error_message(), 'error');
				$this->redirect_to_cart();
			}

			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body);

			if ($data->status == 'paid') {
				// Mark order as 'completed' instead of 'processing'
				$order->update_status('completed', 'Payment received, order marked as completed.');
				
				wc_reduce_stock_levels($order_id); // If applicable, reduce stock

				wp_safe_redirect($order->get_checkout_order_received_url());
				exit;
			} else {
				wc_add_notice('Payment failed or pending.', 'error');
				$this->redirect_to_cart();
			}
		}


        private function redirect_to_cart() {
            wp_safe_redirect(wc_get_cart_url());
            exit;
        }
    }
}
