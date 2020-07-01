<?php
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
