<?php
/**
 * File Scanner Class
 *
 * Scans course for unused files
 *
 * @package    report_fileusechecker
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_fileusechecker;

use context_course;
use context_module;

defined('MOODLE_INTERNAL') || die();

/**
 * File scanner for identifying unused files in a course
 */
class file_scanner {

    /**
     * @var int Course ID
     */
    private $courseid;

    /**
     * @var context_course Course context
     */
    private $context;

    /**
     * @var array Array of all files in course
     */
    private $all_files = array();

    /**
     * @var array Array of used file IDs
     */
    private $used_files = array();

    /**
     * Constructor
     *
     * @param int $courseid The course ID
     */
    public function __construct($courseid) {
        $this->courseid = $courseid;
        $this->context = context_course::instance($courseid);
    }

    /**
     * Scan the course and identify unused files
     *
     * @return array Array of unused file objects
     */
    public function scan_course() {
        // Get all files in course context and activity contexts
        $this->all_files = $this->get_course_files();

        // Get all used files (files referenced in activities/resources)
        $this->used_files = $this->get_used_files();

        // Find unused files by comparing the two sets
        $unused = $this->identify_unused_files();

        return $unused;
    }

    /**
     * Get all files stored in course and activity contexts
     *
     * @return array Array of file objects
     */
    private function get_course_files() {
        $fs = get_file_storage();
        $files = array();

        // Get files from course context
        $course_files = $fs->get_area_files(
            $this->context->id,
            'course',
            'legacy',
            false,
            'filename',
            false
        );
        $files = array_merge($files, $course_files);

        // Get files from all course modules (activities/resources)
        $cms = get_fast_modinfo($this->courseid)->get_cms();

        foreach ($cms as $cm) {
            // Get context for this module
            $modcontext = context_module::instance($cm->id);

            // Get files from different file areas for this module
            $areas = $this->get_module_file_areas($cm->modname);

            foreach ($areas as $area) {
                $module_files = $fs->get_area_files(
                    $modcontext->id,
                    'mod_' . $cm->modname,
                    $area,
                    false,
                    'filename',
                    false
                );
                $files = array_merge($files, $module_files);
            }
        }

        return $files;
    }

    /**
     * Get file areas for a specific module type
     *
     * @param string $modname Module name (e.g., 'assign', 'book', 'resource')
     * @return array Array of file area names
     */
    private function get_module_file_areas($modname) {
        $areas = array();

        // Standard areas most modules use
        $standard_areas = array('intro', 'content', 'description');

        // Module-specific areas
        switch ($modname) {
            case 'assign':
                $areas = array('intro', 'introattachments', 'submission_files', 'feedback_files');
                break;
            case 'book':
                $areas = array('intro', 'chapter');
                break;
            case 'forum':
                $areas = array('intro', 'post', 'attachment');
                break;
            case 'glossary':
                $areas = array('intro', 'entry', 'attachment');
                break;
            case 'resource':
                $areas = array('intro', 'content');
                break;
            case 'page':
                $areas = array('intro', 'content');
                break;
            case 'label':
                $areas = array('intro', 'content');
                break;
            case 'lesson':
                $areas = array('intro', 'page_contents', 'page_answers', 'page_responses');
                break;
            case 'quiz':
                $areas = array('intro', 'question');
                break;
            case 'scorm':
                $areas = array('intro', 'content');
                break;
            case 'url':
                $areas = array('intro');
                break;
            default:
                $areas = $standard_areas;
        }

        return $areas;
    }

    /**
     * Get all files that are being used (referenced in activities)
     *
     * @return array Array of used file IDs
     */
    private function get_used_files() {
        global $DB;

        $used = array();

        // Get files referenced in activities
        $cms = get_fast_modinfo($this->courseid)->get_cms();

        foreach ($cms as $cm) {
            $modcontext = context_module::instance($cm->id);

            // Get files from filearea 'intro' (editor content)
            $used = array_merge($used, $this->get_editor_files($modcontext, 'intro'));

            // Get files from filearea 'content' (content area)
            $used = array_merge($used, $this->get_editor_files($modcontext, 'content'));

            // Get files from module-specific areas that should be included
            $used = array_merge($used, $this->get_module_referenced_files($cm, $modcontext));
        }

        // Get course files that are referenced
        $used = array_merge($used, $this->get_editor_files($this->context, 'summary'));

        return $used;
    }

    /**
     * Get file IDs referenced in editor fields
     *
     * @param context $context The context
     * @param string $filearea The file area name
     * @return array Array of file IDs
     */
    private function get_editor_files($context, $filearea) {
        $fs = get_file_storage();
        $files = array();

        try {
            // Determine component name based on context type
            $component = $this->get_context_component($context);
            
            if (empty($component)) {
                return $files;
            }

            $stored_files = $fs->get_area_files(
                $context->id,
                $component,
                $filearea,
                false,
                'id',
                false
            );

            foreach ($stored_files as $file) {
                $files[] = $file->get_id();
            }
        } catch (\Exception $e) {
            // Silently continue if area doesn't exist for this context
        }

        return $files;
    }

