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
 * Book from znanium.com module
 *
 * @package    mod_znaniumcombook
 * @copyright COPYRIGHT
 * @license LICENSE
 */
class backup_znaniumcombook_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // Module stores no user info.

        // Define each element separated
        $znaniumcombook = new backup_nested_element('znaniumcombook', array('id'), array(
            'name', 'timemodified', 'intro', 'introformat', 'bookid', 'bookdescription', 'bookpage',
            'showbibliography', 'bibliographyposition'));

        // Build the tree
        //nothing here for URLs

        // Define sources
        $znaniumcombook->set_source_table('znaniumcombook', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        //module has no id annotations

        // Define file annotations
        $znaniumcombook->annotate_files('mod_znaniumcombook', 'intro', null); // This file area hasn't itemid

        // Return the root element (url), wrapped into standard activity structure
        return $this->prepare_activity_structure($znaniumcombook);

    }
}
