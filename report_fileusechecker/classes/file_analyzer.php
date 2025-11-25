<?php
/**
 * File Analyzer Class
 *
 * Analyzes file usage patterns and provides detailed insights
 *
 * @package    report_fileusechecker
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_fileusechecker;

use context_course;

defined('MOODLE_INTERNAL') || die();

/**
 * File analyzer for detailed file usage analysis
 */
class file_analyzer {

    /**
     * @var int Course ID
     */
    private $courseid;

    /**
     * @var context_course Course context
     */
    private $context;

    /**
     * @var array Cache for file analysis results
     */
    private static $analysis_cache = array();

    /**
     * Cache TTL in seconds (5 minutes)
     */
    const CACHE_TTL = 300;

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
     * Analyze file usage for the course
     *
     * @return object Analysis results object
     */
    public function analyze_file_usage() {
        // Check cache first
        $cache_key = 'course_' . $this->courseid;

        if (isset(self::$analysis_cache[$cache_key])) {
            $cached = self::$analysis_cache[$cache_key];
            if (time() - $cached['timestamp'] < self::CACHE_TTL) {
                return $cached['data'];
            }
        }

        $analysis = $this->perform_analysis();

        // Store in cache
        self::$analysis_cache[$cache_key] = array(
            'timestamp' => time(),
            'data' => $analysis,
        );

        return $analysis;
    }

    /**
     * Perform the actual file usage analysis
     *
     * @return object Analysis results
     */
    private function perform_analysis() {
        global $DB;

        $fs = get_file_storage();
        $analysis = (object) array(
            'course_id' => $this->courseid,
            'total_files' => 0,
            'total_storage' => 0,
            'file_types' => array(),
            'files_by_area' => array(),
            'files_by_module' => array(),
            'largest_files' => array(),
            'timestamp' => time(),
        );

        // Get all files in course context
        $all_files = $fs->get_area_files($this->context->id, false, false, false);

        foreach ($all_files as $file) {
            if ($file->is_directory()) {
                continue;
            }

            $analysis->total_files++;
            $analysis->total_storage += $file->get_filesize();

            // Track file types
            $mimetype = $file->get_mimetype();
            $extension = $this->get_file_extension($file->get_filename());

            if (!isset($analysis->file_types[$extension])) {
                $analysis->file_types[$extension] = array(
                    'count' => 0,
                    'size' => 0,
                    'mimetype' => $mimetype,
                );
            }

            $analysis->file_types[$extension]['count']++;
            $analysis->file_types[$extension]['size'] += $file->get_filesize();

            // Track by file area
            $area_key = $file->get_component() . '/' . $file->get_filearea();

            if (!isset($analysis->files_by_area[$area_key])) {
                $analysis->files_by_area[$area_key] = array(
                    'count' => 0,
                    'size' => 0,
                );
            }

            $analysis->files_by_area[$area_key]['count']++;
            $analysis->files_by_area[$area_key]['size'] += $file->get_filesize();
        }

        // Get largest files
        $analysis->largest_files = $this->get_largest_files($fs, 10);

        // Analyze module storage
        $analysis->files_by_module = $this->analyze_module_storage();

        return $analysis;
    }

    /**
     * Get the largest files in the course
     *
     * @param object $fs File storage instance
     * @param int $limit Maximum number of files to return
     * @return array Array of large file objects
     */
    private function get_largest_files($fs, $limit = 10) {
        $all_files = $fs->get_area_files($this->context->id, false, false, false);
        $files = array();

        foreach ($all_files as $file) {
            if (!$file->is_directory()) {
                $files[] = $file;
            }
        }

        // Sort by filesize descending
        usort($files, function($a, $b) {
            return $b->get_filesize() <=> $a->get_filesize();
        });

        return array_slice($files, 0, $limit);
    }

