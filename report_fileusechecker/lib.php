<?php
/**
 * Library file for the File Use Checker report plugin.
 *
 * This file contains functions necessary to hook into Moodle's core systems,
 * specifically extending the course navigation for reports.
 *
 * @package    report_fileusechecker
 * @copyright  2025 Sean Rugge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extends the course report navigation for fileusechecker.
 *
 * @param navigation_node $navigation The report navigation node.
 * @param context_course $context The course context.
 * @param int $courseid The course ID.
 * @param array $reportname The name of the report in the URL.
 * @return void
 */
function report_fileusechecker_extend_navigation(\navigation_node $navigation, \context_course $context) {
    global $CFG;

    // Check if the user has the capability to view the report in this context.
    if (has_capability('report/fileusechecker:view', $context)) {
        $reporturl = new \moodle_url('/report/fileusechecker/index.php', array('id' => $context->instanceid));
        $reportname = get_string('pluginname', 'report_fileusechecker');
        
        // Add the link to the course reports navigation tree.
        $navigation->add($reportname, $reporturl, \navigation_node::TYPE_SETTING, null, 'fileusechecker');
    }
}
