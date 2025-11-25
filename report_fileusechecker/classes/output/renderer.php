<?php
/**
 * Renderer for the File Use Checker report.
 *
 * @package    report_fileusechecker
 * @copyright  2025 Sean Rugge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_fileusechecker\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;
use report_fileusechecker\output\page_renderable;

/**
 * Renderer class.
 *
 * @copyright  2025 Sean Rugge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Renders the File Use Checker report page.
     *
     * @param page_renderable $page The renderable object.
     * @return string HTML for the report.
     */
    public function render_report(page_renderable $page): string {
        // Add the required styles and script modules.
        $this->page->requires->css('/report/fileusechecker/styles.css');
        $this->page->requires->js_module('report_fileusechecker', 'report');

        // Render the main report template.
        return parent::render_from_template('report_fileusechecker/report_view', $page->export_for_template());
    }
}
