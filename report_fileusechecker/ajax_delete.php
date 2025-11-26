<?php
/**
 * File Use Checker - AJAX Delete Handler
 *
 * Handles AJAX requests for file deletion
 *
 * @package    report_fileusechecker
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use report_fileusechecker\report;

defined('MOODLE_INTERNAL') || die();

// Ensure AJAX request
if (!AJAX_SCRIPT) {
    http_response_code(400);
    die(json_encode(array(
        'success' => false,
        'message' => 'Invalid request',
    )));
}

// Get course ID
$courseid = required_param('courseid', PARAM_INT);

// Get course and verify it exists
$course = get_course($courseid);
if (!$course) {
    http_response_code(404);
    die(json_encode(array(
        'success' => false,
        'message' => get_string('coursenotfound', 'moodle'),
    )));
}

// Get context and require login
$context = context_course::instance($courseid);
require_login($course);

// Verify session key for CSRF protection
$sesskey = optional_param('sesskey', '', PARAM_RAW);
if (!confirm_sesskey($sesskey)) {
    http_response_code(403);
    die(json_encode(array(
        'success' => false,
        'message' => get_string('invalidsesskey', 'moodle'),
    )));
}

// Get action
$action = optional_param('action', '', PARAM_ALPHA);

// Validate action
if (empty($action)) {
    http_response_code(400);
    die(json_encode(array(
        'success' => false,
        'message' => 'Missing action parameter',
    )));
}

// Handle delete action
if ($action === 'delete') {
    // Get file IDs
    $fileids = optional_param('fileids', array(), PARAM_INT);

    if (!is_array($fileids)) {
        $fileids = array($fileids);
    }

    // Validate file IDs
    if (empty($fileids)) {
        http_response_code(400);
        die(json_encode(array(
            'success' => false,
            'message' => 'No files specified',
        )));
    }

    // Create report instance
    $report = new report($courseid);

    // Check permission to delete
    if (!$report->can_delete()) {
        http_response_code(403);
        die(json_encode(array(
            'success' => false,
            'message' => get_string('permissiondenied', 'moodle'),
        )));
    }

    // Perform deletion
    $result = $report->delete_files($fileids);

    // Return response
    if ($result['success']) {
        http_response_code(200);
        die(json_encode(array(
            'success' => true,
            'message' => $result['message'],
            'deleted_count' => $result['deleted_count'],
        )));
    } else {
        http_response_code(400);
        die(json_encode(array(
            'success' => false,
            'message' => $result['message'],
            'deleted_count' => $result['deleted_count'],
        )));
    }
} else {
    http_response_code(400);
    die(json_encode(array(
        'success' => false,
        'message' => 'Invalid action',
    )));
}
