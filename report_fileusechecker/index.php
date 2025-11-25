<?php
/**
 * Main entry point for the File Use Checker course report.
 *
 * @package    report_fileusechecker
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once('classes/report.php');
require_once('classes/output/page_renderable.php');

use report_fileusechecker\report;
use report_fileusechecker\output\page_renderable;
use core\output\notification;

// 1. Get Course and Context
$courseid = required_param('id', PARAM_INT);
$course = get_course($courseid);
$context = context_course::instance($courseid);

// 2. Check capabilities
require_capability('report/fileusechecker:view', $context);

// 3. Setup Page
$reportname = get_string('pluginname', 'report_fileusechecker');
$PAGE->set_url('/report/fileusechecker/index.php', ['id' => $courseid]);
$PAGE->set_title(format_string($course->fullname) . ': ' . $reportname);
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Course administration is the default navigation for course reports
$courseadminnode = $PAGE->navigation->add(get_string('courseadministration'));
$reportnode = $courseadminnode->add(get_string('reports'), null, navigation_node::TYPE_CONTAINER);
$reportnode->add($reportname);

$renderer = $PAGE->get_renderer('report_fileusechecker');
$file_report = new report();
$message = null; // Variable to hold success/error messages

// 4. Handle Deletion Form Submission
if (data_submitted() && confirm_sesskey()) {
    $can_delete = has_capability('report/fileusechecker:delete', $context);
    $fileids = optional_param_array('fileids', array(), PARAM_INT);

    if ($can_delete && !empty($fileids)) {
        try {
            $deletedcount = $file_report->delete_files($fileids, $context);
            if ($deletedcount > 0) {
                $a = new \stdClass();
                $a->count = $deletedcount;
                // Log the action.
                $event = \core\event\report_viewed::create([
                    'context' => $context,
                    'other' => ['deleted_count' => $deletedcount, 'file_ids' => $fileids],
                ]);
                $event->trigger();

                $message = new notification(get_string('delete_success', 'report_fileusechecker', $deletedcount), \core\output\notification::SUCCESS);
            }
        } catch (\Exception $e) {
            $message = new notification(get_string('delete_error', 'report_fileusechecker'), \core\output\notification::ERROR);
            \core\session\manager::get_exception_handler()->handle_exception($e);
        }
    } else if (!empty($fileids)) {
        // User attempted deletion without capability.
        $message = new notification(get_string('nopermissions', 'error'), \core\output\notification::ERROR);
    }
}

// 5. Output Header and Content
echo $OUTPUT->header();

// Display success/error messages if any.
if (!empty($message)) {
    echo $renderer->render($message);
}

// Render the main report.
$renderable = new page_renderable($courseid);
echo $renderer->render_report($renderable);

echo $OUTPUT->footer();
