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

require('../../config.php');
require_once("$CFG->dirroot/mod/znaniumcombook/lib.php");
require_once($CFG->libdir . '/completionlib.php');

$id = required_param('id', PARAM_INT);        // Course module ID

$cm = get_coursemodule_from_id('znaniumcombook', $id, 0, false, MUST_EXIST);
$book = $DB->get_record('znaniumcombook', array('id' => $cm->instance), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/znaniumcombook:view', $context);

// Completion and trigger events.
znaniumcombook_view($book, $course, $cm, $context);

$params = array(
    'contextid' => $context->id,
    'documentid' => $book->bookid,
);
if ($book->bookpage) {
    $params['page'] = $book->bookpage;
}
$url = new moodle_url('/blocks/znanium_com/redirect.php', $params);
redirect($url);