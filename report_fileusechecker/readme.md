# File Use Checker - Moodle Course Report Plugin

A powerful Moodle report plugin that identifies and helps manage unused files in courses. This plugin scans course files to detect files that are stored in the file system but not used by any activity or resource, helping you reclaim storage space and maintain course organization.

## Features

- **File Detection**: Automatically scans all files in course context and activity contexts
- **Comprehensive Analysis**: Identifies unused files across all activity types (Assign, Book, Forum, Glossary, Resource, Page, Label, Lesson, Quiz, SCORM, and more)
- **Summary Statistics**: Displays total unused files, total storage consumed, and number of affected activities
- **Visual Dashboard**: Clean, modern interface with responsive design
- **Bulk Operations**: Select multiple files at once for efficient management
- **Confirmation Protection**: Double-check deletion with confirmation modal
- **Permissions-Based**: Configurable access controls for different user roles
- **Mobile Responsive**: Fully responsive design works on desktop, tablet, and mobile
- **AJAX Operations**: Smooth, non-blocking file operations
- **Audit Trail**: Deletion operations are logged for compliance

## System Requirements

- **Moodle Version**: 5.1 or later
- **PHP Version**: 7.4 or later
- **Database**: Any supported Moodle database (MySQL, PostgreSQL, etc.)

## Installation

### Via Git

1. Clone the repository into your Moodle plugins directory:

```bash
cd /path/to/moodle
git clone https://github.com/your-org/moodle-report_fileusechecker.git report/fileusechecker
```

2. Navigate to **Site Administration > Notifications** in your Moodle instance
3. The plugin will be detected and installed automatically
4. Follow the on-screen prompts to complete installation

### Manual Installation

1. Download the plugin as a ZIP file
2. Extract the files to `/report/fileusechecker/` in your Moodle installation
3. Navigate to **Site Administration > Notifications**
4. The plugin will be detected and installed automatically

## Usage

### Accessing the Report

1. Navigate to any course
2. Go to **Course Administration > Reports** (in the left menu)
3. Click on **File Use Checker**
4. The report will scan the course and display unused files

### Understanding the Report

The report displays four key metrics:

- **Unused Files**: Total number of files not used by any activity or resource
- **Total Size**: Combined storage size of all unused files
- **Affected Activities**: Number of activities/resources containing unused files
- **Scan Status**: Shows when the report was last scanned

### Managing Files

#### Viewing Details

- Click on the location link to navigate to the activity or resource containing the file
- File names, sizes, and types are clearly displayed
- Activity types are color-coded for easy identification

#### Selecting Files

- Use individual checkboxes to select specific files
- Use **Select All** to select all unused files at once
- Use **Deselect All** to clear all selections
- The delete button shows the count of selected files

#### Deleting Files

1. Select the files you want to delete
2. Click **Delete Selected**
3. Review the confirmation dialog carefully
4. Click **Delete** to confirm and permanently remove the files
5. A success message will appear after deletion

### File Detection

The plugin detects files as "used" if they are:

- Referenced in activity introduction/description fields
- Embedded in course summary sections
- Attached to forum posts or glossary entries
- Included in assignment descriptions or introductions
- Part of book chapters
- Embedded in any editor field with proper file references
- Stored in designated activity file areas (intro, content, etc.)

Files are considered "unused" if they:

- Are stored in the course file system
- Are NOT referenced by any activity or resource
- Are NOT embedded in any text editor content
- Are NOT visible or accessible to learners

## Permissions

The plugin uses two main capabilities that can be configured per role:

### report/fileusechecker:view

- **Description**: Allows users to view the File Use Checker report
- **Default**: Allowed for Teachers, Editing Teachers, Course Creators, and Managers
- **Context**: Course level

### report/fileusechecker:delete

- **Description**: Allows users to delete files identified as unused
- **Default**: Allowed for Editing Teachers, Course Creators, and Managers
- **Context**: Course level
- **Note**: Teachers can view the report but cannot delete files

## Privacy

This plugin does not store any personal or user data. It only analyzes existing files in your course and does not track user activity. The plugin complies with GDPR and other data protection regulations.

## Supported Activity Types

The plugin has specific optimizations for detecting file usage in:

- Assignments (Assign)
- Books
- Forums
- Glossaries
- File Resources (Resource)
- Pages
- Labels
- Lessons
- Quizzes
- SCORM packages
- URLs
- And more...