    /**
     * Analyze storage usage by module
     *
     * @return array Array of storage statistics by module
     */
    private function analyze_module_storage() {
        $fs = get_file_storage();
        $modstats = array();

        $cms = get_fast_modinfo($this->courseid)->get_cms();

        foreach ($cms as $cm) {
            $modcontext = \context_module::instance($cm->id);
            $component = 'mod_' . $cm->modname;

            $modfiles = $fs->get_area_files(
                $modcontext->id,
                $component,
                false,
                false
            );

            $file_count = 0;
            $total_size = 0;

            foreach ($modfiles as $file) {
                if (!$file->is_directory()) {
                    $file_count++;
                    $total_size += $file->get_filesize();
                }
            }

            if ($file_count > 0 || $total_size > 0) {
                $modstats[$cm->modname . '_' . $cm->id] = array(
                    'module_name' => $cm->modname,
                    'module_id' => $cm->id,
                    'instance_name' => $cm->name,
                    'file_count' => $file_count,
                    'total_size' => $total_size,
                );
            }
        }

        return $modstats;
    }

    /**
     * Get file extension
     *
     * @param string $filename File name
     * @return string File extension
     */
    private function get_file_extension($filename) {
        $parts = explode('.', $filename);
        if (count($parts) > 1) {
            return strtolower(end($parts));
        }
        return 'unknown';
    }

    /**
     * Get file context usage information
     *
     * @param int $fileid File ID
     * @return object File usage information
     */
    public function get_file_context_usage($fileid) {
        $fs = get_file_storage();
        $file = $fs->get_file_by_id($fileid);

        if (!$file) {
            return null;
        }

        $usage = (object) array(
            'file_id' => $fileid,
            'filename' => $file->get_filename(),
            'filesize' => $file->get_filesize(),
            'component' => $file->get_component(),
            'filearea' => $file->get_filearea(),
            'itemid' => $file->get_itemid(),
            'contextid' => $file->get_contextid(),
            'timecreated' => $file->get_timecreated(),
            'timemodified' => $file->get_timemodified(),
            'mimetype' => $file->get_mimetype(),
            'is_used' => $this->check_file_usage($fileid),
        );

        return $usage;
    }

    /**
     * Check if a file is being used
     *
     * @param int $fileid File ID
     * @return bool True if file is used, false otherwise
     */
    private function check_file_usage($fileid) {
        global $DB;

        $fs = get_file_storage();
        $file = $fs->get_file_by_id($fileid);

        if (!$file) {
            return false;
        }

        // Check if file is in editor content (references via pluginfile.php)
        $filename = $file->get_filename();

        // Check all course activities for references to this file
        $cms = get_fast_modinfo($this->courseid)->get_cms();

        foreach ($cms as $cm) {
            $modcontext = \context_module::instance($cm->id);

            // Check intro and content areas
            foreach (array('intro', 'content', 'description') as $area) {
                try {
                    $area_files = $fs->get_area_files(
                        $modcontext->id,
                        'mod_' . $cm->modname,
                        $area,
                        false,
                        'id',
                        false
                    );

                    foreach ($area_files as $areafile) {
                        if ($areafile->get_id() == $fileid) {
                            return true;
                        }
                    }
                } catch (\Exception $e) {
                    // Continue if area doesn't exist
                }
            }
        }

        return false;
    }

    /**
     * Get storage statistics
     *
     * @return object Storage statistics object
     */
    public function get_storage_stats() {
        $analysis = $this->analyze_file_usage();

        return (object) array(
            'total_files' => $analysis->total_files,
            'total_storage' => $analysis->total_storage,
            'total_storage_formatted' => $this->format_bytes($analysis->total_storage),
            'average_file_size' => $analysis->total_files > 0 ?
                $analysis->total_storage / $analysis->total_files : 0,
            'file_types_count' => count($analysis->file_types),
            'largest_files' => $analysis->largest_files,
        );
    }

    /**
     * Format bytes to human-readable format
     *
     * @param int $bytes Bytes to format
     * @return string Formatted bytes
     */
    private function format_bytes($bytes) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Clear analysis cache for a course
     *
     * @param int $courseid Course ID to clear cache for
     */
    public static function clear_cache($courseid = null) {
        if ($courseid) {
            $cache_key = 'course_' . $courseid;
            unset(self::$analysis_cache[$cache_key]);
        } else {
            self::$analysis_cache = array();
        }
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
