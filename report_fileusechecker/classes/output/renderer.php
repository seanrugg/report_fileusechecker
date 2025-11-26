<?php
/**
 * Renderer Class
 *
 * Handles output rendering for the File Use Checker report
 *
 * @package    report_fileusechecker
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_fileusechecker\output;

use plugin_renderer_base;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Renderer for File Use Checker report
 */
class renderer extends plugin_renderer_base {

    /**
     * Render the main report page
     *
     * @param page_renderable $page The page renderable object
     * @return string HTML output
     */
    public function render_page_renderable(page_renderable $page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('report_fileusechecker/report_view', $data);
    }

    /**
     * Render the summary statistics section
     *
     * @param object $stats Summary statistics object
     * @return string HTML output
     */
    public function render_summary($stats) {
        $data = array(
            'has_unused_files' => $stats->has_unused_files,
            'total_files' => $stats->total_files,
            'total_size_formatted' => $stats->total_size_formatted,
            'affected_activities_count' => $stats->affected_activities_count,
        );

        return $this->render_from_template('report_fileusechecker/summary_box', $data);
    }

    /**
     * Render the no files found message
     *
     * @return string HTML output
     */
    public function render_no_files_message() {
        return $this->output->notification(
            get_string('no_unused_files', 'report_fileusechecker'),
            'success'
        );
    }

    /**
     * Render the file table
     *
     * @param array $files Array of file objects
     * @param bool $can_delete Whether user can delete files
     * @return string HTML output
     */
    public function render_file_table($files, $can_delete = false) {
        if (empty($files)) {
            return $this->render_no_files_message();
        }

        $table_html = html_writer::start_tag('div', array('class' => 'file-table-wrapper'));

        // Action buttons
        if ($can_delete) {
            $table_html .= $this->render_action_buttons();
        }

        // Table
        $table_html .= html_writer::start_tag('table', array(
            'class' => 'file-table table table-striped table-hover',
            'id' => 'unused-files-table',
        ));

        // Table header
        $table_html .= $this->render_table_header($can_delete);

        // Table body
        $table_html .= html_writer::start_tag('tbody');

        foreach ($files as $file) {
            $table_html .= $this->render_file_row($file, $can_delete);
        }

        $table_html .= html_writer::end_tag('tbody');
        $table_html .= html_writer::end_tag('table');
        $table_html .= html_writer::end_tag('div');

        return $table_html;
    }

    /**
     * Render action buttons (select all, deselect all, delete)
     *
     * @return string HTML output
     */
    private function render_action_buttons() {
        $buttons = html_writer::start_tag('div', array('class' => 'action-buttons mb-3'));

        $buttons .= html_writer::tag(
            'button',
            get_string('select_all', 'report_fileusechecker'),
            array(
                'type' => 'button',
                'class' => 'btn btn-secondary btn-sm',
                'id' => 'select-all-btn',
            )
        );

        $buttons .= ' ';

        $buttons .= html_writer::tag(
            'button',
            get_string('deselect_all', 'report_fileusechecker'),
            array(
                'type' => 'button',
                'class' => 'btn btn-secondary btn-sm',
                'id' => 'deselect-all-btn',
            )
        );

        $buttons .= ' ';

        $buttons .= html_writer::tag(
            'button',
            get_string('delete_selected', 'report_fileusechecker'),
            array(
                'type' => 'button',
                'class' => 'btn btn-danger btn-sm',
                'id' => 'delete-selected-btn',
            )
        );

        $buttons .= html_writer::end_tag('div');

        return $buttons;
    }

    /**
     * Render table header row
     *
     * @param bool $can_delete Whether user can delete files
     * @return string HTML output
     */
    private function render_table_header($can_delete = false) {
        $header = html_writer::start_tag('thead');
        $header .= html_writer::start_tag('tr', array('class' => 'table-header-row'));

        if ($can_delete) {
            $header .= html_writer::tag('th', '', array(
                'class' => 'text-center checkbox-column',
                'style' => 'width: 40px;',
            ));
        }

        $header .= html_writer::tag('th', get_string('file_name', 'report_fileusechecker'), array(
            'class' => 'file-name-column',
        ));

        $header .= html_writer::tag('th', get_string('file_size', 'report_fileusechecker'), array(
            'class' => 'file-size-column',
            'style' => 'width: 120px;',
        ));

        $header .= html_writer::tag('th', get_string('activity_type', 'report_fileusechecker'), array(
            'class' => 'activity-type-column',
            'style' => 'width: 120px;',
        ));

        $header .= html_writer::tag('th', get_string('location', 'report_fileusechecker'), array(
            'class' => 'location-column',
        ));

        $header .= html_writer::end_tag('tr');
        $header .= html_writer::end_tag('thead');

        return $header;
    }

