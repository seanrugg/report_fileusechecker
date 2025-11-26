<?php
/**
 * Page Renderable Class
 *
 * Data structure for rendering the report page
 *
 * @package    report_fileusechecker
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_fileusechecker\output;

use renderable;
use renderer_base;
use templatable;

defined('MOODLE_INTERNAL') || die();

/**
 * Page renderable for File Use Checker report
 */
class page_renderable implements renderable, templatable {

    /**
     * @var int Course ID
     */
    private $courseid;

    /**
     * @var array Array of unused file objects
     */
    private $unused_files;

    /**
     * @var object Summary statistics object
     */
    private $summary_stats;

    /**
     * @var bool Whether user can delete files
     */
    private $can_delete;

    /**
     * @var string Report title
     */
    private $title;

    /**
     * @var string Report description
     */
    private $description;

    /**
     * Constructor
     *
     * @param int $courseid Course ID
     * @param array $unused_files Array of unused file objects
     * @param object $summary_stats Summary statistics object
     * @param bool $can_delete Whether user can delete files
     */
    public function __construct($courseid, $unused_files, $summary_stats, $can_delete = false) {
        $this->courseid = $courseid;
        $this->unused_files = $unused_files;
        $this->summary_stats = $summary_stats;
        $this->can_delete = $can_delete;
        $this->title = get_string('title', 'report_fileusechecker');
        $this->description = get_string('description', 'report_fileusechecker');
    }

    /**
     * Export data for use in templates
     *
     * @param renderer_base $output The renderer
     * @return array Data for template
     */
    public function export_for_template(renderer_base $output) {
        global $COURSE;

        $has_files = !empty($this->unused_files) && $this->summary_stats->total_files > 0;

        $data = array(
            'courseid' => $this->courseid,
            'coursename' => $COURSE->fullname,
            'title' => $this->title,
            'description' => $this->description,
            'has_unused_files' => $has_files,
            'can_delete' => $this->can_delete,
        );

        // Add summary statistics
        if ($has_files) {
            $data['summary'] = array(
                'total_files' => $this->summary_stats->total_files,
                'total_size_formatted' => $this->summary_stats->total_size_formatted,
                'affected_activities_count' => $this->summary_stats->affected_activities_count,
            );

            // Prepare file data for template
            $data['files'] = $this->prepare_files_for_template();
        } else {
            $data['no_files_message'] = get_string('no_unused_files', 'report_fileusechecker');
        }

        // Add capabilities and permissions
        $data['permissions'] = array(
            'can_view' => true,
            'can_delete' => $this->can_delete,
        );

        // Add URLs
        $data['ajax_delete_url'] = new \moodle_url('/report/fileusechecker/ajax_delete.php', array(
            'courseid' => $this->courseid,
            'sesskey' => sesskey(),
        ));

        $data['refresh_url'] = new \moodle_url('/report/fileusechecker/index.php', array(
            'courseid' => $this->courseid,
        ));

        return $data;
    }

    /**
     * Prepare files for template rendering
     *
     * @return array Array of prepared file data
     */
    private function prepare_files_for_template() {
        $files_for_template = array();

        foreach ($this->unused_files as $file) {
            $files_for_template[] = array(
                'fileid' => $file->fileid,
                'filename' => $file->filename,
                'filesize' => $file->filesizeformatted,
                'activitytype' => $file->activitytype,
                'location' => $file->location,
                'locationurl' => $file->locationurl->out(false),
                'mimetype' => $file->mimetype,
            );
        }

        return $files_for_template;
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
     * Get unused files
     *
     * @return array Array of unused file objects
     */
    public function get_unused_files() {
        return $this->unused_files;
    }

    /**
     * Get summary statistics
     *
     * @return object Summary statistics object
     */
    public function get_summary_stats() {
        return $this->summary_stats;
    }

    /**
     * Check if user can delete
     *
     * @return bool True if user can delete
     */
    public function can_delete() {
        return $this->can_delete;
    }

    /**
     * Get title
     *
     * @return string Title
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * Get description
     *
     * @return string Description
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * Set title
     *
     * @param string $title Title to set
     */
    public function set_title($title) {
        $this->title = $title;
    }

    /**
     * Set description
     *
     * @param string $description Description to set
     */
    public function set_description($description) {
        $this->description = $description;
    }

    /**
     * Set unused files
     *
     * @param array $files Array of file objects
     */
    public function set_unused_files($files) {
        $this->unused_files = $files;
    }

    /**
     * Set summary statistics
     *
     * @param object $stats Statistics object
     */
    public function set_summary_stats($stats) {
        $this->summary_stats = $stats;
    }

    /**
     * Set can delete permission
     *
     * @param bool $can_delete Whether user can delete
     */
    public function set_can_delete($can_delete) {
        $this->can_delete = $can_delete;
    }

    /**
     * Get file count
     *
     * @return int Number of unused files
     */
    public function get_file_count() {
        return count($this->unused_files);
    }

    /**
     * Check if there are any unused files
     *
     * @return bool True if there are unused files
     */
    public function has_unused_files() {
        return $this->get_file_count() > 0;
    }

    /**
     * Get total storage size
     *
     * @return int Total size in bytes
     */
    public function get_total_storage() {
        return $this->summary_stats->total_size ?? 0;
    }

    /**
     * Get affected activities count
     *
     * @return int Number of affected activities
     */
    public function get_affected_activities_count() {
        return $this->summary_stats->affected_activities_count ?? 0;
    }
}
