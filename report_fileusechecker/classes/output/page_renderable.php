<?php
/**
 * Data structure for rendering the File Use Checker report page.
 *
 * @package    report_fileusechecker
 * @copyright  2025 Sean Rugge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_fileusechecker\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use report_fileusechecker\report;
use context_course;
use moodle_url;

/**
 * Page renderable class for the File Use Checker report.
 */
class page_renderable implements renderable, templatable {

    /** @var int $courseid The ID of the course being viewed. */
    protected $courseid;

    /** @var object $summary_stats Summary statistics from the report. */
    protected $summary_stats;

    /** @var array $unused_files Array of unused file objects. */
    protected $unused_files;

    /** @var context_course $context Course context. */
    protected $context;

    /** @var bool $can_delete Permission flag for file deletion. */
    protected $can_delete;

    /**
     * Constructor.
     *
     * @param int $courseid The course ID.
     */
    public function __construct(int $courseid) {
        global $USER;

        $this->courseid = $courseid;
        $this->context = context_course::instance($courseid);

        // Check deletion capability early.
        $this->can_delete = has_capability('report/fileusechecker:delete', $this->context, $USER->id);

        // FIX: Pass $courseid to the report constructor as required by report.php
        $report = new report($courseid);

        // FIX: Call methods without $courseid argument
        $this->unused_files = $report->get_unused_files();
        $this->summary_stats = $report->get_summary_stats();
    }

    /**
     * Exports the data for mustache rendering.
     *
     * @global \moodle_page $PAGE
     * @return stdClass
     */
    public function export_for_template(\renderer_base $output) {
        $data = new \stdClass();
        
        $data->courseid = $this->courseid;
        $data->sesskey = sesskey();
        $data->can_delete = $this->can_delete;
        $data->has_unused_files = !empty($this->unused_files);
        
        // This is used for the action on the page, like the bulk delete form
        $data->deleteurl = new moodle_url('/report/fileusechecker/index.php', ['id' => $this->courseid]);

        $data->summary_stats = $this->summary_stats;
        $data->unused_files = [];

        // Format unused files for the template.
        foreach ($this->unused_files as $file) {
             // Ensure files are objects (as they come from the report class)
            $data->unused_files[] = (object) [
                'fileid' => $file->fileid,
                'filename' => $file->filename,
                'filesize' => $file->filesize,
                'filesizeformatted' => $file->filesizeformatted,
                'location' => $file->location,
                'locationurl' => $file->locationurl->out(false), // Convert moodle_url object to string
                'activitytype' => $file->activitytype,
                'can_delete' => $this->can_delete,
            ];
        }
        
        // Pass necessary strings to the template
        $data->strings = [
            'title' => get_string('title', 'report_fileusechecker'),
            'description' => get_string('description', 'report_fileusechecker'),
            'file_name' => get_string('file_name', 'report_fileusechecker'),
            'file_size' => get_string('file_size', 'report_fileusechecker'),
            'location' => get_string('location', 'report_fileusechecker'),
            'activity_type' => get_string('activity_type', 'report_fileusechecker'),
            'select' => get_string('select', 'report_fileusechecker'),
            'delete_selected' => get_string('delete_selected', 'report_fileusechecker'),
            'no_unused_files' => get_string('no_unused_files', 'report_fileusechecker'),
            'total_unused_files' => get_string('total_unused_files', 'report_fileusechecker', $data->summary_stats->total_files),
            'total_file_size' => get_string('total_file_size', 'report_fileusechecker', $data->summary_stats->total_size_formatted),
            'affected_activities' => get_string('affected_activities', 'report_fileusechecker', $data->summary_stats->affected_activities_count),
            'delete_confirmation' => get_string('delete_confirmation', 'report_fileusechecker', -1), // Placeholder for JS
        ];

        return $data;
    }
}
