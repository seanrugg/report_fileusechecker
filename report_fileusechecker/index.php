<?php
/**
 * File Use Checker Report - Main Entry Point
 *
 * @package    report_fileusechecker
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use report_fileusechecker\report;
use report_fileusechecker\output\page_renderable;

defined('MOODLE_INTERNAL') || die();

// Get course ID from request
$courseid = required_param('id', PARAM_INT);

// Get the course
$course = get_course($courseid);

// Set up the page
$url = new moodle_url('/report/fileusechecker/index.php', array('id' => $courseid));
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('pluginname', 'report_fileusechecker'));
$PAGE->set_heading($course->fullname);
$PAGE->set_context(context_course::instance($courseid));

// Add breadcrumb
$PAGE->navbar->add(get_string('reports', 'moodle'), new moodle_url('/report/index.php', array('id' => $courseid)));
$PAGE->navbar->add(get_string('pluginname', 'report_fileusechecker'), $url);

// Require login and course access
require_login($course);

// Create report instance
$report = new report($courseid);

// Check if user can view this report
if (!$report->can_view()) {
    print_error('permissiondenied', 'moodle');
}

// Get unused files
$unused_files = $report->get_unused_files();

// Get summary statistics
$summary_stats = $report->get_summary_stats();

// Check if user can delete files
$can_delete = $report->can_delete();

// Create page renderable object
$page = new page_renderable($courseid, $unused_files, $summary_stats, $can_delete);

// Output the page header
echo $OUTPUT->header();

// Output the report content
echo $OUTPUT->render($page);

// Add CSS
$PAGE->requires->css('/report/fileusechecker/styles.css');

// Add any necessary JS
$PAGE->requires->js_call_amd('report_fileusechecker/report', 'init', array($courseid, $can_delete));

// Output the page footer
echo $OUTPUT->footer();