## Performance Considerations

- **File Scanning**: On courses with thousands of files, initial scan may take several seconds
- **Caching**: Results are cached for 5 minutes to improve performance
- **AJAX Operations**: File deletion is performed asynchronously to avoid timeout issues
- **Batch Operations**: Delete multiple files in a single operation for efficiency

### Tips for Better Performance

1. Regular cleanup of unused files
2. Archive old courses that you don't need to actively maintain
3. Encourage instructors to remove unnecessary files when creating courses
4. Use course backups before bulk deletion operations

## Troubleshooting

### Report Not Showing Any Files

- Verify that files are actually present in the course
- Check user permissions (report/fileusechecker:view capability)
- Clear browser cache and reload the page
- Check Moodle error logs for debugging information

### Cannot Delete Files

- Verify you have the report/fileusechecker:delete capability
- Confirm you are an editing teacher or manager
- Check that you have permission to modify course files
- Verify the files haven't been recently modified by another user

### Scan Takes Too Long

- This is normal for courses with very large numbers of files
- Try the operation during off-peak hours if available
- Contact your server administrator if timeouts persist

### Files Not Being Detected as Used

- The plugin uses specific file area patterns to detect usage
- Custom plugins that store files in non-standard areas may not be recognized
- Contact the plugin developer if you believe files are being incorrectly classified

## Development

### Repository Structure

```
report_fileusechecker/
├── index.php                    # Main report entry point
├── ajax_delete.php              # AJAX handler for file deletion
├── styles.css                   # Plugin styling
├── version.php                  # Version information
├── README.md                    # This file
├── lang/
│   └── en/
│       └── report_fileusechecker.php    # Language strings
├── db/
│   └── access.php               # Capabilities definition
├── classes/
│   ├── report.php               # Main report logic
│   ├── file_scanner.php         # File detection engine
│   ├── file_analyzer.php        # File analysis utilities
│   └── output/
│       ├── renderer.php         # Output rendering
│       └── page_renderable.php  # Page data structure
└── templates/
    ├── report_view.mustache     # Main report template
    ├── summary_box.mustache     # Summary statistics
    └── file_list_item.mustache  # File row template
```

### Contributing

We welcome contributions! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Coding Standards

- Follow Moodle coding guidelines
- Use PSR-12 style for PHP code
- Include PHPDoc comments for all functions
- Test thoroughly before submitting PRs

### Testing

To run tests:

```bash
php admin/tool/phpunit/cli/run.php --testcase=report_fileusechecker
```

## Known Limitations

1. **Custom File Areas**: Files stored in custom file areas by third-party plugins may not be detected as used
2. **Dynamic References**: Files referenced dynamically via JavaScript or API calls may not be detected
3. **Deleted Activities**: If an activity is deleted but files remain, they will show as unused
4. **External Files**: Files stored on external storage services may not be fully supported

## Future Enhancements

Planned features for future releases:

- CSV/Excel export functionality
- Advanced filtering and sorting
- Scheduled automated cleanup
- File usage history tracking
- Detailed analytics dashboard
- Integration with course analytics
- Scheduled email reports
- File organization suggestions

## Support

For issues, questions, or suggestions:

- **GitHub Issues**: [Report a bug or request a feature](https://github.com/seanrugg/report_fileusechecker/issues)
- **Documentation**: See the docs/ folder for detailed guides
- **Moodle Forums**: Post in the Moodle plugins forum

## License

This plugin is licensed under the GNU General Public License v3.0 - see the LICENSE file for details.

## Credits

**Developer**: Sean Rugge / MCU  
**Maintainer**: MCU  
**Contributors**: See CONTRIBUTING.md

## Changelog

### Version 1.0.0 (2025-02-25)
- Initial release
- Core file scanning functionality
- Bulk file deletion
- Responsive UI
- Multiple activity type support

## Disclaimer

This plugin modifies file storage in your Moodle instance. While every effort has been made to ensure data safety, always:

1. Back up your Moodle instance before using file deletion features
2. Test in a development environment first
3. Verify files are unused before deletion
4. Keep audit logs for compliance purposes

---

**For the latest information and updates, visit the [GitHub repository](https://github.com/seanrugg/report_fileusechecker)**
