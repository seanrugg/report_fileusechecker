<?php
/**
 * File Use Checker Report - Admin Settings
 *
 * Provides admin configuration page for the File Use Checker plugin.
 * Settings can be accessed via Site Administration > Plugins > Reports > File Use Checker
 *
 * @package    report_fileusechecker
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Only add settings if the plugin is installed
if (!during_initial_install()) {
    $settings = new admin_settingpage('report_fileusechecker_settings',
        new lang_string('pluginname', 'report_fileusechecker'));

    // Add settings to the admin menu
    $ADMIN->add('reports', $settings);

    // ========================================
    // General Settings Section
    // ========================================

    $settings->add(new admin_setting_heading(
        'report_fileusechecker/general_settings',
        new lang_string('general_settings', 'report_fileusechecker'),
        new lang_string('general_settings_desc', 'report_fileusechecker')
    ));

    // Enable/Disable the plugin
    $settings->add(new admin_setting_configcheckbox(
        'report_fileusechecker/enabled',
        new lang_string('enabled', 'report_fileusechecker'),
        new lang_string('enabled_desc', 'report_fileusechecker'),
        1
    ));

    // ========================================
    // Scanning Settings Section
    // ========================================

    $settings->add(new admin_setting_heading(
        'report_fileusechecker/scanning_settings',
        new lang_string('scanning_settings', 'report_fileusechecker'),
        new lang_string('scanning_settings_desc', 'report_fileusechecker')
    ));

    // Cache TTL (Time To Live)
    $settings->add(new admin_setting_configtext(
        'report_fileusechecker/cache_ttl',
        new lang_string('cache_ttl', 'report_fileusechecker'),
        new lang_string('cache_ttl_desc', 'report_fileusechecker'),
        300,  // Default: 5 minutes
        PARAM_INT,
        4  // Display width in form
    ));

    // Maximum files to scan per request
    $settings->add(new admin_setting_configtext(
        'report_fileusechecker/max_files_per_scan',
        new lang_string('max_files_per_scan', 'report_fileusechecker'),
        new lang_string('max_files_per_scan_desc', 'report_fileusechecker'),
        5000,  // Default: 5000 files
        PARAM_INT,
        5
    ));

    // File size threshold for warning
    $settings->add(new admin_setting_configtext(
        'report_fileusechecker/file_size_threshold',
        new lang_string('file_size_threshold', 'report_fileusechecker'),
        new lang_string('file_size_threshold_desc', 'report_fileusechecker'),
        1048576,  // Default: 1 MB (in bytes)
        PARAM_INT,
        10
    ));

    // ========================================
    // Deletion Settings Section
    // ========================================

    $settings->add(new admin_setting_heading(
        'report_fileusechecker/deletion_settings',
        new lang_string('deletion_settings', 'report_fileusechecker'),
        new lang_string('deletion_settings_desc', 'report_fileusechecker')
    ));

    // Enable/Disable file deletion
    $settings->add(new admin_setting_configcheckbox(
        'report_fileusechecker/allow_deletion',
        new lang_string('allow_deletion', 'report_fileusechecker'),
        new lang_string('allow_deletion_desc', 'report_fileusechecker'),
        1
    ));

    // Require confirmation before deletion
    $settings->add(new admin_setting_configcheckbox(
        'report_fileusechecker/require_confirmation',
        new lang_string('require_confirmation', 'report_fileusechecker'),
        new lang_string('require_confirmation_desc', 'report_fileusechecker'),
        1
    ));

    // Enable deletion logging
    $settings->add(new admin_setting_configcheckbox(
        'report_fileusechecker/log_deletions',
        new lang_string('log_deletions', 'report_fileusechecker'),
        new lang_string('log_deletions_desc', 'report_fileusechecker'),
        1
    ));

    // ========================================
    // File Detection Settings Section
    // ========================================

    $settings->add(new admin_setting_heading(
        'report_fileusechecker/detection_settings',
        new lang_string('detection_settings', 'report_fileusechecker'),
        new lang_string('detection_settings_desc', 'report_fileusechecker')
    ));

    // Include hidden files
    $settings->add(new admin_setting_configcheckbox(
        'report_fileusechecker/include_hidden_files',
        new lang_string('include_hidden_files', 'report_fileusechecker'),
        new lang_string('include_hidden_files_desc', 'report_fileusechecker'),
        0
    ));

    // Include backup files
    $settings->add(new admin_setting_configcheckbox(
        'report_fileusechecker/include_backup_files',
        new lang_string('include_backup_files', 'report_fileusechecker'),
        new lang_string('include_backup_files_desc', 'report_fileusechecker'),
        0
    ));

    // File types to ignore (comma-separated extensions)
    $settings->add(new admin_setting_configtext(
        'report_fileusechecker/ignored_file_types',
        new lang_string('ignored_file_types', 'report_fileusechecker'),
        new lang_string('ignored_file_types_desc', 'report_fileusechecker'),
        'bak,tmp,swp,lock,tmp~',
        PARAM_TEXT,
        50
    ));

    // ========================================
    // UI/Display Settings Section
    // ========================================

    $settings->add(new admin_setting_heading(
        'report_fileusechecker/display_settings',
        new lang_string('display_settings', 'report_fileusechecker'),
        new lang_string('display_settings_desc', 'report_fileusechecker')
    ));

    // Items per page
    $settings->add(new admin_setting_configtext(
        'report_fileusechecker/items_per_page',
        new lang_string('items_per_page', 'report_fileusechecker'),
        new lang_string('items_per_page_desc', 'report_fileusechecker'),
        50,
        PARAM_INT,
        3
    ));

    // Show file type icons
    $settings->add(new admin_setting_configcheckbox(
        'report_fileusechecker/show_file_icons',
        new lang_string('show_file_icons', 'report_fileusechecker'),
        new lang_string('show_file_icons_desc', 'report_fileusechecker'),
        1
    ));

    // Theme color scheme
    $theme_options = array(
        'light' => new lang_string('theme_light', 'report_fileusechecker'),
        'dark' => new lang_string('theme_dark', 'report_fileusechecker'),
        'auto' => new lang_string('theme_auto', 'report_fileusechecker'),
    );
    $settings->add(new admin_setting_configselect(
        'report_fileusechecker/theme_color',
        new lang_string('theme_color', 'report_fileusechecker'),
        new lang_string('theme_color_desc', 'report_fileusechecker'),
        'auto',
        $theme_options
    ));

    // ========================================
    // Notification Settings Section
    // ========================================

    $settings->add(new admin_setting_heading(
        'report_fileusechecker/notification_settings',
        new lang_string('notification_settings', 'report_fileusechecker'),
        new lang_string('notification_settings_desc', 'report_fileusechecker')
    ));

    // Send email notifications to admin
    $settings->add(new admin_setting_configcheckbox(
        'report_fileusechecker/notify_admin',
        new lang_string('notify_admin', 'report_fileusechecker'),
        new lang_string('notify_admin_desc', 'report_fileusechecker'),
        0
    ));

    // Notify when unused files exceed threshold (in MB)
    $settings->add(new admin_setting_configtext(
        'report_fileusechecker/notify_threshold_mb',
        new lang_string('notify_threshold_mb', 'report_fileusechecker'),
        new lang_string('notify_threshold_mb_desc', 'report_fileusechecker'),
        100,  // Default: 100 MB
        PARAM_INT,
        5
    ));

    // ========================================
    // Advanced Settings Section
    // ========================================

    $settings->add(new admin_setting_heading(
        'report_fileusechecker/advanced_settings',
        new lang_string('advanced_settings', 'report_fileusechecker'),
        new lang_string('advanced_settings_desc', 'report_fileusechecker')
    ));

    // Enable debug mode
    $settings->add(new admin_setting_configcheckbox(
        'report_fileusechecker/debug_mode',
        new lang_string('debug_mode', 'report_fileusechecker'),
        new lang_string('debug_mode_desc', 'report_fileusechecker'),
        0
    ));

    // ========================================
    // Information Section
    // ========================================

    $settings->add(new admin_setting_heading(
        'report_fileusechecker/info_section',
        new lang_string('info_section', 'report_fileusechecker'),
        ''
    ));

    // Plugin version information
    $plugin_info = new stdClass();
    $plugin_info->version = get_config('report_fileusechecker', 'plugin_version') ?? 'Unknown';
    $plugin_info->release = get_config('report_fileusechecker', 'plugin_release') ?? 'Unknown';

    $info_html = html_writer::div(
        html_writer::tag('strong', 'File Use Checker Report Plugin') . '<br/>' .
        'Version: ' . $plugin_info->version . '<br/>' .
        'Release: ' . $plugin_info->release . '<br/>' .
        'For more information, visit: ' .
        html_writer::link('https://github.com/your-org/moodle-report_fileusechecker', 
            'GitHub Repository', array('target' => '_blank')),
        '',
        array('style' => 'padding: 10px; background-color: #f5f5f5; border-radius: 4px; margin-top: 10px;')
    );

    $settings->add(new admin_setting_description(
        'report_fileusechecker/plugin_info',
        '',
        $info_html
    ));

}
