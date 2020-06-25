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
 * Znanium.com books repository
 *
 * @package    repository_znanium_com
 * @copyright  2020 Vadim Dvorovenko
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_znaniumcombook;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');

/**
 * Search api class
 *
 * @package    repository_znanium_com
 * @copyright  2020 Vadim Dvorovenko
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_api extends \external_api {

    /**
     * @var string
     */
    private static $searchurl = 'https://znanium.com/api/search';

    /**
     * @var string
     */
    private static $infourl = 'https://znanium.com/api/getinfo';

    /**
     * Returns description of method parameters
     * @return \external_function_parameters
     */
    public static function search_books_parameters() {
        return new \external_function_parameters(array(
            'searchquery' => new \external_value(PARAM_TEXT, 'Search query', VALUE_REQUIRED),
            'page' => new \external_value(PARAM_INT, 'Results page', VALUE_DEFAULT, 0),
        ));
    }

    /**
     * Returns description of method parameters
     * @return \external_description
     */
    public static function search_books_returns() {
        return new \external_multiple_structure(
            new \external_single_structure(array())
        );
    }

    /**
     * @param string $searchquery
     * @param int $page
     * @return array
     */
    public static function search_books($searchquery, $page = 0) {
        $domain = get_config('block_znanium_com', 'domain');
        $curl = new \curl(array('cache' => true, 'module_cache' => 'repository_znanium_com'));

        $params = array(
            'searchQuery' => $searchquery,
            'domain' => $domain,
        );
        if ($page) {
            $params['page'] = $page;
        }
        $json = $curl->get(static::$searchurl, $params);
        return json_decode($json, true);
    }

    /**
     * @param int $id
     * @return array
     */
    public function get_book_info($id) {
        $params = array(
            'id' => $id,
            'domain' => $this->domain,
        );
        $json = $this->curl->get($this->infourl, $params);
        $response = json_decode($json, true);
        return $response;
    }

    /**
     * Clean response
     */
    public static function clean_returnvalue(\external_description $description, $response) {
        return $response;
    }

}
