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
 * Url module admin settings and defaults
 *
 * @package    mod_url
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot . '/mod/znaniumcombook/lib.php');

    //--- modedit defaults -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading(
        'znaniumcombookmodeditdefaults',
        get_string('modeditdefaults', 'admin'),
        get_string('condifmodeditdefaults', 'admin')
    ));

    $settings->add(new admin_setting_configcheckbox(
        'znaniumcombook/showbibliography',
        get_string('mod_form_show_bibliography', 'znaniumcombook'),
        '',
        0
    ));

    $bibliographypositions = array(
        ZNANIUMCOMBOOK_BIBLIOGRAPHY_POSITION_BEFORE => get_string('mod_form_bibliography_position_before', 'znaniumcombook'),
        ZNANIUMCOMBOOK_BIBLIOGRAPHY_POSITION_AFTER => get_string('mod_form_bibliography_position_after', 'znaniumcombook'),
    );
    $settings->add(new admin_setting_configselect(
        'znaniumcombook/bibliographyposition',
        get_string('mod_form_bibliography_position', 'znaniumcombook'),
        '',
        ZNANIUMCOMBOOK_BIBLIOGRAPHY_POSITION_BEFORE,
        $bibliographypositions
    ));
}
