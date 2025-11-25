<?php
/**
 * File Use Checker Report Class
 *
 * @package    report_fileusechecker
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_fileusechecker;

use context_course;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Report class for handling report logic and data retrieval
 */
class report {

    /**
     * @var int Course ID
     */
    private $courseid;

    /**
     * @var context_course Course context
     */
    private $context;

    /**
     * @var file_scanner File scanner instance
     */
    private $scanner;

    /**
     * @var file_analyzer File analyzer instance
     */
    private $analyzer;

    /**
     * Constructor
     *
     * @param int $courseid The course ID
     */
    public function __construct($courseid) {
        $this->courseid = $courseid;
        $this->context = context_course::instance($courseid);
        $this->scanner = new file_scanner($courseid);
        $this->analyzer = new file_analyzer($courseid);
    }

    /**
     * Check if user can view this report
     *
     * @return bool True if user has view capability
     */
    public function can_view() {
        return has_capability('report/fileusechecker:view', $this->context);
    }

    /**
     * Check if user can delete files
     *
     * @return bool True if user has delete capability
     */
    public function can_delete() {
        return has_capability('report/fileusechecker:delete', $this->context);
    }

    /**
     * Get all unused files in the course
     *
     * @return array Array of unused file objects
     */
    public function get_unused_files() {
        try {
            $unused_files = $this->scanner->scan_course();
            return $this->prepare_file_data($unused_files);
        } catch (\Exception $e) {
            debugging('Error scanning course files: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return array();
        }
    }

    /**
     * Prepare file data for display
     *
     * @param array $files Array of file objects
     * @return array Formatted file data
     */
    private function prepare_file_data($files) {
        $prepared = array();

        foreach ($files as $file) {
            $prepared[] = (object) array(
                'fileid' => $file->id,
                'filename' => $file->filename,
                'filesize' => $file->filesize,
                'filesizeformatted' => $this->format_file_size($file->filesize),
                'location' => $this->get_file_location($file),
                'locationurl' => $this->get_location_url($file),
                'activitytype' => $this->get_activity_type($file),
                'mimetype' => $file->mimetype,
                'timecreated' => $file->timecreated,
            );
        }

        return $prepared;
    }

    /**
     * Get summary statistics for unused files
     *
     * @return object Summary statistics
     */
    public function get_summary_stats() {
        $files = $this->get_unused_files();

        $total_files = count($files);
        $total_size = 0;
        $affected_activities = array();

        foreach ($files as $file) {
            $total_size += $file->filesize;
            if (!empty($file->location)) {
                $affected_activities[$file->location] = true;
            }
        }

        return (object) array(
            'total_files' => $total_files,
            'total_size' => $total_size,
            'total_size_formatted' => $this->format_file_size($total_size),
            'affected_activities_count' => count($affected_activities),
            'has_unused_files' => $total_files > 0,
        );
    }

    /**
     * Delete specified files
     *
     * @param array $fileids Array of file IDs to delete
     * @return array Result array with status and message
     */
    public function delete_files($fileids) {
        global $DB, $USER;

        if (!$this->can_delete()) {
            return array(
                'success' => false,
                'message' => get_string('delete_error', 'report_fileusechecker'),
                'deleted_count' => 0,
            );
        }

        if (empty($fileids) || !is_array($fileids)) {
            return array(
                'success' => false,
                'message' => get_string('delete_error', 'report_fileusechecker'),
                'deleted_count' => 0,
            );
        }

        $deleted_count = 0;
        $fs = get_file_storage();

        try {
            foreach ($fileids as $fileid) {
                // Verify file ID is an integer
                $fileid = (int) $fileid;

                // Get file record
                $file = $fs->get_file_by_id($fileid);

                if ($file) {
                    // Verify file is actually unused before deletion
                    if ($this->is_file_unused($fileid)) {
                        $file->delete();
                        $deleted_count++;

                        // Log deletion
                        $this->log_file_deletion($fileid, $file->get_filename(), $USER->id);
                    }
                }
            }

            if ($deleted_count > 0) {
                return array(
                    'success' => true,
                    'message' => get_string('delete_success', 'report_fileusechecker', $deleted_count),
                    'deleted_count' => $deleted_count,
                );
            } else {
                return array(
                    'success' => false,
                    'message' => get_string('delete_error', 'report_fileusechecker'),
                    'deleted_count' => 0,
                );
            }
        } catch (\Exception $e) {
            debugging('Error deleting files: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return array(
                'success' => false,
                'message' => get_string('delete_error', 'report_fileusechecker'),
                'deleted_count' => $deleted_count,
            );
        }
    }

    /**
     * Verify if file is unused before deletion
     *
     * @param int $fileid File ID
     * @return bool True if file is unused
     */
    private function is_file_unused($fileid) {
        $unused_files = $this->get_unused_files();

        foreach ($unused_files as $file) {
            if ($file->fileid == $fileid) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log file deletion for audit trail
     *
     * @param int $fileid File ID
     * @param string $filename File name
     * @param int $userid User ID who performed deletion
     */
    private function log_file_deletion($fileid, $filename, $userid) {
        // Add event logging if needed in future
        \core\event\base::create_from_data(array(
            'context' => $this->context,
            'objectid' => $fileid,
            'eventname' => '\report_fileusechecker\event\file_deleted',
        ))->trigger();
    }

    /**
     * Format file size in human-readable format
     *
     * @param int $bytes File size in bytes
     * @return string Formatted file size
     */
    public function format_file_size($bytes) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get human-readable location string for a file
     *
     * @param object $file File object
     * @return string Location description
     */
    private function get_file_location($file) {
        $location = '';

        if (!empty($file->component)) {
            $location = $file->component;

            // Add activity instance name if available
            if (!empty($file->itemid)) {
                $location .= ' - ' . $file->itemid;
            }
        }

        return !empty($location) ? $location : get_string('unknown', 'moodle');
    }

    /**
     * Get URL to manage files location for a file
     *
     * @param object $file File object
     * @return moodle_url URL to file management area
     */
    private function get_location_url($file) {
        global $DB;

        // Handle different component types
        if (strpos($file->component, 'mod_') === 0) {
            // Module activity
            $cm = get_coursemodule_from_id(substr($file->component, 4), $file->itemid);
            if ($cm) {
                return new moodle_url('/course/modedit.php', array(
                    'update' => $cm->id,
                    'return' => 1,
                ));
            }
        }

        // Default to course files
        return new moodle_url('/course/view.php', array('id' => $this->courseid));
    }

    /**
     * Get activity type for display
     *
     * @param object $file File object
     * @return string Activity type
     */
    private function get_activity_type($file) {
        if (!empty($file->component) && strpos($file->component, 'mod_') === 0) {
            return ucfirst(substr($file->component, 4));
        }

        if ($file->component === 'course') {
            return get_string('coursefiles', 'moodle');
        }

        return get_string('unknown', 'moodle');
    }

    /**
     * Get course ID
     *
     * @return int Course ID
     */
    public function get_courseid() {
        return $this->courseid;
    }

    /**
     * Get course context
     *
     * @return context_course Course context
     */
    public function get_context() {
        return $this->context;
    }
}