    /**
     * Render a single file table row
     *
     * @param object $file File object
     * @param bool $can_delete Whether user can delete files
     * @return string HTML output
     */
    private function render_file_row($file, $can_delete = false) {
        $row = html_writer::start_tag('tr', array(
            'class' => 'file-row',
            'data-fileid' => $file->fileid,
        ));

        if ($can_delete) {
            $checkbox = html_writer::checkbox(
                'fileid',
                $file->fileid,
                false,
                '',
                array(
                    'class' => 'file-checkbox',
                    'data-fileid' => $file->fileid,
                )
            );

            $row .= html_writer::tag('td', $checkbox, array(
                'class' => 'text-center checkbox-column',
            ));
        }

        // File name with icon
        $file_icon = $this->get_file_icon($file->filename, $file->mimetype);
        $file_name_html = $file_icon . ' ' . html_writer::tag(
            'span',
            format_text($file->filename, FORMAT_PLAIN),
            array('class' => 'file-name')
        );

        $row .= html_writer::tag('td', $file_name_html, array(
            'class' => 'file-name-column',
        ));

        // File size
        $row .= html_writer::tag('td', $file->filesizeformatted, array(
            'class' => 'file-size-column text-nowrap',
        ));

        // Activity type
        $row .= html_writer::tag('td', $file->activitytype, array(
            'class' => 'activity-type-column',
        ));

        // Location with link
        $location_link = html_writer::link(
            $file->locationurl,
            format_text($file->location, FORMAT_PLAIN),
            array(
                'class' => 'location-link',
                'target' => '_blank',
                'rel' => 'noopener noreferrer',
            )
        );

        $row .= html_writer::tag('td', $location_link, array(
            'class' => 'location-column',
        ));

        $row .= html_writer::end_tag('tr');

        return $row;
    }

    /**
     * Get file icon based on file type
     *
     * @param string $filename File name
     * @param string $mimetype MIME type
     * @return string HTML icon element
     */
    private function get_file_icon($filename, $mimetype) {
        global $CFG;

        $iconurl = moodle_url::make_pluginfile_url(
            get_file_storage()->get_file_by_hash(sha1('/' . $mimetype))->get_contextid(),
            'core',
            'filetypes',
            0,
            '/',
            $mimetype,
            false
        );

        // Use Moodle's built-in file icon picker
        $iconurl = $this->output->pix_url('f/document');

        // Override for common types
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $icon_map = array(
            'pdf' => 'f/pdf',
            'doc' => 'f/document',
            'docx' => 'f/document',
            'xls' => 'f/spreadsheet',
            'xlsx' => 'f/spreadsheet',
            'ppt' => 'f/presentation',
            'pptx' => 'f/presentation',
            'zip' => 'f/archive',
            'txt' => 'f/text',
            'jpg' => 'f/image',
            'jpeg' => 'f/image',
            'png' => 'f/image',
            'gif' => 'f/image',
            'mp3' => 'f/audio',
            'mp4' => 'f/video',
            'mov' => 'f/video',
            'avi' => 'f/video',
        );

        if (isset($icon_map[$ext])) {
            $iconurl = $this->output->pix_url($icon_map[$ext]);
        }

        return html_writer::img(
            $iconurl,
            $ext,
            array(
                'class' => 'file-icon',
                'alt' => $ext,
            )
        );
    }

    /**
     * Render loading indicator
     *
     * @return string HTML output
     */
    public function render_loading() {
        return html_writer::div(
            html_writer::tag('i', '', array('class' => 'fa fa-spinner fa-spin')) . ' ' .
            get_string('loading', 'report_fileusechecker'),
            'alert alert-info text-center'
        );
    }

    /**
     * Render error message
     *
     * @param string $message Error message
     * @return string HTML output
     */
    public function render_error($message) {
        return $this->output->notification($message, 'error');
    }

    /**
     * Render information message
     *
     * @param string $message Information message
     * @return string HTML output
     */
    public function render_info($message) {
        return $this->output->notification($message, 'info');
    }

    /**
     * Render warning message
     *
     * @param string $message Warning message
     * @return string HTML output
     */
    public function render_warning($message) {
        return $this->output->notification($message, 'warning');
    }

    /**
     * Render success message
     *
     * @param string $message Success message
     * @return string HTML output
     */
    public function render_success($message) {
        return $this->output->notification($message, 'success');
    }

    /**
     * Render a permission denied message
     *
     * @return string HTML output
     */
    public function render_permission_denied() {
        return $this->output->notification(
            get_string('permissiondenied', 'moodle'),
            'error'
        );
    }

    /**
     * Render help text
     *
     * @param string $identifier String identifier
     * @param string $component Component name
     * @return string HTML output
     */
    public function render_help($identifier, $component = 'report_fileusechecker') {
        return $this->output->help_icon($identifier, $component);
    }

    /**
     * Render a badge with count
     *
     * @param int $count The count to display
     * @param string $class CSS class for styling
     * @return string HTML output
     */
    public function render_badge($count, $class = 'badge-secondary') {
        return html_writer::tag(
            'span',
            $count,
            array(
                'class' => 'badge ' . $class,
            )
        );
    }

    /**
     * Render confirmation dialog HTML
     *
     * @param int $filecount Number of files to delete
     * @return string HTML output
     */
    public function render_confirmation_dialog($filecount) {
        $message = get_string('delete_confirmation', 'report_fileusechecker', $filecount);

        return html_writer::div(
            html_writer::tag('p', $message, array('class' => 'confirmation-message')),
            'confirmation-dialog',
            array('style' => 'display: none;')
        );
    }
}
