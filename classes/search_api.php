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

namespace mod_znaniumcombook;

use core_external\external_api;
use core_external\external_description;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use curl;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');

// For compatibility with 4.1 and earlier.
if (!class_exists('\core_external\external_api')) {
    class_alias('\external_api', '\core_external\external_api');
}
if (!class_exists('\core_external\external_description')) {
    class_alias('\external_description', '\core_external\external_description');
}
if (!class_exists('\core_external\external_function_parameters')) {
    class_alias('\external_function_parameters', '\core_external\external_function_parameters');
}
if (!class_exists('\core_external\external_multiple_structure')) {
    class_alias('\external_multiple_structure', '\core_external\external_multiple_structure');
}
if (!class_exists('\core_external\external_single_structure')) {
    class_alias('\external_single_structure', '\core_external\external_single_structure');
}
if (!class_exists('\core_external\external_value')) {
    class_alias('\external_value', '\core_external\external_value');
}

/**
 * Search api class
 */
class search_api extends external_api {

    /**
     * @var string
     */
    private static $searchurl = 'https://znanium.com/api/search';

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function search_books_parameters() {
        return new external_function_parameters([
            'searchquery' => new external_value(PARAM_TEXT, 'Search query', VALUE_REQUIRED),
            'page' => new external_value(PARAM_INT, 'Results page', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Returns description of method parameters
     * @return external_description
     */
    public static function search_books_returns() {
        return new external_multiple_structure(
            new external_single_structure([])
        );
    }

    /**
     * Searches for book
     *
     * @param string $searchquery
     * @param int $page
     * @return array
     */
    public static function search_books($searchquery, $page = 0) {
        $domain = get_config('block_znanium_com', 'domain');
        $curl = new curl(['cache' => true, 'module_cache' => 'repository_znanium_com']);

        $params = [
            'searchQuery' => $searchquery,
            'domain' => $domain,
        ];
        if ($page) {
            $params['page'] = $page;
        }
        $json = $curl->get(static::$searchurl, $params);
        return json_decode($json, true);
    }

    /**
     * Clean response
     * If a response attribute is unknown from the description, we just ignore the attribute.
     * If a response attribute is incorrect, invalid_response_exception is thrown.
     * Note: this function is similar to validate parameters, however it is distinct because
     * parameters validation must be distinct from cleaning return values.
     *
     * @param external_description $description description of the return values
     * @param mixed $response the actual response
     * @return mixed response with added defaults for optional items, invalid_response_exception thrown if any problem found
     */
    public static function clean_returnvalue(external_description $description, $response) {
        return $response;
    }
}
