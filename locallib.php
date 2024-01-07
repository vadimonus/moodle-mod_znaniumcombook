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

/**
 * Print znaniumcombook info and workaround link when JS not available.
 * @param object $book
 * @param object $cm
 * @param object $course
 * @param moodle_url $url
 * @return does not return
 */
function znaniumcombook_print_workaround($book, $cm, $course, $url) {
    global $OUTPUT;

    znaniumcombook_print_header($book, $cm, $course);
    znaniumcombook_print_heading($book, $cm, $course);
    znaniumcombook_print_intro($book, $cm, $course);

    echo html_writer::start_div('text-center');
    echo html_writer::tag('a', get_string('clicktoopen', 'znaniumcombook'), [
        'href' => $url,
        'class' => 'btn btn-primary',
        'target' => '_blank',
    ]);
    echo html_writer::end_div();

    echo $OUTPUT->footer();
    die;
}

/**
 * Print znaniumcombook header.
 * @param object $znaniumcombook
 * @param object $cm
 * @param object $course
 * @return void
 */
function znaniumcombook_print_header($znaniumcombook, $cm, $course) {
    global $PAGE, $OUTPUT;

    $PAGE->set_title($course->shortname.': '.$znaniumcombook->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($znaniumcombook);
    echo $OUTPUT->header();
}

/**
 * Print znaniumcombook heading.
 * @param object $znaniumcombook
 * @param object $cm
 * @param object $course
 * @return void
 */
function znaniumcombook_print_heading($znaniumcombook, $cm, $course) {
    global $OUTPUT;
    echo $OUTPUT->heading(format_string($znaniumcombook->name), 2);
}

/**
 * Print znaniumcombook introduction.
 * @param object $book
 * @param object $cm
 * @param object $course
 * @return void
 */
function znaniumcombook_print_intro($book, $cm, $course) {
    global $OUTPUT;

    $modinfo = get_fast_modinfo($course);
    /** @var cached_cm_info $cminfo */
    $cminfo = $modinfo->cms[$cm->id];
    $intro = $cminfo->content;
    if ($intro) {
        echo $OUTPUT->box_start('mod_introbox', 'znaniumcombookintro');
        echo $intro;
        echo $OUTPUT->box_end();
    }
}
