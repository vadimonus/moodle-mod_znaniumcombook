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

require_once($CFG->dirroot . '/mod/znaniumcombook/backup/moodle2/backup_znaniumcombook_stepslib.php');

/**
 * Backup task
 */
class backup_znaniumcombook_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        $this->add_step(new backup_znaniumcombook_activity_structure_step('znaniumcombook_structure', 'znaniumcombook.xml'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     * @param string $content
     * @return string
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot . '/mod/znaniumcombook', '#');

        // Access a list of all links in a course.
        $pattern = '#('.$base.'/index\.php\?id=)([0-9]+)#';
        $replacement = '$@ZNANIUMCOMBOOKINDEX*$2@$';
        $content = preg_replace($pattern, $replacement, $content);

        // Access the link supplying a course module id.
        $pattern = '#('.$base.'/view\.php\?id=)([0-9]+)#';
        $replacement = '$@ZNANIUMCOMBOOKVIEWBYID*$2@$';
        $content = preg_replace($pattern, $replacement, $content);

        return $content;
    }
}
