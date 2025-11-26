<?php
/**
 * File Use Checker Report - Version Information
 *
 * @package    report_fileusechecker
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'report_fileusechecker';
$plugin->version = 2025022500;
$plugin->requires = 2024100800; // Moodle 5.1
$plugin->maturity = MATURITY_BETA;
$plugin->release = '1.0.0 (Build: 2025022500)';
$plugin->cron = 0;
$plugin->dependencies = array();

// Plugin metadata
$plugin->author = 'Sean Rugge / MCU';
$plugin->url = 'https://github.com/your-org/moodle-report_fileusechecker';
$plugin->supporturl = 'https://github.com/your-org/moodle-report_fileusechecker/issues';