    /**
     * Get component name for a given context
     *
     * @param context $context The context object
     * @return string Component name (e.g., 'mod_assign', 'course', etc.)
     */
    private function get_context_component($context) {
        if ($context instanceof \context_course) {
            return 'course';
        } else if ($context instanceof \context_module) {
            // For module contexts, we need to get the module name from the course module
            $cm = get_coursemodule_from_id(null, $context->instanceid, null, false, MUST_EXIST);
            return 'mod_' . $cm->modname;
        } else if ($context instanceof \context_system) {
            return 'core';
        }
        
        return '';
    }

    /**
     * Get files referenced in module-specific database fields
     *
     * @param object $cm Course module object
     * @param context $modcontext Module context
     * @return array Array of file IDs
     */
    private function get_module_referenced_files($cm, $modcontext) {
        global $DB;

        $used = array();
        $fs = get_file_storage();

        switch ($cm->modname) {
            case 'resource':
                // Resource module stores file reference
                $resource = $DB->get_record('resource', array('id' => $cm->instance));
                if ($resource && !empty($resource->mainfile)) {
                    $files = $fs->get_area_files(
                        $modcontext->id,
                        'mod_resource',
                        'content',
                        $resource->mainfile,
                        'id',
                        false
                    );
                    foreach ($files as $file) {
                        $used[] = $file->get_id();
                    }
                }
                break;

            case 'book':
                // Book chapters contain content
                $chapters = $DB->get_records('book_chapters', array('bookid' => $cm->instance));
                foreach ($chapters as $chapter) {
                    $files = $fs->get_area_files(
                        $modcontext->id,
                        'mod_book',
                        'chapter',
                        $chapter->id,
                        'id',
                        false
                    );
                    foreach ($files as $file) {
                        $used[] = $file->get_id();
                    }
                }
                break;

            case 'assign':
                // Assignment intro attachments
                $files = $fs->get_area_files(
                    $modcontext->id,
                    'mod_assign',
                    'introattachments',
                    false,
                    'id',
                    false
                );
                foreach ($files as $file) {
                    $used[] = $file->get_id();
                }
                break;

            case 'forum':
                // Forum posts with attachments
                $posts = $DB->get_records_sql(
                    "SELECT DISTINCT fp.id
                     FROM {forum_posts} fp
                     JOIN {forum_discussions} fd ON fp.discussion = fd.id
                     WHERE fd.forum = :forum",
                    array('forum' => $cm->instance)
                );

                foreach ($posts as $post) {
                    $files = $fs->get_area_files(
                        $modcontext->id,
                        'mod_forum',
                        'attachment',
                        $post->id,
                        'id',
                        false
                    );
                    foreach ($files as $file) {
                        $used[] = $file->get_id();
                    }
                }
                break;

            case 'glossary':
                // Glossary entries
                $entries = $DB->get_records('glossary_entries', array('glossaryid' => $cm->instance));
                foreach ($entries as $entry) {
                    $files = $fs->get_area_files(
                        $modcontext->id,
                        'mod_glossary',
                        'attachment',
                        $entry->id,
                        'id',
                        false
                    );
                    foreach ($files as $file) {
                        $used[] = $file->get_id();
                    }
                }
                break;
        }

        return $used;
    }

    /**
     * Identify unused files by comparing all files with used files
     *
     * @return array Array of unused file objects
     */
    private function identify_unused_files() {
        $unused = array();

        foreach ($this->all_files as $file) {
            // Skip directories
            if ($file->is_directory()) {
                continue;
            }

            // Skip backup files and hidden files
            if ($this->should_skip_file($file)) {
                continue;
            }

            // Check if file is in used files list
            if (!in_array($file->get_id(), $this->used_files)) {
                $unused[] = $file;
            }
        }

        return $unused;
    }

    /**
     * Determine if file should be skipped from analysis
     *
     * @param object $file File object
     * @return bool True if file should be skipped
     */
    private function should_skip_file($file) {
        $filename = $file->get_filename();

        // Skip common system/backup files
        $skip_patterns = array(
            '^\\.', // Hidden files
            '^Thumbs\\.db$', // Windows
            '^\\.DS_Store$', // macOS
            'backup', // Backup files
            'archive',
        );

        foreach ($skip_patterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $filename)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get course ID
     *
     * @return int Course ID
     */
    public function get_courseid() {
        return $this->courseid;
    }
}
