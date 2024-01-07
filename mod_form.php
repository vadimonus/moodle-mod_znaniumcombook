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

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/znaniumcombook/lib.php');

/**
 * Book from znanium.com module
 *
 * @package mod_znaniumcombook
 * @copyright 2020 Vadim Dvorovenko
 * @copyright 2020 ООО «ЗНАНИУМ»
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_znaniumcombook_mod_form extends moodleform_mod {
    /**
     * Define this form - called by the parent constructor
     */
    public function definition() {
        global $CFG, $PAGE;
        $mform = $this->_form;
        $config = get_config('znaniumcombook');

        // -------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), ['size' => '48']);
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $bookelements = [];
        $bookelements[] =& $mform->createElement('hidden', 'id', '', [
            'id' => 'id_book_id',
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
            [
                '@click' => 'showModal',
                'disabled' => true,
            ]
        );
        $mform->addElement('group', 'book', get_string('mod_form_book', 'znaniumcombook'), $bookelements, ' ', true);
        $mform->setType('book[id]', PARAM_INT);
        $mform->setType('book[description]', PARAM_RAW_TRIMMED);
        $mform->addRule('book', null, 'required', null, 'client');

        $PAGE->requires->js_call_amd('mod_znaniumcombook/bookpicker-lazy', 'init');

        $mform->addElement('text', 'page', get_string('mod_form_page', 'znaniumcombook'), ['size' => '5']);
        $mform->setType('page', PARAM_INT);

        // Do not display for single module course format.
        $mform->addElement('advcheckbox', 'showbibliography', get_string('mod_form_show_bibliography', 'znaniumcombook'));
        $mform->setDefault('showbibliography', $config->showbibliography);

        $this->standard_intro_elements();
        $element = $mform->getElement('introeditor');
        $attributes = $element->getAttributes();
        $attributes['rows'] = 5;
        $element->setAttributes($attributes);

        // -------------------------------------------------------
        $mform->addElement('header', 'optionssection', get_string('appearance'));

        $bibliographypositions = [
            ZNANIUMCOMBOOK_BIBLIOGRAPHY_POSITION_BEFORE => get_string('mod_form_bibliography_position_before', 'znaniumcombook'),
            ZNANIUMCOMBOOK_BIBLIOGRAPHY_POSITION_AFTER => get_string('mod_form_bibliography_position_after', 'znaniumcombook'),
        ];
        $mform->addElement('select', 'bibliographyposition',
            get_string('mod_form_bibliography_position', 'znaniumcombook'), $bibliographypositions);
        $mform->setType('bibliographyposition', PARAM_INT);
        $mform->setDefault('bibliographyposition', $config->bibliographyposition);
        $mform->disabledIf('bibliographyposition', 'showbibliography', 'notchecked');
        $mform->disabledIf('bibliographyposition', 'showdescription', 'notchecked');

        // -------------------------------------------------------
        $this->standard_coursemodule_elements();

        // -------------------------------------------------------
        $this->add_action_buttons();
    }

    /**
     * Allows the plugin to update the defaultvalues passed in to
     * the settings form (needed to set up draft areas for editor
     * and filemanager elements)
     * @param array $defaultvalues
     */
    public function data_preprocessing(&$defaultvalues) {
        if (!empty($defaultvalues['bookid'])) {
            $defaultvalues['book']['id'] = $defaultvalues['bookid'];
        }
        if (!empty($defaultvalues['bookdescription'])) {
            $defaultvalues['book']['description'] = $defaultvalues['bookdescription'];
        }
        if (!empty($defaultvalues['bookpage'])) {
            $defaultvalues['page'] = $defaultvalues['bookpage'];
        }
    }

    /**
     * Perform minimal validation on the settings form
     * @param array $data
     * @param array $files
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (empty($data['book']['id'])) {
            $errors['book'] = get_string('required');
            return $errors;
        }
        return $errors;
    }
}
