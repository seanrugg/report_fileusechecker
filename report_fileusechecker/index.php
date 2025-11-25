<?php
/**
 * Main entry point for the File Use Checker Report.
 *
 * @package    report_fileusechecker
 * @copyright  2025 Sean Rugge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use report_fileusechecker\report;
use report_fileusechecker\output\page_renderable;
use core\notification;

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('id', PARAM_INT);
$fileids = optional_param_array('fileids', array(), PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$sesskey = optional_param('sesskey', '', PARAM_RAW);

// 1. Check Course and Context
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}
$context = context_course::instance($courseid);

// 2. Check View Capability
require_login($course, false);

$report = new report($courseid); 
if (!$report->can_view()) {
    print_error('nopermissions', 'error', '', 'report/fileusechecker:view');
}

// 3. Process Delete Action (if any)
if ($action === 'delete' && !empty($fileids) && confirm_sesskey($sesskey) && $report->can_delete()) {
    $deletedcount = 0;
    try {
        // FIX: The delete_files method in report.php needs to be implemented to accept $fileids
        $deletedcount = $report->delete_files($fileids);
        
        // Successful deletion, redirect to clear POST data and show message.
        redirect(new moodle_url('/report/fileusechecker/index.php', 
            array('id' => $courseid, 'deletedcount' => $deletedcount)), 
            null, 0);

    } catch (Exception $e) {
        // Handle deletion errors.
        notification::error(get_string('delete_failed', 'report_fileusechecker', $e->getMessage()));
    }
}

// 4. Setup Page Navigation and Breadcrumbs
$reportname = get_string('pluginname', 'report_fileusechecker');
$PAGE->set_url('/report/fileusechecker/index.php', ['id' => $courseid]);
$PAGE->set_title(format_string($course->fullname) . ': ' . $reportname);
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Crucial: Set up navigation/breadcrumbs (redundant if lib.php works, but good practice)
$courseadminnode = $PAGE->navigation->add(get_string('courseadministration'));
$reportnode = $courseadminnode->add(get_string('reports'), null, \navigation_node::TYPE_CONTAINER);
$reportnode->add($reportname, new \moodle_url('/report/fileusechecker/index.php', ['id' => $courseid]));


// 5. Get data and Render
// The renderable object fetches its own data inside its constructor.
$renderable = new page_renderable($courseid);

// Check for and display success message after a redirect
$deletedcount = optional_param('deletedcount', 0, PARAM_INT);
if ($deletedcount > 0) {
    notification::success(get_string('delete_success', 'report_fileusechecker', $deletedcount));
}

// 6. Echo the output
echo $PAGE->get_renderer('report_fileusechecker')->render_page($renderable);
