<?php
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'File Use Checker';
$string['fileusechecker:view'] = 'View file use checker report';
$string['fileusechecker:delete'] = 'Delete unused files';

$string['title'] = 'File Use Checker Report';
$string['description'] = 'Identify and manage unused files in your course';

// Report strings
$string['no_unused_files'] = 'Great! No unused files found in this course.';
$string['unused_files_summary'] = 'Summary';
$string['total_unused_files'] = 'Total Unused Files: {$a}';
$string['total_file_size'] = 'Total Size: {$a}';
$string['affected_activities'] = 'Activities/Resources with Unused Files: {$a}';
$string['unused_files_list'] = 'Unused Files';

// Table headers
$string['file_name'] = 'File Name';
$string['file_size'] = 'File Size';
$string['location'] = 'Location';
$string['activity_type'] = 'Type';
$string['select'] = 'Select';

// Actions
$string['delete_selected'] = 'Delete Selected';
$string['select_all'] = 'Select All';
$string['deselect_all'] = 'Deselect All';
$string['delete_confirmation'] = 'Are you sure you want to delete {$a} file(s)? This action cannot be undone.';
$string['delete_success'] = '{$a} file(s) deleted successfully.';
$string['delete_error'] = 'An error occurred while deleting files.';

// Messages
$string['loading'] = 'Scanning course files...';
$string['scan_complete'] = 'File scan complete';
