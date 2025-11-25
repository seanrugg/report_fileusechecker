# Moodle File Use Checker Plugin - Project Documentation

## Project Overview

**Plugin Name:** File Use Checker  
**Moodle Version:** 5.1+  
**Type:** Course Report Plugin  
**Purpose:** Identify and manage unused course files  

---

## 1. Project Structure

```
report_fileusechecker/
├── version.php
├── lang/
│   └── en/
│       └── report_fileusechecker.php
├── db/
│   └── access.php
├── classes/
│   ├── report.php
│   ├── file_scanner.php
│   ├── file_analyzer.php
│   └── output/
│       ├── renderer.php
│       └── page_renderable.php
├── templates/
│   ├── report_view.mustache
│   └── file_list_item.mustache
├── styles.css
├── index.php
└── README.md
```

---

## 2. Database & Version Configuration

### version.php
```php
<?php
defined('MOODLE_INTERNAL') || die();

$plugin->component = 'report_fileusechecker';
$plugin->version = 2025022500;
$plugin->requires = 2024100800; // Moodle 5.1
$plugin->maturity = MATURITY_BETA;
$plugin->release = '1.0.0';
$plugin->cron = 0;
```

### db/access.php
```php
<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'report/fileusechecker:view' => array(
        'captype' => 'view',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        )
    ),
    'report/fileusechecker:delete' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        )
    ),
);
```

---

## 3. Language Strings

### lang/en/report_fileusechecker.php
```php
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
```

---

## 4. Core Classes

### classes/file_scanner.php
Scans all course activities and identifies unused files.

**Key Methods:**
- `scan_course($courseid)` - Main scanning method
- `get_activity_files($cm)` - Retrieve files from activity
- `get_course_files($courseid)` - Get all course files
- `identify_unused_files()` - Compare and identify unused files

### classes/file_analyzer.php
Analyzes file usage patterns and data structures.

**Key Methods:**
- `analyze_file_usage($courseid)` - Comprehensive analysis
- `get_file_context_usage($fileid)` - Track file usage across contexts
- `calculate_statistics()` - Generate summary statistics

### classes/report.php
Main report class handling business logic.

**Key Methods:**
- `get_unused_files($courseid)` - Retrieve unused files list
- `get_summary_stats($courseid)` - Calculate summary statistics
- `delete_files($fileids)` - Delete specified files
- `format_file_size($bytes)` - Convert bytes to readable format

### classes/output/page_renderable.php
Data structure for template rendering.

**Properties:**
- `unused_files` - Array of unused file objects
- `summary_stats` - Summary statistics
- `can_delete` - Permission check

### classes/output/renderer.php
Renders the report interface.

**Key Methods:**
- `render_report()` - Main report rendering
- `render_file_table()` - Table of unused files
- `render_summary()` - Summary statistics box

---

## 5. Templates

### templates/report_view.mustache
Main template structure with summary, file list, and action buttons.

### templates/file_list_item.mustache
Individual file row template with checkboxes and location link.

---

## 6. Styling Strategy

**report_fileusechecker.css:**
- Clean, modern card-based design
- Responsive table layout
- Hover effects on rows
- Summary statistics cards
- Professional color scheme (blues/grays)
- Accessible buttons and controls

---

## 7. JavaScript Functionality

**Key Features:**
- Select/deselect all functionality
- Confirmation dialogs for deletion
- AJAX-based file deletion
- Loading indicators
- Real-time UI updates

---

## 8. Installation & Usage

### Installation Steps
1. Clone into `/mnt/data/moodle/report/fileusechecker/`
2. Visit Site Administration > Notifications
3. Install plugin
4. No database tables required (uses existing file tables)

### Access the Report
1. Navigate to any course
2. Go to Course Administration > Reports
3. Click "File Use Checker"

---

## 9. GitHub Repository Structure

```
fileusechecker/
├── .gitignore
├── README.md
├── LICENSE
├── CONTRIBUTING.md
├── report_fileusechecker/
│   └── [plugin files as listed above]
└── docs/
    ├── INSTALLATION.md
    ├── USER_GUIDE.md
    └── DEVELOPER_GUIDE.md
```

---

## 10. Key Implementation Considerations

### File Detection Algorithm
1. Scan all files in course context and activity contexts
2. Build index of all referenced files (activities, resources, embedded content)
3. Identify orphaned files not in index
4. Handle special cases:
   - Book resource chapters
   - Embedded files in text areas
   - Backup/hidden files

### Performance Optimization
- Cache scan results (5-minute TTL)
- Use batch operations for deletion
- Lazy-load file details on demand
- Index frequently accessed contexts

### Security
- Require `report/fileusechecker:delete` capability for deletion
- Validate all file IDs before deletion
- Use Moodle's file API exclusively
- Log all deletion operations

### User Experience
- Responsive design for mobile/tablet
- Clear visual feedback for actions
- Helpful tooltips and explanations
- Bulk operation confirmation
- Progress indicators for long operations

---

## 11. Testing Checklist

- [ ] File detection accuracy across activity types
- [ ] Unused file identification correctness
- [ ] Bulk selection functionality
- [ ] File deletion operations
- [ ] Permission checks
- [ ] Mobile responsiveness
- [ ] Large course performance (1000+ files)
- [ ] Backup/restore compatibility
- [ ] Language string completeness

---

## 12. Future Enhancements

- Export report to CSV/Excel
- Scheduled cleanup automation
- File usage history tracking
- Detailed file metadata display
- Integration with course analytics
- Scheduled reports via email
- File organization suggestions

---

## Development Workflow

1. Set up local Moodle 5.1 development environment
2. Clone repository to `/report/fileusechecker/`
3. Enable debugging and plugin development mode
4. Implement and test each class sequentially
5. Create templates and styling
6. Comprehensive testing across browsers and devices
7. Documentation and release