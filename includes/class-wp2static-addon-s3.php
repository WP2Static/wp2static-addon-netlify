<?php

class Wp2static_Addon_Netlify {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp2static-addon-netlify';

		$this->load_dependencies();
		$this->define_admin_hooks();
	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp2static-addon-netlify-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp2static-addon-netlify-admin.php';

		$this->loader = new Wp2static_Addon_Netlify_Loader();
	}

	private function define_admin_hooks() {
		$plugin_admin = new Wp2static_Addon_Netlify_Admin( $this->get_plugin_name(), $this->get_version() );

        if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'wp2static')) {
            $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        }
	}

    public function add_deployment_option_to_ui( $deploy_options ) {
        $deploy_options['netlify'] = array('Amazon Netlify');

        return $deploy_options;
    }

    public function load_deployment_option_template( $templates ) {
        $templates[] =  __DIR__ . '/../views/netlify_settings_block.phtml';

        return $templates;
    }

    public function add_deployment_option_keys( $keys ) {
        $new_keys = array(
          'baseUrl-netlify',
          'cfDistributionId',
          'netlifyBucket',
          'netlifyCacheControl',
          'netlifyKey',
          'siteID',
          'netlifyRemotePath',
          'netlifySecret',
        );

        $keys = array_merge(
            $keys,
            $new_keys
        );

        return $keys;
    }

    public function whitelist_deployment_option_keys( $keys ) {
        $whitelist_keys = array(
          'baseUrl-netlify',
          'cfDistributionId',
          'netlifyBucket',
          'netlifyCacheControl',
          'netlifyKey',
          'siteID',
          'netlifyRemotePath',
        );

        $keys = array_merge(
            $keys,
            $whitelist_keys
        );

        return $keys;
    }

    public function add_post_and_db_keys( $keys ) {
        $keys['netlify'] = array(
          'baseUrl-netlify',
          'cfDistributionId',
          'netlifyBucket',
          'netlifyCacheControl',
          'netlifyKey',
          'siteID',
          'netlifyRemotePath',
          'netlifySecret',
        );

        return $keys;
    }

	public function run() {
		$this->loader->run();

        add_filter(
            'wp2static_add_deployment_method_option_to_ui',
            [$this, 'add_deployment_option_to_ui']
        );

        add_filter(
            'wp2static_load_deploy_option_template',
            [$this, 'load_deployment_option_template']
        );

        add_filter(
            'wp2static_add_option_keys',
            [$this, 'add_deployment_option_keys']
        );

        add_filter(
            'wp2static_whitelist_option_keys',
            [$this, 'whitelist_deployment_option_keys']
        );

        add_filter(
            'wp2static_add_post_and_db_keys',
            [$this, 'add_post_and_db_keys']
        );
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}
}
