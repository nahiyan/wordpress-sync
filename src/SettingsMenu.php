<?php

namespace Vivasoft\WpSync;

class SettingsMenu
{
    private $wp_sync_options;
    public function wp_sync_add_plugin_page()
    {
        add_options_page(
            'WP Sync', // page_title
            'WP Sync', // menu_title
            'manage_options', // capability
            'wp-sync', // menu_slug
            array($this, 'wp_sync_create_admin_page') // function
        );
    }

    public function wp_sync_create_admin_page()
    {
        $this->wp_sync_options = get_option(Config::$optionsName);?>

		<div class="wrap">
			<h2>WP Sync</h2>
			<p></p>
			<?php settings_errors();?>

			<form method="post" action="options.php">
				<?php
settings_fields('wp_sync_option_group');
        do_settings_sections('wp-sync-admin');
        submit_button();
        ?>
			</form>
		</div>
	<?php }

    public function wp_sync_page_init()
    {
        register_setting(
            'wp_sync_option_group', // option_group
            Config::$optionsName, // option_name
            array($this, 'wp_sync_sanitize') // sanitize_callback
        );

        add_settings_section(
            'wp_sync_setting_section', // id
            'Settings', // title
            array($this, 'wp_sync_section_info'), // callback
            'wp-sync-admin' // page
        );

        add_settings_field(
            'github_repository_url_0', // id
            'GitHub Repository URL', // title
            array($this, 'github_repository_url_0_callback'), // callback
            'wp-sync-admin', // page
            'wp_sync_setting_section' // section
        );

        add_settings_field(
            'github_repository_branch_1', // id
            'GitHub Repository Branch', // title
            array($this, 'github_repository_branch_1_callback'), // callback
            'wp-sync-admin', // page
            'wp_sync_setting_section' // section
        );
    }

    public function wp_sync_sanitize($input)
    {
        $sanitary_values = array();
        if (isset($input['github_repository_url_0'])) {
            $sanitary_values['github_repository_url_0'] = sanitize_text_field($input['github_repository_url_0']);
        }

        if (isset($input['github_repository_branch_1'])) {
            $sanitary_values['github_repository_branch_1'] = sanitize_text_field($input['github_repository_branch_1']);
        }

        return $sanitary_values;
    }

    public function wp_sync_section_info()
    {

    }

    public function github_repository_url_0_callback()
    {
        printf(
            '<input class="regular-text" type="text" name="wp_sync_options[github_repository_url_0]" id="github_repository_url_0" value="%s">',
            isset($this->wp_sync_options['github_repository_url_0']) ? esc_attr($this->wp_sync_options['github_repository_url_0']) : ''
        );
    }

    public function github_repository_branch_1_callback()
    {
        printf(
            '<input class="regular-text" type="text" name="wp_sync_options[github_repository_branch_1]" id="github_repository_branch_1" value="%s">',
            isset($this->wp_sync_options['github_repository_branch_1']) ? esc_attr($this->wp_sync_options['github_repository_branch_1']) : ''
        );
    }
}