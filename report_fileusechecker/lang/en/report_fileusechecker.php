<?php
/**
 * File Use Checker Report - English Language Strings
 *
 * @package    report_fileusechecker
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// ========================================
// Plugin & General Strings
// ========================================

$string['pluginname'] = 'File Use Checker';
$string['description'] = 'Identify and manage unused files in your course';

// ========================================
// Capabilities
// ========================================

$string['fileusechecker:view'] = 'View file use checker report';
$string['fileusechecker:delete'] = 'Delete unused files from the course';

// ========================================
// Report Title & Navigation
// ========================================

$string['title'] = 'File Use Checker Report';
$string['reports'] = 'Reports';
$string['report_description'] = 'This report helps you identify unused files in your course that are not referenced by any activity or resource.';

// ========================================
// Summary Statistics
// ========================================

$string['unused_files_list'] = 'Unused Files';
$string['total_file_size'] = 'Total Size';
$string['total_unused_files'] = 'Total Unused Files: {$a}';
$string['affected_activities'] = 'Activities/Resources with Unused Files';
$string['scan_complete'] = 'Scan Status';
$string['scan_status'] = 'Status';
$string['ready'] = 'Ready';
$string['now'] = 'Just now';
$string['great'] = 'Great!';

// ========================================
// File Table Columns
// ========================================

$string['file_name'] = 'File Name';
$string['file_size'] = 'Size';
$string['location'] = 'Location';
$string['activity_type'] = 'Type';
$string['select'] = 'Select';

// ========================================
// Buttons & Actions
// ========================================

$string['delete_selected'] = 'Delete Selected';
$string['select_all'] = 'Select All';
$string['deselect_all'] = 'Deselect All';
$string['refresh'] = 'Refresh';
$string['delete'] = 'Delete';
$string['cancel'] = 'Cancel';
$string['confirm'] = 'Confirm';

// ========================================
// Confirmation & Messages
// ========================================

$string['delete_confirmation'] = 'Are you sure you want to delete {$a} file(s)? This action cannot be undone and the files will be permanently removed from your course.';
$string['delete_info'] = 'Note: Only files that are not used by any activity or resource will be deleted.';
$string['delete_success'] = '{$a} file(s) deleted successfully.';
$string['delete_error'] = 'An error occurred while deleting files. Please try again.';

// ========================================
// Status & Information Messages
// ========================================

$string['no_unused_files'] = 'Great! No unused files found in this course.';
$string['loading'] = 'Scanning course files...';
$string['scanning'] = 'Scanning files...';
$string['unknown'] = 'Unknown';
$string['open_in_new_window'] = 'Open in new window';

// ========================================
// Error Messages
// ========================================

$string['coursenotfound'] = 'Course not found';
$string['invalidsesskey'] = 'Invalid session key';
$string['permissiondenied'] = 'Permission denied';
$string['error_scanning_files'] = 'Error scanning course files';
$string['no_files_specified'] = 'No files specified for deletion';

// ========================================
// File Types & Activity Names
// ========================================

$string['coursefiles'] = 'Course Files';

// Common activity/resource types
$string['assign'] = 'Assignment';
$string['book'] = 'Book';
$string['forum'] = 'Forum';
$string['glossary'] = 'Glossary';
$string['resource'] = 'File';
$string['page'] = 'Page';
$string['label'] = 'Label';
$string['lesson'] = 'Lesson';
$string['quiz'] = 'Quiz';
$string['scorm'] = 'SCORM';
$string['url'] = 'URL';
$string['chat'] = 'Chat';
$string['choice'] = 'Choice';
$string['data'] = 'Database';
$string['feedback'] = 'Feedback';
$string['folder'] = 'Folder';
$string['h5p'] = 'H5P';
$string['lti'] = 'External Tool';
$string['survey'] = 'Survey';
$string['workshop'] = 'Workshop';

// ========================================
// Help Strings
// ========================================

$string['help_title'] = 'File Use Checker Help';
$string['help_description'] = 'The File Use Checker report scans your course to identify files that are stored in the file system but are not used by any activity or resource.';

$string['help_what_are_unused_files'] = 'What are unused files?';
$string['help_what_are_unused_files_help'] = 'Unused files are files that have been uploaded to your course but are not referenced by any activity or resource. They consume storage space but are not visible or accessible to students.';

$string['help_how_are_files_scanned'] = 'How are files scanned?';
$string['help_how_are_files_scanned_help'] = 'The scanner checks all files in your course context and compares them against files that are actively used by activities and resources. Files embedded in text editors, attached to assignments, included in forums, and other referenced files are marked as "used".';

$string['help_can_i_recover_deleted_files'] = 'Can I recover deleted files?';
$string['help_can_i_recover_deleted_files_help'] = 'Deleted files cannot be recovered through this interface. However, if you have enabled course backups or have access to server backups, your hosting provider may be able to restore files. Always verify that files are truly unused before deleting them.';

$string['help_safe_to_delete'] = 'Is it safe to delete these files?';
$string['help_safe_to_delete_help'] = 'Yes, this report only identifies files that are not used by any activity or resource. However, we recommend reviewing the file names before deleting to ensure they are not referenced elsewhere or needed for course functionality.';

$string['help_why_storage_matters'] = 'Why does unused file storage matter?';
$string['help_why_storage_matters_help'] = 'Unused files consume valuable server storage space. Removing them helps reduce storage costs, improves server performance, and keeps your course organized and clean.';

// ========================================
// Privacy Strings
// ========================================

$string['privacy:metadata'] = 'The File Use Checker report does not store any personal data. It only analyzes existing files in the course.';

// ========================================
// Additional Strings
// ========================================

$string['refresh_report'] = 'Refresh Report';
$string['last_scanned'] = 'Last scanned: {$a}';
$string['files_found'] = '{$a} unused file(s) found';
$string['total_storage_saved'] = 'Total storage: {$a}';
$string['processing'] = 'Processing...';
$string['please_wait'] = 'Please wait...';
$string['no_permission'] = 'You do not have permission to access this report.';
$string['course_not_found'] = 'Course not found.';
$string['session_expired'] = 'Your session has expired. Please reload the page and try again.';
