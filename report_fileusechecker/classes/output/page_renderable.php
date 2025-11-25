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

/**
 * Page renderable class for the File Use Checker report.
 *
 * @copyright  2025 Sean Rugge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_renderable implements renderable, templatable {

    /** @var int $courseid The ID of the course being viewed. */
    protected $courseid;

    /** @var array $summary_stats Summary statistics from the report. */
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
        global $PAGE, $USER;

        $this->courseid = $courseid;
        $this->context = context_course::instance($courseid);

        // Check deletion capability early.
        $this->can_delete = has_capability('report/fileusechecker:delete', $this->context, $USER->id);

        $report = new report();

        // Retrieve data.
        $this->unused_files = $report->get_unused_files($courseid);
        $this->summary_stats = $report->get_summary_stats($courseid);
    }

    /**
     * Exports the data for mustache rendering.
     *
     * @global \moodle_page $PAGE
     * @return array
     */
    public function export_for_template() {
        global $PAGE;

        $data = [
            'courseid' => $this->courseid,
            'sesskey' => sesskey(),
            'can_delete' => $this->can_delete,
            'has_unused_files' => !empty($this->unused_files),
            'deleteurl' => $PAGE->url->out(), // Post back to the current page for action
            'summary_stats' => $this->summary_stats,
            'unused_files' => [],
        ];

        // Format unused files for the template.
        foreach ($this->unused_files as $file) {
            $data['unused_files'][] = [
                'fileid' => $file->id, // File record ID (or contenthash/contextid depending on how the report class implements it)
                'filename' => $file->filename,
                'filesize' => $file->filesize,
                'filesizeformatted' => \core_string_manager::instance()->format_bytes($file->filesize),
                'filepath' => $file->filepath,
                'locationname' => $file->locationname,
                'locationurl' => $file->locationurl,
                'activitytype' => $file->activitytype,
                'can_delete' => $this->can_delete, // Pass down to the sub-template
            ];
        }

        return $data;
    }
}
