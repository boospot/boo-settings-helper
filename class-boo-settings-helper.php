<?php
/**
 * Name:        Boo Settings API helper class
 *
 * Version:     5.3
 * Author:      RaoAbid | BooSpot
 *
 * @author RaoAbid | BooSpot
 * @link https://github.com/boospot/boo-settings-helper
 */
if ( ! class_exists( 'Boo_Settings_Helper' ) ):

	class Boo_Settings_Helper {

		public $debug = false;

		public $log = false;

		public $plugin_basename = '';

		public $action_links = array();

		public $config_menu = array();

		public $field_types = array();

		protected $is_tabs = false;

		public $slug;

		protected $active_tab;

		protected $sections_count;

		protected $sections_ids;

		protected $fields_ids;

		// flag for options processing
		protected $is_settings_saved_once = false;

		protected $sanitized_data;

		protected $prefix = '';

		protected $is_simple_options = true;

		/**
		 * settings sections array
		 *
		 * @var array
		 */
		protected $settings_sections = array();

		/**
		 * Settings fields array
		 *
		 * @var array
		 */
		protected $settings_fields = array();

		public function __construct( $config_array = null ) {

			if ( ! empty( $config_array ) && is_array( $config_array ) ) {
				$this->set_properties( $config_array );
			}

			add_action( 'admin_init', array( $this, 'admin_init' ) );

		}


		public function setup_hooks() {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		}

		protected function get_default_config() {

			return array(
				'tabs' => false,
				'menu' => $this->get_default_config_menu(),

			);

		}

		/**
		 * Set Properties of the class
		 */
		protected function set_properties( array $config_array ) {

			// Normalise config array
			$config_array = wp_parse_args( $config_array, $this->get_default_config() );

			if ( isset( $config_array['prefix'] ) ) {
				$this->prefix = ( ! empty( $config_array['prefix'] ) ) ? sanitize_key( $config_array['prefix'] ) : '';
			}

			if ( isset( $config_array['tabs'] ) ) {
				$this->set_tabs( $config_array['tabs'] );
			}

			// Do we have menu config, if yes, call the method
			if ( isset( $config_array['menu'] ) ) {
				$this->set_menu( $config_array['menu'] );
			}

			// Do we have sections config, if yes, call the method
			if ( isset( $config_array['sections'] ) ) {
				$this->set_sections( $config_array['sections'] );
			}

			// Do we have fields config, if yes, call the method
			if ( isset( $config_array['fields'] ) ) {
				$this->set_fields( $config_array['fields'] );
			}

			if ( isset( $config_array['links'] ) ) {
				$this->set_links( $config_array['links'] );
			}

			$this->set_active_tab();


		}

		/**
		 *
		 */
		public function set_active_tab() {
			$this->active_tab =
				( isset( $_GET['tab'] ) )
					? sanitize_key( $_GET['tab'] )
					: $this->settings_sections[0]['id'];
		}

		public function set_links( array $config_links ) {

			if (
				isset( $config_links['plugin_basename'] ) &&
				! empty( $config_links['plugin_basename'] )
			) {
				$this->plugin_basename = $config_links['plugin_basename'];
				$this->action_links    = isset( $config_links['action_links'] ) ? $config_links['action_links'] : true;

				$prefix = is_network_admin() ? 'network_admin_' : '';

				add_filter(
					"{$prefix}plugin_action_links_{$this->plugin_basename}",
					array( $this, 'plugin_action_links' ),
					10, // priority
					4   // parameters
				);
			}

		}

		public function get_default_settings_url() {

			if ( $this->config_menu['submenu'] ) {
				$options_base_file_name = $this->config_menu['parent'];
				if ( in_array( $options_base_file_name, array(
					'options-general.php',
					'edit-comments.php',
					'plugins.php',
					'edit.php',
					'upload.php',
					'themes.php',
					'users.php',
					'tools.php'
				) ) ) {
					return admin_url( "{$options_base_file_name}?page={$this->config_menu['slug']}" );
				} else {
					return admin_url( "admin.php?page={$this->config_menu['slug']}" );
				}
			} else {
				return admin_url( "admin.php?page={$this->config_menu['slug']}" );
			}


		}

		public function get_default_settings_link() {

			return array(
				'<a href="' . $this->get_default_settings_url() . '">' . __( 'Settings' ) . '</a>',
			);

		}

		/**
		 * Register "settings" for plugin option page in plugins list
		 *
		 * @param array $links plugin links
		 *
		 * @return array possibly modified $links
		 */
		public function plugin_action_links( $links, $plugin_file, $plugin_data, $context ) {
			/**
			 * Documentation :
			 * https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
			 */

			// BOOL of settings is given true | false
			if ( is_bool( $this->action_links ) ) {

				// FALSE: If it is false, no need to go further
				if ( ! $this->action_links ) {
					return $links;
				}
				// TRUE: if Settings link is not defined, lets create one
				if ( $this->action_links ) {
					return array_merge( $links, $this->get_default_settings_link() );
				}

			} // if ( is_bool( $this->config['settings_link'] ) )


			// Admin URL of settings is given
			if ( ! is_bool( $this->action_links ) && ! is_array( $this->action_links ) ) {

				$settings_link = array(
					'<a href="' . admin_url( esc_url( $this->action_links ) ) . '">' . __( 'Settings' ) . '</a>',
				);

				return array_merge( $settings_link, $links );
			}

			// Array of settings_link is given
			if ( is_array( $this->action_links ) ) {

				$settings_link_array = array();

				foreach ( $this->action_links as $link ) {

					$link_text         = isset( $link['text'] ) ? sanitize_text_field( $link['text'] ) : __( 'Settings', '' );
					$link_url_un_clean = isset( $link['url'] ) ? $link['url'] : '#';

					$link_type = isset( $link['type'] ) ? sanitize_key( $link['type'] ) : 'default';

					switch ( $link_type ) {
						case ( 'external' ):
							$link_url = esc_url_raw( $link_url_un_clean );
							break;

						case ( 'internal' ):
							$link_url = admin_url( esc_url( $link_url_un_clean ) );
							break;

						default:

							$link_url = $this->get_default_settings_url();

					}

					$settings_link_array[] = '<a href="' . $link_url . '">' . $link_text . '</a>';

				}

				return array_merge( $settings_link_array, $links );


			} // if (  $this->action_links ) )

			// if nothing is returned so far, return original $links
			return $links;

		}

		public function set_tabs( $config_tabs ) {
			$this->is_tabs = ( (bool) $config_tabs ) ? true : false;
		}

		/**
		 * Register plugin option page
		 */
		public function set_menu( $config_menu ) {

			$this->config_menu = array_merge_recursive( $this->config_menu, wp_parse_args( $config_menu, $this->get_default_config_menu() ) );

			$this->slug = $this->config_menu['slug'] =
				isset( $this->config_menu['slug'] )
					? sanitize_key( $this->config_menu['slug'] )
					: sanitize_title( $this->config_menu['page_title'] );


			// Is it a main menu or sub_menu
			if ( ! $this->config_menu['submenu'] ) {

				add_menu_page(
					$this->config_menu['page_title'],
					$this->config_menu['menu_title'],
					$this->config_menu['capability'],
					$this->config_menu['slug'], //slug
					array( $this, 'display_page' ),
					$this->config_menu['icon'],
					$this->config_menu['position']
				);

			} else {

				add_submenu_page(
					$this->config_menu['parent'],
					$this->config_menu['page_title'],
					$this->config_menu['menu_title'],
					$this->config_menu['capability'],
					$this->config_menu['slug'], // slug
					array( $this, 'display_page' )
				);

			}

		}

		/**
		 * Get default config for menu
		 * @return array $default
		 */
		public function get_default_config_menu() {

			return apply_filters( 'boo_settings_filter_default_menu_array', array(
				//The name of this page
				'page_title' => __( 'Plugin Options' ),
				// //The Menu Title in Wp Admin
				'menu_title' => __( 'Plugin Options' ),
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

		//DEBUG
		public function write_log( $type, $log_line ) {

			$hash        = '';
			$fn          = plugin_dir_path( __FILE__ ) . '/' . $type . '-' . $hash . '.log';
			$log_in_file = file_put_contents( $fn, date( 'Y-m-d H:i:s' ) . ' - ' . $log_line . PHP_EOL, FILE_APPEND );

		}

		/*
		 * @return array configured field types
		 */
		public function get_field_types() {

			foreach ( $this->settings_fields as $sections_fields ) {
				foreach ( $sections_fields as $field ) {
					$this->field_types[] = isset( $field['type'] ) ? sanitize_key( $field['type'] ) : 'text';
				}
			}

			return array_unique( $this->field_types );
		}


		/**
		 * @return bool true if its plugin options page is loaded
		 */
		protected function is_menu_page_loaded() {

			$current_screen = get_current_screen();

			return substr( $current_screen->id, - strlen( $this->config_menu['slug'] ) ) === $this->config_menu['slug'];

		}

		/**
		 * Enqueue scripts and styles
		 */
		function admin_enqueue_scripts() {

			// Conditionally Load scripts and styles for field types configured

			// Load scripts for only plugin menu page
			if ( ! $this->is_menu_page_loaded() ) {
				return null;
			}

			// Load Color Picker if required
			if ( in_array( 'color', $this->get_field_types() ) ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
			}

			if ( in_array( 'media', $this->get_field_types() ) || in_array( 'file', $this->get_field_types() )  ) {
				wp_enqueue_media();
			}
			wp_enqueue_script( 'jquery' );

		}

		/**
		 * Set settings sections
		 *
		 * @param array $sections setting sections array
		 */
		function set_sections( array $sections ) {

			$this->settings_sections = array_merge_recursive( $this->settings_sections, $sections );

			$this->sections_count = count( $this->settings_sections );

			$this->sections_ids = array_values( $this->settings_sections );

			return $this;
		}

		/**
		 * Set settings fields
		 *
		 * @param array $fields settings fields array
		 */
		public function set_fields( $fields ) {
			$this->settings_fields = array_merge_recursive( $this->settings_fields, $fields );
			$this->normalize_fields();
			$this->setup_hooks();

			return $this;

		}

		public function get_markup_placeholder( $placeholder ) {
			return ' placeholder="' . esc_html( $placeholder ) . '" ';
		}


		public function get_sanitize_callback_method( $type ) {

			return ( method_exists( $this, "sanitize_{$type}" ) )
				? array( $this, "sanitize_{$type}" )
				: array( $this, "sanitize_text" );

		}

		public function get_field_markup_callback_method( $type ) {

			return ( method_exists( $this, "callback_{$type}" ) )
				? array( $this, "callback_{$type}" )
				: array( $this, "callback_text" );

		}

		public function get_field_markup_callback_name( $type ) {

			return ( method_exists( $this, "callback_{$type}" ) )
				? "callback_{$type}"
				: "callback_text";


		}

		public function get_default_field_args( $field, $section = 'default' ) {

			$type = isset( $field['type'] ) ? $field['type'] : 'text';

			return array(
				'id'                => $field['id'],
				'label'             => '',
				'desc'              => '',
				'type'              => 'text',
				'placeholder'       => '',
				'default'           => '',
				'options'           => array(),
				'callback'          => '',
				'sanitize_callback' => '',
				'value'             => '',
				'show_in_rest'      => true,
				'class'             => $field['id'],
				'std'               => '',
				'size'              => 'regular',
				// Auto Calculated
				'name'              => $this->prefix . $field['id'],
				'label_for'         => $this->prefix . $field['id'],
				'section'           => $section,

			);
		}

		public function normalize_fields() {

			foreach ( $this->settings_fields as $section_id => $fields ) {
				if ( is_array( $fields ) && ! empty( $fields ) ) {
					foreach ( $fields as $i => $field ) {
						$this->settings_fields[ $section_id ][ $i ] =
							wp_parse_args(
								$field,
								$this->get_default_field_args( $field, $section_id )
							);
					}
				}

			}


		}


		/**
		 *
		 */
		public function get_page_id_for_sections( $section_id = '' ) {
//			return $this->config_menu['slug'] . '_' . $section_id;
			return $this->config_menu['slug'];
		}

		public function get_options_group( $section_id = '' ) {
			return str_replace( '-', '_', $this->slug ) . "_" . $section_id;
//			return str_replace( '-', '_', $this->slug );
		}

		function display_page() {

			// Save Default options in DB with default values
//			$this->set_default_db_options();

			if ( 'options-general.php' != $this->config_menu['parent'] ) {
				settings_errors();
			}

			echo '<div class="wrap">';
			echo "<h1>" . get_admin_page_title() . "</h1>";

			// If Debug is ON
			if ( $this->debug ) {
				echo "<b>TYPES of fields</b>";
				$this->var_dump_pretty( $this->get_field_types() );

				if ( $this->is_tabs ) {
					echo "<b>Active Tab Options Array</b>";
					$this->var_dump_pretty( get_option( $this->active_tab ) );

				}
			}

			?>
            <div class="metabox-holder">
				<?php
				if ( $this->is_tabs ) {
					$this->show_navigation();
				}
				?>
                <form method="post" action="options.php">
					<?php

					if ( $this->is_tabs ) {
						foreach ( $this->settings_sections as $section ) :
							if ( $section['id'] !== $this->active_tab ) {
								continue;
							}

							// for tabs
							settings_fields( $this->get_options_group( $section['id'] ) );
							do_settings_sections( $this->get_page_id_for_sections( $section['id'] ) );
						endforeach; // end foreach

					} else {
						// for tab-less
						settings_fields( $this->get_options_group() );
						do_settings_sections( $this->get_page_id_for_sections() );

					}

					?>
                    <div style="padding-left: 10px">
						<?php submit_button(); ?>
                    </div>

                </form>
            </div>
			<?php

			// Call General Scripts
			$this->script_general();
			?>
            </div>
			<?php
		}

		public function add_settings_section() {

			//register settings sections
			foreach ( $this->settings_sections as $section ) {

				if ( $this->is_tabs ) {
					if ( $section['id'] !== $this->active_tab ) {
						continue;
					}
				}

				// Callback for Section Description
				if ( isset( $section['callback'] ) && is_callable( $section['callback'] ) ) {
					$callback = $section['callback'];
				} else if ( isset( $section['desc'] ) && ! empty( $section['desc'] ) ) {
					$callback = function () use ( $section ) {
						echo "<div class='inside'>" . esc_html( $section['desc'] ) . "</div>";
					};
				} else {
					$callback = null;
				}

				add_settings_section(
					$section['id'],
					$section['title'],
					$callback,
					$this->get_page_id_for_sections( $section['id'] ) // page
				);

			}

		}

		public function add_settings_field_loop() {

			//register settings fields
			foreach ( $this->settings_fields as $section_id => $fields ) {

				if ( $this->is_tabs ) {
					if ( $section_id !== $this->active_tab ) {
						continue;
					}
				}

				foreach ( $fields as $field ) :

					$field['value'] = get_option( $field['name'], $field['default'] );

					add_settings_field(
						$field['name'],
						$field['label'],
						( is_callable( $field['callback'] ) )
							? $field['callback']
							: $this->get_field_markup_callback_method( $field['type'] ),
						$this->get_page_id_for_sections( $section_id ), // page
						$section_id, // section
						$field  // args
					);
				endforeach;
			}

		}

		public function register_settings() {
			// creates our settings in the options table
			foreach ( $this->settings_fields as $section_id => $fields ) :

				foreach ( $fields as $field ) :

					register_setting(
						( $this->is_tabs ) ?
							$this->get_options_group( $field['section'] )
							: $this->get_options_group(), // options_group
						$field['name'], // options_id
						array(
//								'type'              => $field['type'],
							'description'       => $field['desc'],
							'sanitize_callback' => ( is_callable( $field['sanitize_callback'] ) )
								? $field['sanitize_callback']
								: $this->get_sanitize_callback_method( $field['type'] ),
							'show_in_rest'      => $field['show_in_rest'],
							'default'           => $field['default'],
						)
					);
				endforeach;
			endforeach;
		}


		function sanitize_text( $value ) {
			return ( ! empty( $value ) ) ? sanitize_text_field( $value ) : '';
		}

		function sanitize_number( $value ) {
			return ( is_numeric( $value ) ) ? $value : 0;
		}

		function sanitize_editor( $value ) {
			return wp_kses_post( $value );
		}

		function sanitize_textarea( $value ) {
			return sanitize_textarea_field( $value );
		}

		function sanitize_checkbox( $value ) {
			return ( $value === '1' ) ? 1 : 0;
		}

		function sanitize_select( $value ) {
			return $this->sanitize_text( $value );
		}

		function sanitize_user_role( $value ) {
			return sanitize_key( $value );
		}

		function sanitize_radio( $value ) {
			return $this->sanitize_text( $value );
		}

		function sanitize_multicheck( $value ) {
			return ( is_array( $value ) ) ? array_map( 'sanitize_text_field', $value ) : array();
		}

		function sanitize_color( $value ) {

			if ( false === strpos( $value, 'rgba' ) ) {
				return sanitize_hex_color( $value );
			} else {
				// By now we know the string is formatted as an rgba color so we need to further sanitize it.

				$value = trim( $value, ' ' );
				$red   = $green = $blue = $alpha = '';
				sscanf( $value, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );

				return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
			}
		}

		function sanitize_password( $value ) {

			$password_get_info = password_get_info( $value );

			if ( isset( $password_get_info['algo'] ) && $password_get_info['algo'] ) {
				unset( $password_get_info );

				return $value;
				// do nothing, we have got already stored hashed password
			} else {
				unset( $password_get_info );

				return password_hash( $value, PASSWORD_DEFAULT );
			}

		}

		function sanitize_url( $value ) {
			return esc_url_raw( $value );
		}

		function sanitize_file( $value ) {
//		    TODO: if the option to store file as file url
			return esc_url_raw( $value );
		}

		function sanitize_html( $value ) {
			// nothing to save
			return '';
		}

		function sanitize_posts( $value ) {
			// Only store post id
			return absint( $value );
		}

		function sanitize_pages( $value ) {
			// Only store page id
			return absint( $value );
		}

		function sanitize_media( $value ) {
			// Only store media id
			return absint( $value );
		}

		/**
		 * Displays a text field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_text( $args ) {

			$html = sprintf(
				'<input 
                        type="%1$s" 
                        class="%2$s-text %8$s" 
                        id="%3$s[%4$s]" 
                        name="%7$s" 
                        value="%5$s"
                        %6$s
                        />',
				$args['type'],
				$args['size'],
				$args['section'],
				$args['id'],
				$args['value'],
				$this->get_markup_placeholder( $args['placeholder'] ),
				$args['name'],
				$args['class']
			);
			$html .= $this->get_field_description( $args );

			echo $html;
			unset( $html );
		}


		/**
		 * Initialize and registers the settings sections and fileds to WordPress
		 *
		 * Usually this should be called at `admin_init` hook.
		 *
		 * This function gets the initiated settings sections and fields. Then
		 * registers them to WordPress and ready for use.
		 */
		function admin_init() {

			$this->add_settings_section();
			$this->add_settings_field_loop();
			$this->register_settings();


		}

		/**
		 * Get field description for display
		 *
		 * @param array $args settings field args
		 */
		public function get_field_description( $args ) {
			return sprintf( '<p class="description">%s</p>', $args['desc'] );
		}


		/**
		 * Displays a url field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_url( $args ) {
			$this->callback_text( $args );
		}

		/**
		 * Displays a number field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_number( $args ) {
			$min  = ( isset( $args['options']['min'] ) && ! empty( $args['options']['min'] ) ) ? ' min="' . $args['options']['min'] . '"' : '';
			$max  = ( isset( $args['options']['max'] ) && ! empty( $args['options']['max'] ) ) ? ' max="' . $args['options']['max'] . '"' : '';
			$step = ( isset( $args['options']['step'] ) && ! empty( $args['options']['step'] ) ) ? ' step="' . $args['options']['step'] . '"' : '';

			$html = sprintf(
				'<input
                        type="%1$s"
                        class="%2$s-text"
                        id="%3$s[%4$s]"
                        name="%10$s"
                        value="%5$s"
                        %6$s
                        %7$s
                        %8$s
                        %9$s
                        />',
				$args['type'],
				$args['size'],
				$args['section'],
				$args['id'],
				$args['value'],
				$this->get_markup_placeholder( $args['placeholder'] ),
				$min,
				$max,
				$step,
				$args['name']
			);
			$html .= $this->get_field_description( $args );
			echo $html;

			unset( $html, $min, $max, $step );
		}

		/**
		 * Displays a checkbox for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_checkbox( $args ) {


			$html = '<fieldset>';
			$html .= sprintf( '<label for="%1$s[%2$s]">', $args['section'], $args['id'] );
			$html .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%4$s" value="1" %3$s />', $args['section'], $args['id'], checked( $args['value'], '1', false ), $args['name'] );
			$html .= sprintf( '%1$s</label>', $args['desc'] );
			$html .= '</fieldset>';

			echo $html;
		}

		/**
		 * Displays a multicheckbox for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_multicheck( $args ) {
			$value = $args['value'];
			if ( empty( $value ) ) {
				$value = $args['default'];
			}

			$html = '<fieldset>';
			foreach ( $args['options'] as $key => $label ) {
				$checked = isset( $value[ $key ] ) ? $value[ $key ] : '0';
				$html    .= sprintf( '<label for="%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
				$html    .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%5$s[%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $checked, $key, false ), $args['name'] );
				$html    .= sprintf( '%1$s</label><br>', $label );
			}

			$html .= $this->get_field_description( $args );
			$html .= '</fieldset>';
			echo $html;
			unset( $value, $html );
		}

		/**
		 * Displays a radio button for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_radio( $args ) {

			$value = $args['value'];

			if ( empty( $value ) ) {
				$value = is_array( $args['default'] ) ? $args['default'] : array();
			}

			$html = '<fieldset>';

			foreach ( $args['options'] as $key => $label ) {

				$html .= sprintf( '<label for="%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
				$html .= sprintf( '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%5$s" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $args['value'], $key, false ), $args['name'] );
				$html .= sprintf( '%1$s</label><br>', $label );
			}

			$html .= $this->get_field_description( $args );
			$html .= '</fieldset>';

			echo $html;
			unset( $value, $html );
		}

		/**
		 * Displays a selectbox for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_select( $args ) {

			$html = sprintf( '<select class="%1$s-text %5$s" name="%4$s" id="%2$s[%3$s]">', $args['size'], $args['section'], $args['id'], $args['name'], $args['class'] );

			foreach ( $args['options'] as $key => $label ) {
				$html .=
					sprintf( '<option value="%1s"%2s>%3s</option>',
						$key,
						selected( $args['value'], $key, false ),
						$label
					);
			}

			$html .= sprintf( '</select>' );
			$html .= $this->get_field_description( $args );

			echo $html;
			unset( $html );
		}

		/**
		 * Displays a textarea for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_textarea( $args ) {

			$html = sprintf(
				'<textarea 
                        rows="5" 
                        cols="55" 
                        class="%1$s-text" 
                        id="%2$s" 
                        name="%5$s"
                        %3$s
                        >%4$s</textarea>',
				$args['size'], $args['id'], $this->get_markup_placeholder( $args['placeholder'] ), $args['value'], $args['name'] );
			$html .= $this->get_field_description( $args );

			echo $html;
		}

		/**
		 * Displays the html for a settings field
		 *
		 * @param array $args settings field args
		 *
		 * @return string
		 */
		function callback_html( $args ) {
			echo $this->get_field_description( $args );
		}


		/**
		 * Displays a file upload field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_file( $args ) {

			$label = isset( $args['options']['btn'] )
				? $args['options']['btn']
				: __( 'Select' );

			$html = sprintf( '<input type="url" class="%1$s-text wpsa-url" id="%2$s[%3$s]" name="%5$s" value="%4$s"/>', $args['size'], $args['section'], $args['id'], $args['value'], $args['name'] );
			$html .= '<input type="button" class="button boospot-browse-button" value="' . $label . '" />';
			$html .= $this->get_field_description( $args );

			echo $html;
		}


		/**
		 * Generate: Uploader field
		 *
		 * @param array $args
		 *
		 * @source: https://mycyberuniverse.com/integration-wordpress-media-uploader-plugin-options-page.html
		 */
		public function callback_media( $args ) {

			// Set variables
			$default_image = isset( $args['default'] ) ? esc_url_raw( $args['default'] ) : 'https://www.placehold.it/115x115';
			$max_width     = isset( $args['options']['max_width'] ) ? absint( $args['options']['max_width'] ) : 150;
			$width         = isset( $args['options']['width'] ) ? absint( $args['options']['width'] ) : '';
			$height        = isset( $args['options']['height'] ) ? absint( $args['options']['height'] ) : '';
			$text          = isset( $args['options']['btn'] ) ? sanitize_text_field( $args['options']['btn'] ) : __( 'Upload' );


			$image_size = ( ! empty( $width ) && ! empty( $height ) ) ? array( $width, $height ) : 'thumbnail';

			if ( ! empty( $args['value'] ) ) {
				$image_attributes = wp_get_attachment_image_src( $args['value'], $image_size );
				$src              = $image_attributes[0];
				$value            = $args['value'];
			} else {
				$src   = $default_image;
				$value = '';
			}

			$image_style = ! is_array( $image_size ) ? "style='max-width:100%; height:auto;'" : "style='width:{$width}px; height:{$height}px;'";

			$max_width = $max_width . "px";
			// Print HTML field
			echo '
                <div class="upload" style="max-width:' . $max_width . ';">
                    <img data-src="' . $default_image . '" src="' . $src . '" ' . $image_style . '/>
                    <div>
                        <input type="hidden" name="' . $args['name'] . '" id="' . $args['name'] . '" value="' . $value . '" />
                        <button type="submit" class="boospot-image-upload button">' . $text . '</button>
                        <button type="submit" class="boospot-image-remove button">&times;</button>
                    </div>
                </div>
            ';

			$this->get_field_description( $args );

			// free memory
			unset( $default_image, $max_width, $width, $height, $text, $image_size, $image_style, $value );

		}

		/**
		 * Displays a password field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_password( $args ) {

			$html = sprintf( '<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%5$s" value="%4$s"/>', $args['size'], $args['section'], $args['id'], $args['value'], $args['name'] );
			$html .= $this->get_field_description( $args );

			echo $html;
		}

		/**
		 * Displays a color picker field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_color( $args ) {
			$html = sprintf( '<input type="text" class="%1$s-text wp-color-picker-field" data-alpha="true" id="%2$s[%3$s]" name="%6$s" value="%4$s" data-default-color="%5$s" />', $args['size'], $args['section'], $args['id'], $args['value'], $args['default'], $args['name'] );
			$html .= $this->get_field_description( $args );

			echo $html;
		}


		/**
		 * Displays a select box for creating the pages select box
		 *
		 * @param array $args settings field args
		 */
		function callback_pages( $args ) {
			$size          = $args['size'];
			$css_classes   = $args['class'];
			$dropdown_args = array(
				'selected'         => $args['value'],
				'name'             => $args['name'],
				'id'               => $args['section'] . '[' . $args['id'] . ']',
				'echo'             => 1,
				'show_option_none' => '-- ' . __( 'Select' ) . ' --',
				'class'            => "{$size}-text $css_classes", // string
			);
			wp_dropdown_pages( $dropdown_args );

		}

		/**
		 * Displays a select box for creating the user roles
		 *
		 * @param array $args settings field args
		 */
		function callback_user_roles( $args ) {

			$options        = array(
				'' => '-- ' . __( 'Select' ) . ' --'
			);
			$editable_roles = get_editable_roles();
			foreach ( $editable_roles as $role => $details ) {
				$options[ esc_attr( $role ) ] = translate_user_role( $details['name'] );
			}

			// free memory
			unset( $editable_roles );

			//$args['options'] is required by callback_select()
			$args['options'] = $options;

			$this->callback_select( $args );

		}

		function callback_posts( $args ) {
			$default_args = array(
				'post_type'   => 'post',
				'numberposts' => - 1
			);

			$posts_args = wp_parse_args( $args['options'], $default_args );

			$posts = get_posts( $posts_args );

			$options = array(
				'' => '-- ' . __( 'Select' ) . ' --'
			);

			foreach ( $posts as $post ) :
				setup_postdata( $post );
				$options[ $post->ID ] = esc_html( $post->post_title );
				wp_reset_postdata();
			endforeach;

			// free memory
			unset( $posts, $posts_args, $default_args );

			//$args['options'] is required by callback_select()
			$args['options'] = $options;

			$this->callback_select( $args );

		}

		/**
		 * Show navigations as tab
		 *
		 * Shows all the settings section labels as tab
		 */
		function show_navigation() {

			$settings_page = $this->get_default_settings_url();

			$count = count( $this->settings_sections );

			// don't show the navigation if only one section exists
			if ( $count === 1 ) {
				return;
			}


			$html = '<h2 class="nav-tab-wrapper">';

			foreach ( $this->settings_sections as $tab ) {
				$active_class = ( $tab['id'] == $this->active_tab ) ? 'nav-tab-active' : '';
				$html         .= sprintf( '<a href="%3$s&tab=%1$s" class="nav-tab %4$s" id="%1$s-tab">%2$s</a>', $tab['id'], $tab['title'], $settings_page, $active_class );
			}

			$html .= '</h2>';

			echo $html;
		}

		public function var_dump_pretty( $var ) {
			echo "<pre>";
			var_dump( $var );
			echo "</pre>";
		}

		public function var_export_pretty( $var ) {
			echo "<pre>";
			var_export( $var );
			echo "</pre>";
		}

		/**
		 * Tabbable JavaScript codes & Initiate Color Picker
		 *
		 * This code uses localstorage for displaying active tabs
		 */
		public function script_general() {
			?>
            <script>
                jQuery(document).ready(function ($) {
                    //Initiate Color Picker
                    if ($('.wp-color-picker-field').length > 0) {
                        $('.wp-color-picker-field').wpColorPicker();
                    }


                    // For Files Upload
                    $('.boospot-browse-button').on('click', function (event) {
                        event.preventDefault();

                        var self = $(this);

                        // Create the media frame.
                        var file_frame = wp.media.frames.file_frame = wp.media({
                            title: self.data('uploader_title'),
                            button: {
                                text: self.data('uploader_button_text'),
                            },
                            multiple: false
                        });

                        file_frame.on('select', function () {
                            attachment = file_frame.state().get('selection').first().toJSON();
                            self.prev('.wpsa-url').val(attachment.url).change();
                        });

                        // Finally, open the modal
                        file_frame.open();
                    });


                    // Prevent page navigation for un-saved changes
                    $(function () {
                        var changed = false;

                        $('input, textarea, select, checkbox').change(function () {
                            changed = true;
                        });

                        $('.nav-tab-wrapper a').click(function () {
                            if (changed) {
                                window.onbeforeunload = function () {
                                    return "Changes you made may not be saved."
                                };
                            } else {
                                window.onbeforeunload = '';
                            }
                        });

                        $('.submit :input').click(function () {
                            window.onbeforeunload = '';
                        });
                    });


                    // The "Upload" button
                    $('.boospot-image-upload').click(function () {
                        var send_attachment_bkp = wp.media.editor.send.attachment;
                        var button = $(this);
                        wp.media.editor.send.attachment = function (props, attachment) {
                            $(button).parent().prev().attr('src', attachment.url);
                            if (attachment.id) {
                                $(button).prev().val(attachment.id);
                            }
                            wp.media.editor.send.attachment = send_attachment_bkp;
                        }
                        wp.media.editor.open(button);
                        return false;
                    });

                    // The "Remove" button (remove the value from input type='hidden')
                    $('.boospot-image-remove').click(function () {
                        var answer = confirm('Are you sure?');
                        if (answer == true) {
                            var src = $(this).parent().prev().attr('data-src');
                            $(this).parent().prev().attr('src', src);
                            $(this).prev().prev().val('');
                        }
                        return false;
                    });

                });
            </script>
			<?php
		}

		public function get_settings_sections() {

			return ( ! empty( $this->settings_sections ) ) ? $this->settings_sections : array();

		}

		public function get_settings_fields() {

			return $this->settings_fields;
		}

		public function get_settings_fields_ids() {

			foreach ( $this->settings_fields as $sections_fields ) {
				foreach ( $sections_fields as $field ) {
					$this->fields_ids[] = $field['id'];
				}
			}

			return $this->fields_ids;
		}


	}

endif;
