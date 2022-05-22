<?php

namespace Vivasoft\WpSync;

/**
 * Plugin Name: WP Sync
 */

require_once 'vendor/autoload.php';

if (!function_exists("register_activation_hook")) {
    exit();
}

class WPSync
{
    public static function Start()
    {
        // Activation hook
        register_activation_hook(__FILE__, 'WPSync::activate');

        // Settings
        $settings_menu = new SettingsMenu();
        add_action('admin_menu', array($settings_menu, 'wp_sync_add_plugin_page'));
        add_action('admin_init', array($settings_menu, 'wp_sync_page_init'));

        // Endpoint
        add_action('rest_api_init', function () {
            register_rest_route('wp-sync/v1', 'sync', [
                'methods' => 'POST',
                'callback' => '\Vivasoft\WPSync\WPSync::sync',
                'permission_callback' => '__return_true',
            ]);
        });
    }

    public static function sync()
    {
        Logger::debug(null, "Endpoint Called!");

        $sync_result = GitHub::sync();
        if (!$sync_result) {
            return false;
        }

        // $inputJSON = file_get_contents('php://input');
        // Logger::debug("Input", $inputJSON);
    }

    public static function activate()
    {
        Logger::debug(null, "Activation");
    }
}

add_action('init', '\Vivasoft\WpSync\WPSync::Start');
