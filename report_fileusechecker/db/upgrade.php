<?php
/**
 * File Use Checker Report - Database Upgrade
 *
 * This file handles database schema and data migrations when the plugin is upgraded.
 * It is called by Moodle when the version number in version.php changes.
 *
 * @package    report_fileusechecker
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Main upgrade function for the File Use Checker report plugin
 *
 * @param int $oldversion The version we are upgrading from
 * @return bool True if successful, False otherwise
 */
function xmldb_report_fileusechecker_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // === 1.0.0 Release ===
    // Initial release - no database tables needed

    // === UPGRADE EXAMPLES FOR FUTURE VERSIONS ===

    // Upgrade from version less than 2024100800 to 2024100900
    // if ($oldversion < 2024100900) {
    //     // Define table to be created
    //     $table = new xmldb_table('report_fileusechecker_cache');
    //
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('data', XMLDB_TYPE_LONGTEXT, null, null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    //
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
    //
    //     // Conditionally launch create table for report_fileusechecker_cache
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //
    //     // Savepoint reached.
    //     upgrade_plugin_savepoint(true, 2024100900, 'report', 'fileusechecker');
    // }

    // Upgrade from version less than 2024101000 to 2024101100
    // if ($oldversion < 2024101100) {
    //     // Example: Add a new column to an existing table
    //     $table = new xmldb_table('report_fileusechecker_cache');
    //     $field = new xmldb_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'pending');
    //
    //     // Conditionally launch add field status
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //
    //     // Savepoint reached.
    //     upgrade_plugin_savepoint(true, 2024101100, 'report', 'fileusechecker');
    // }

    return true;
}
