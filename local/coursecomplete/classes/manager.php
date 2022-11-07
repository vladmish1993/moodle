<?php
// This file is part of Moodle Course Rollover Plugin
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
 * @package     local_coursecomplete
 * @author      Vladyslav
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coursecomplete;

class manager {

    /**
     * Get all courses completions by userid
     * @param int $userid
     * @return array
     */
    public function get_user_courses_completions(int $userid): array {
        global $DB;
        return $completions = $DB->get_records_select('course_completions', 'userid = ?', array($userid),
            'course ASC', 'id, course, timestarted, timecompleted');
    }

    /**
     * Get all users
     * @return array
     */
    public function get_all_users(): array {
        global $DB;
        return $users = $DB->get_records_select('user', 'confirmed = 1 AND lastlogin > 0', null,
            'id DESC', 'id, username, firstname, lastname');
    }
}
