<?php
/**
 * File Use Checker Report - Plugin Integration
 *
 * Provides plugin hooks and callbacks for Moodle integration
 *
 * @package    report_fileusechecker
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extend the course navigation with the File Use Checker report link
 *
 * This function is called by Moodle to allow plugins to add navigation nodes.
 * It adds a link to the File Use Checker report in the course administration menu.
 *
 * @param navigation_node $navigation The navigation node to add to
 * @param stdClass $course The course object
 * @param context_course $context The course context
 */
function report_fileusechecker_extend_navigation_course(navigation_node $navigation, stdClass $course, context_course $context) {
    // Only add the report link if the user has permission to view it
    if (has_capability('report/fileusechecker:view', $context)) {
        // Get the "Reports" navigation node
        $reports_node = $navigation->find('coursereports', navigation_node::TYPE_CONTAINER);
        
        if ($reports_node) {
            // Add the File Use Checker report link to the Reports node
            $url = new moodle_url('/report/fileusechecker/index.php', array('id' => $course->id));
            $reports_node->add(
                get_string('pluginname', 'report_fileusechecker'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                'report_fileusechecker'
            );
        }
    }
}

/**
 * Register plugin as a report
 *
 * This function tells Moodle that this is a course report plugin.
 *
 * @return array Plugin information
 */
function report_fileusechecker_get_report_info() {
    return array(
        'report_fileusechecker' => array(
            'name' => get_string('pluginname', 'report_fileusechecker'),
            'description' => get_string('description', 'report_fileusechecker'),
        ),
    );
}

/**
 * Check if plugin is enabled
 *
 * Optionally check settings to see if plugin is globally enabled
 *
 * @return bool True if plugin is enabled
 */
function report_fileusechecker_is_enabled() {
    // Check if admin has disabled the plugin
    $enabled = get_config('report_fileusechecker', 'enabled');
    
    // Default to enabled if setting not set
    if ($enabled === false) {
        return true;
    }
    
    return (bool) $enabled;
}
