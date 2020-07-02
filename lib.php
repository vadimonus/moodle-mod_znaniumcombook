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
 * @package    mod_znaniumcombook
 * @copyright COPYRIGHT
 * @license LICENSE
 */

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in module
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
            return false;
        case FEATURE_SHOW_DESCRIPTION:
            return true;

        default:
            return null;
    }
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function znaniumcombook_reset_userdata($data) {
    return array();
}
//
/**
 * Add module instance.
 * @param object $data
 * @param object $mform
 * @return int new module instance id
 */
function znaniumcombook_add_instance($data, $mform) {
    global $DB;

    $data->bookid = $data->book['id'];
    $data->bookdescription = $data->book['description'];
    $data->bookpage = $data->page;
    $data->timemodified = time();
    $data->id = $DB->insert_record('znaniumcombook', $data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'url', $data->id, $completiontimeexpected);

    return $data->id;
}

/**
 * Update module instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function znaniumcombook_update_instance($data, $mform) {
    global $DB;

    $data->bookid = $data->book['id'];
    $data->bookdescription = $data->book['description'];
    $data->bookpage = $data->page;
    $data->timemodified = time();
    $data->id = $data->instance;

    $DB->update_record('znaniumcombook', $data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'znaniumcombook', $data->id, $completiontimeexpected);

    return true;
}

/**
 * Delete module instance.
 * @param int $id
 * @return bool true
 */
function znaniumcombook_delete_instance($id) {
    global $DB;

    if (!$book = $DB->get_record('znaniumcombook', array('id' => $id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('znaniumcombook', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'znaniumcombook', $id, null);

    // note: all context files are deleted automatically
    $DB->delete_records('znaniumcombook', array('id' => $id));

    return true;
}

///**
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
    $params = array(
        'context' => $context,
        'objectid' => $instance->id
    );

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
function znaniumcombook_check_updates_since(cm_info $cm, $from, $filter = array()) {
    $updates = course_check_module_updates_since($cm, $from, array('content'), $filter);
    return $updates;
}

