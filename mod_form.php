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

require_once($CFG->dirroot . '/course/moodleform_mod.php');

//require_once($CFG->dirroot.'/mod/url/locallib.php');

/**
 * Book from znanium.com module
 *
 * @package    mod_znaniumcombook
 * @copyright COPYRIGHT
 * @license LICENSE
 */
class mod_znaniumcombook_mod_form extends moodleform_mod {
    function definition() {
        global $CFG, $PAGE;
        $mform = $this->_form;

        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size' => '48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $bookelements = array();
        $bookelements[] =& $mform->createElement('hidden', 'id', '', [
            'v-model' => 'selectedBook.id',
        ]);
        $bookelements[] =& $mform->createElement('text', 'description', '', [
            'size' => '60',
            'readonly' => true,
            'v-model' => 'selectedBook.description',
        ]);
        $bookelements[] =& $mform->createElement(
            'button',
            'select',
            get_string('open_book_picker_btn', 'znaniumcombook'),
            array(
                '@click' => 'showModal',
                'disabled' => true,
            )
        );
        $mform->addElement('group', 'book', get_string('mod_form_book', 'znaniumcombook'), $bookelements, ' ', true);
        $mform->setType('book[id]', PARAM_INT);
        $mform->setType('book[description]', PARAM_RAW_TRIMMED);
        $mform->addRule('book', null, 'required', null, 'client');

        $PAGE->requires->js_call_amd('mod_znaniumcombook/bookpicker-lazy', 'init');

        $mform->addElement('text', 'page', 'Номер страницы', array('size' => '5',));
        $mform->setType('page', PARAM_INT);

        $this->standard_intro_elements();
        $element = $mform->getElement('introeditor');
        $attributes = $element->getAttributes();
        $attributes['rows'] = 5;
        $element->setAttributes($attributes);

        //-------------------------------------------------------
        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values) {
        if (!empty($default_values['bookid'])) {
            $default_values['book']['id'] = $default_values['bookid'];
        }
        if (!empty($default_values['bookdescription'])) {
            $default_values['book']['description'] = $default_values['bookdescription'];
        }
        if (!empty($default_values['bookpage'])) {
            $default_values['page'] = $default_values['bookpage'];
        }
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (empty($data['book']['id'])) {
            $errors['book'] = get_string('required');
            return $errors;
        }
        return $errors;
    }
}
