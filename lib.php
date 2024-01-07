<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Book from znanium.com module
 *
 * @package mod_znaniumcombook
 * @copyright 2020 Vadim Dvorovenko
 * @copyright 2020 ООО «ЗНАНИУМ»
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

define('ZNANIUMCOMBOOK_BIBLIOGRAPHY_POSITION_BEFORE', 0);
define('ZNANIUMCOMBOOK_BIBLIOGRAPHY_POSITION_AFTER', 1);

require_once($CFG->libdir . '/completionlib.php');

/**
 * List of features supported in module
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool|null True if module supports feature, false if not, null if doesn't know
 */
function znaniumcombook_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;

        default:
            return null;
    }
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 *
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function znaniumcombook_reset_userdata($data) {
    return [];
}

/**
 * Add module instance.
 *
 * @param object $data
 * @param object $mform
 * @return int new module instance id
 */
function znaniumcombook_add_instance($data, $mform) {
    global $CFG, $DB;

    $config = get_config('znaniumcombook');

    $data->bookid = $data->book['id'];
    $data->bookdescription = $data->book['description'];
    unset($data->book);
    $data->bookpage = $data->page;
    $data->timemodified = time();
    if (!isset($data->showbibliography)) {
        $data->showbibliography = $config->showbibliography;
    }
    if (!isset($data->bibliographyposition)) {
        $data->bibliographyposition = $config->bibliographyposition;
    }
    $data->id = $DB->insert_record('znaniumcombook', $data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    if ($CFG->version > 2017051500.00) {
        \core_completion\api::update_completion_date_event($data->coursemodule, 'url', $data->id, $completiontimeexpected);
    }

    return $data->id;
}

/**
 * Update module instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function znaniumcombook_update_instance($data, $mform) {
    global $CFG, $DB;

    $config = get_config('znaniumcombook');

    $data->bookid = $data->book['id'];
    $data->bookdescription = $data->book['description'];
    unset($data->book);
    $data->bookpage = $data->page;
    $data->timemodified = time();
    $data->id = $data->instance;

    $DB->update_record('znaniumcombook', $data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    if ($CFG->version > 2017051500.00) {
        \core_completion\api::update_completion_date_event($data->coursemodule, 'znaniumcombook',
            $data->id, $completiontimeexpected);
    }

    return true;
}

/**
 * Delete module instance.
 * @param int $id
 * @return bool true
 */
function znaniumcombook_delete_instance($id) {
    global $CFG, $DB;

    if (!$book = $DB->get_record('znaniumcombook', ['id' => $id])) {
        return false;
    }

    $cm = get_coursemodule_from_instance('znaniumcombook', $id);
    if ($CFG->version > 2017051500.00) {
        \core_completion\api::update_completion_date_event($cm->id, 'znaniumcombook', $id, null);
    }

    // All context files are deleted automatically.
    $DB->delete_records('znaniumcombook', ['id' => $id]);

    return true;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See get_array_of_activities in course/lib.php
 *
 * @param object $coursemodule
 * @return cached_cm_info info
 */
function znaniumcombook_get_coursemodule_info($coursemodule) {
    global $DB;

    if (!$book = $DB->get_record('znaniumcombook', ['id' => $coursemodule->instance],
        'id, name, intro, introformat, bookdescription, showbibliography, bibliographyposition')
    ) {
        return null;
    }

    $info = new cached_cm_info();
    $info->name = $book->name;

    $info->icon = '';
    $fullurl = new moodle_url('/mod/znaniumcombook/view.php', [
        'id' => $coursemodule->id,
    ]);
    $info->onclick = "window.open('$fullurl'); return false;";
    $info->url = $fullurl;

    $info->content = '';
    if ($book->showbibliography && $book->bibliographyposition == ZNANIUMCOMBOOK_BIBLIOGRAPHY_POSITION_BEFORE) {
        $info->content .= '<div>' . htmlentities($book->bookdescription) . '</div>';
    }
    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $info->content .= format_module_intro('znaniumcombook', $book, $coursemodule->id, false);
    }
    if ($book->showbibliography && $book->bibliographyposition == ZNANIUMCOMBOOK_BIBLIOGRAPHY_POSITION_AFTER) {
        $info->content .= '<div>' . htmlentities($book->bookdescription) . '</div>';
    }

    return $info;
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function znaniumcombook_page_type_list($pagetype, $parentcontext, $currentcontext) {
    return [];
}

/**
 * Export module resource contents
 *
 * @param  stdClass $cm Course module object
 * @param  string $baseurl Base URL for file downloads
 * @return array of file content
 */
function znaniumcombook_export_contents($cm, $baseurl) {
    global $CFG, $DB;
    $contents = [];
    $context = context_module::instance($cm->id);

    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $book = $DB->get_record('znaniumcombook', ['id' => $cm->instance], '*', MUST_EXIST);

    if (!has_capability('mod/znaniumcombook:view', $context)) {
        return [];
    };

    $params = [
        'contextid' => $context->id,
        'documentid' => $book->bookid,
    ];
    if ($book->bookpage) {
        $params['page'] = $book->bookpage;
    }
    $url = new moodle_url('/blocks/znanium_com/redirect.php', $params);

    $file = [];
    $file['type'] = 'url';
    $file['filename'] = 'book';
    $file['filepath'] = null;
    $file['filesize'] = 0;
    $file['fileurl'] = $url;
    $file['timecreated'] = null;
    $file['timemodified'] = $book->timemodified;
    $file['sortorder'] = null;
    $file['userid'] = null;
    $file['author'] = null;
    $file['license'] = null;
    $contents[] = $file;

    return $contents;
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $instance   znaniumcombook object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function znaniumcombook_view($instance, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = [
        'context' => $context,
        'objectid' => $instance->id,
    ];

    $event = \mod_znaniumcombook\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('znaniumcombook', $instance);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param  cm_info $cm course module data
 * @param  int $from the time to check updates from
 * @param  array $filter  if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function znaniumcombook_check_updates_since(cm_info $cm, $from, $filter = []) {
    $updates = course_check_module_updates_since($cm, $from, ['content'], $filter);
    return $updates;
}

