<?php

/**
 * Boo Settings API helper class
 *
 * @version 1.0
 *
 * @author RaoAbid | BooSpot
 * @link https://github.com/boospot/boo-settings-helper
 */
if ( ! class_exists( 'Boo_Settings_Helper' ) ):
	class Boo_Settings_Helper {


		/*
		 * Menu Configuration array
		 */
		private $config_menu;

		private $is_submenu;

		public $text_domain;

		private $page_slug;

		/**
		 * @param mixed $text_domain
		 */
		public function set_text_domain( $text_domain ) {
			$this->text_domain = $text_domain;
		}

		public function __construct( array $config_array = null ) {


			if ( ! empty( $config_array ) ) {

				$this->set_properties( $config_array );
			}

			$this->setup_hooks();
//			$this->get_default_config_menu();

		}


		/**
		 * Set Properties of the class
		 */
		protected function set_properties( array $config_array ) {

			if ( isset( $config_array['menu'] ) ) {
				$this->set_config_menu( $config_array['menu'] );
			}

		}


		public function set_config_menu( array $config_menu ) {

			// set if it is a sub menu or not
			$this->is_submenu  = ( isset( $config_menu['submenu'] ) && $config_menu['submenu'] ) ? true : false;
			$this->config_menu = wp_parse_args( $config_menu, $this->get_default_config_menu() );
			$this->page_slug = isset($this->config_menu['slug']) ? $this->config_menu['slug'] : sanitize_title( $this->config_menu['page_title']);
//			var_dump( $this->config_menu ); die();
		}

		public function setup_menu(array $config_menu){
			$this->config_menu = $config_menu;
			var_dump( $this->config_menu ); die();
		}


		public function setup_hooks() {

			$this->add_admin_menu();

		}


		/**
		 * Register plugin option page
		 */
		public function add_admin_menu() {

//			add_menu_page( 'test', 'test', 'manage_options', 'test', array( $this, 'display_page' ));

			// Is it a main menu or sub_menu
			if ( ! $this->is_submenu ) {

				add_menu_page(
					$this->config_menu['page_title'],
					$this->config_menu['menu_title'],
					$this->config_menu['capability'],
					$this->page_slug, //slug
					array( $this, 'display_page' ),
					$this->config_menu['icon'],
					$this->config_menu['position']
				);

			} else {

//				$this->config = wp_parse_args( $this->config, $default );

				add_submenu_page(
					$this->config_menu['parent'],
					$this->config_menu['page_title'],
					$this->config_menu['page_title'],
					$this->config_menu['capability'],
					$this->page_slug, // slug
					array( $this, 'display_page' )
				);

			}

		}


		public function display_page() {

			settings_errors();

			echo 'I am display_page()';
		}


		/**
		 * Get default config for menu
		 * @return array $default
		 */
		public function get_default_config_menu() {

			return apply_filters( 'boo_settings_filter_default_menu_array', array(
				//The name of this page
				'page_title' => __( 'Plugin Options', $this->text_domain ),
				// //The Menu Title in Wp Admin
				'menu_title' => __( 'Plugin Options', $this->text_domain ),
				// The capability needed to view the page
				'capability' => 'manage_options',
				// dashicons id or url to icon
				// https://developer.wordpress.org/resource/dashicons/
				'icon'       => '',
				// Required for submenu
				'submenu'    => false,
				// position
				'position'   => 100,
				// For sub menu, we can define parent menu slug (Defaults to Options Page)
				'parent'     => 'options-general.php',
			) );

		}


	}
endif;