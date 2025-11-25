<?php
/**
 * Capabilities file for the File Use Checker report plugin.
 *
 * @package    report_fileusechecker
 * @copyright  2025 Sean Rugge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    'report/fileusechecker:view' => array(
        // This capability allows users to view the report.
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
        ),
        'clonepermissionsfrom' => 'moodle/site:viewreports'
    ),

    'report/fileusechecker:delete' => array(
        // This capability allows users to delete files listed in the report.
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        ),
        // Teachers typically should not be able to delete course files without editing rights.
        'clonepermissionsfrom' => 'moodle/course:managefiles'
    ),
);
