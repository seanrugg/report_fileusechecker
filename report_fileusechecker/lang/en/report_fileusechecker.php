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

// ========================================
// Settings Page Strings
// ========================================

// General Settings
$string['general_settings'] = 'General Settings';
$string['general_settings_desc'] = 'Configure general behavior of the File Use Checker plugin.';
$string['enabled'] = 'Enable File Use Checker';
$string['enabled_desc'] = 'When enabled, the File Use Checker report will be available to course managers. Disable this to temporarily hide the report from all users.';

// Scanning Settings
$string['scanning_settings'] = 'Scanning Settings';
$string['scanning_settings_desc'] = 'Configure how the plugin scans for unused files.';
$string['cache_ttl'] = 'Cache Duration (seconds)';
$string['cache_ttl_desc'] = 'How long to cache scan results (in seconds). Shorter duration = more accurate results but higher CPU usage. Default is 300 seconds (5 minutes).';
$string['max_files_per_scan'] = 'Maximum Files Per Scan';
$string['max_files_per_scan_desc'] = 'Maximum number of files to process in a single scan. Courses with more files will take longer to scan. Adjust based on your server resources.';
$string['file_size_threshold'] = 'File Size Threshold (bytes)';
$string['file_size_threshold_desc'] = 'Minimum file size to track (in bytes). Files smaller than this are ignored. Default is 1048576 bytes (1 MB). Use 0 to track all files.';

// Deletion Settings
$string['deletion_settings'] = 'Deletion Settings';
$string['deletion_settings_desc'] = 'Configure file deletion behavior and safety features.';
$string['allow_deletion'] = 'Allow File Deletion';
$string['allow_deletion_desc'] = 'When enabled, users with appropriate permissions can delete unused files. When disabled, only viewing the report is allowed.';
$string['require_confirmation'] = 'Require Confirmation Before Deletion';
$string['require_confirmation_desc'] = 'When enabled, users must confirm deletion in a modal dialog. Recommended for safety.';
$string['log_deletions'] = 'Log File Deletions';
$string['log_deletions_desc'] = 'When enabled, all file deletions are logged to the Moodle event log for audit purposes.';

// Detection Settings
$string['detection_settings'] = 'File Detection Settings';
$string['detection_settings_desc'] = 'Configure which files are included in the unused file scan.';
$string['include_hidden_files'] = 'Include Hidden Files';
$string['include_hidden_files_desc'] = 'When enabled, files starting with a dot (.) are included in scans. When disabled, these are skipped as they are typically system files.';
$string['include_backup_files'] = 'Include Backup Files';
$string['include_backup_files_desc'] = 'When enabled, files with names containing "backup" or "archive" are included. When disabled, these are skipped to avoid false positives.';
$string['ignored_file_types'] = 'Ignored File Types (comma-separated extensions)';
$string['ignored_file_types_desc'] = 'File extensions to ignore during scanning (comma-separated, no dots). Examples: bak, tmp, swp, lock. Leave empty to scan all file types.';

// Display Settings
$string['display_settings'] = 'Display & UI Settings';
$string['display_settings_desc'] = 'Configure how the report is displayed to users.';
$string['items_per_page'] = 'Items Per Page';
$string['items_per_page_desc'] = 'Number of files to display per page in the report table. Higher values show more files but may impact performance on slow connections.';
$string['show_file_icons'] = 'Show File Type Icons';
$string['show_file_icons_desc'] = 'When enabled, file type icons are displayed next to file names for easier visual identification.';
$string['theme_color'] = 'UI Theme Color';
$string['theme_color_desc'] = 'Select the color scheme for the report interface.';
$string['theme_light'] = 'Light Theme';
$string['theme_dark'] = 'Dark Theme';
$string['theme_auto'] = 'Auto (Follow System)';

// Notification Settings
$string['notification_settings'] = 'Notification Settings';
$string['notification_settings_desc'] = 'Configure email notifications about unused files.';
$string['notify_admin'] = 'Notify Site Admin of Large Unused File Collections';
$string['notify_admin_desc'] = 'When enabled, the site administrator receives an email notification when a course contains unused files exceeding the threshold.';
$string['notify_threshold_mb'] = 'Notification Threshold (MB)';
$string['notify_threshold_mb_desc'] = 'Send notifications when unused files in a course exceed this size (in megabytes). Set to 0 to disable notifications.';

// Advanced Settings
$string['advanced_settings'] = 'Advanced Settings';
$string['advanced_settings_desc'] = 'Advanced configuration options for developers and experienced administrators.';
$string['debug_mode'] = 'Enable Debug Mode';
$string['debug_mode_desc'] = 'When enabled, additional debugging information is logged. Enable only for troubleshooting purposes, then disable in production.';

// Information Section
$string['info_section'] = 'Plugin Information';
$string['plugin_info'] = 'About File Use Checker';
