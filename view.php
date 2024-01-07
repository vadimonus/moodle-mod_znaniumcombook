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

require('../../config.php');
require_once("$CFG->dirroot/mod/znaniumcombook/lib.php");
require_once("$CFG->dirroot/mod/znaniumcombook/locallib.php");

$id = required_param('id', PARAM_INT); // Course module ID.
$forceview = optional_param('forceview', 0, PARAM_BOOL);

$cm = get_coursemodule_from_id('znaniumcombook', $id, 0, false, MUST_EXIST);
$book = $DB->get_record('znaniumcombook', ['id' => $cm->instance], '*', MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/znaniumcombook:view', $context);

// Completion and trigger events.
znaniumcombook_view($book, $course, $cm, $context);

$PAGE->set_url('/mod/znaniumcombook/view.php', ['id' => $cm->id]);

$params = [
    'contextid' => $context->id,
    'documentid' => $book->bookid,
];
if ($book->bookpage) {
    $params['page'] = $book->bookpage;
}

if (!course_get_format($course)->has_view_page()) {
    if (has_capability('moodle/course:manageactivities', $context)
        || has_capability('moodle/course:update', $context->get_course_context())
    ) {
        $forceview = true;
    }
}

$url = new moodle_url('/blocks/znanium_com/redirect.php', $params);
if (!$forceview) {
    redirect($url);
}

znaniumcombook_print_workaround($book, $cm, $course, $url);
