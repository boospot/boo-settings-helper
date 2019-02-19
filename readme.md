# Boo Settings Helper
This is a helper class for WordPress Settings API

## Hook

Hook into `admin_menu`

## Sample Config Array

```
	$config_array_plain = array(
		'prefix'   => 'plugin_name_',
		'tabs'     => true,
		'menu'     =>
			array(
				'page_title' => __( 'Plugin Name Settings', 'plugin-name' ),
				'menu_title' => __( 'Plugin Name', 'plugin-name' ),
				'capability' => 'manage_options',
				'slug'       => 'plugin-name',
				'icon'       => 'dashicons-performance',
				'position'   => 10,
				'parent'     => 'options-general.php',
				'submenu'    => true,

			),
		'sections' =>
			array(
				array(
					'id'    => 'plugin_name_general_section',
					'title' => __( 'General Settings', 'plugin-name' ),
					'desc'  => __( 'These are general settings for Plugin Name', 'plugin-name' ),
//                        'callback' => function(){echo "Hi" ;}
				),
				array(
					'id'    => 'plugin_name_advance_section',
					'title' => __( 'Advanced Settings', 'plugin-name' ),
					'desc'  => __( 'These are advance settings for Plugin Name', 'plugin-name' )
				)
			),
		'fields'   => array(
			'plugin_name_general_section' => array(
				array(
					'id'    => 'plugin_name_test_field',
					'label' => __( 'Test Field', 'plugin-name' ),
				),
				array(
					'id'    => 'plugin_name_url_field',
					'label' => __( 'URL Field', 'plugin-name' ),
					'type'  => 'url',
				),
				array(
					'id'    => 'color_test',
					'label' => __( 'Color Field', 'plugin-name' ),
					'type'  => 'color',
				),
				array(
					'id'                => 'number_field_id',
					'label'             => __( 'Number Input', 'plugin-name' ),
					'desc'              => __( 'Number field with validation callback `floatval`', 'plugin-name' ),
					'placeholder'       => __( '1.99', 'plugin-name' ),
					'min'               => 0,
					'max'               => 99,
					'step'              => '0.01',
					'type'              => 'number',
					'default'           => '50',
					'sanitize_callback' => 'floatval'
				),
				array(
					'id'          => 'textarea_field_id',
					'label'       => __( 'Textarea Input', 'plugin-name' ),
					'desc'        => __( 'Textarea description', 'plugin-name' ),
					'placeholder' => __( 'Textarea placeholder', 'plugin-name' ),
					'type'        => 'textarea'
				),
				array(
					'id'   => 'html',
					'desc' => __( 'HTML area description. You can use any <strong>bold</strong> or other HTML elements.', 'plugin-name' ),
					'type' => 'html'
				),
				array(
					'id'    => 'checkbox_field_id',
					'label' => __( 'Checkbox', 'plugin-name' ),
					'desc'  => __( 'Checkbox Label', 'plugin-name' ),
					'type'  => 'checkbox',
					''
				),

				array(
					'id'      => 'multi_field_id',
					'label'   => __( 'Multicheck', 'plugin-name' ),
					'desc'    => __( 'A radio button', 'plugin-name' ),
					'type'    => 'multicheck',
					'options' => array(
						'multi_1' => 'Radio 1',
						'multi_2' => 'Radio 2',
						'multi_3' => 'Radio 3'
					),
					'default' => array(
						'multi_1' => 'multi_1',
						'multi_3' => 'multi_3'
					)
				),
				array(
					'id'      => 'radio_field_id',
					'label'   => __( 'Radio Button', 'plugin-name' ),
					'desc'    => __( 'A radio button', 'plugin-name' ),
					'type'    => 'radio',
					'options' => array(
						'radio_1' => 'Radio 1',
						'radio_2' => 'Radio 2',
						'radio_3' => 'Radio 3'
					),
					'default' => array(
						'radio_1' => 'radio_1',
						'radio_2' => 'radio_2'
					)
				),

				array(
					'id'      => 'select_field_id',
					'label'   => __( 'A Dropdown Select', 'plugin-name' ),
					'desc'    => __( 'Dropdown description', 'plugin-name' ),
					'type'    => 'select',
					'default' => 'option_2',
					'options' => array(
						'option_1' => 'Option 1',
						'option_2' => 'Option 2',
						'option_3' => 'Option 3'
					),
				),

				array(
					'id'      => 'pages_field_id',
					'label'   => __( 'Pages Field Type', 'plugin-name' ),
					'desc'    => __( 'List of Pages', 'plugin-name' ),
					'type'    => 'pages',
					'options' => array(
						'post_type' => 'post'
					),
				),

				array(
					'id'      => 'posts_field_id',
					'label'   => __( 'Posts Field Type', 'plugin-name' ),
//						'desc'    => __( 'List of Posts', 'plugin-name' ),
					'type'    => 'posts',
					'options' => array(
						'post_type' => 'post'
					),

				),

				array(
					'id'      => 'password_field_id',
					'label'   => __( 'Password Field', 'plugin-name' ),
					'desc'    => __( 'Password description', 'plugin-name' ),
					'type'    => 'password',
					'default' => '',
				),
				array(
					'id'      => 'file_field_id',
					'label'   => __( 'File', 'plugin-name' ),
					'desc'    => __( 'File description', 'plugin-name' ),
					'type'    => 'file',
					'default' => '',
					'options' => array(
						'btn' => 'Get it'
					)
				),
				array(
					'id'      => 'media_field_id',
					'label'   => __( 'Media', 'plugin-name' ),
					'desc'    => __( 'Media', 'plugin-name' ),
					'type'    => 'media',
					'default' => '',
					'options' => array(
						'btn'       => 'Get the image',
						'width'     => 900,
//							'height'    => 300,
						'max_width' => 900
					)

				)
			),
			'plugin_name_advance_section' => array(
				array(
					'id'                => 'text_field_id',
					'label'             => __( 'Another Text', 'plugin-name' ),
					'desc'              => __( 'with sanitize callback "absint"', 'plugin-name' ),
					'placeholder'       => __( 'Text Input placeholder', 'plugin-name' ),
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'absint'
				),
			)
		),
		'links'    => array(
			'plugin_basename' => plugin_basename( __FILE__ ),
			'action_links'    => true,
		),
	);

	$settings_helper = new Boo_Settings_Helper( $config_array_plain );

```
