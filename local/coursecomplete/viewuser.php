<?php
// This file is part of Moodle Course Complete Plugin
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

use local_coursecomplete\manager;

require_once(__DIR__ . '/../../config.php');

global $DB;

require_login();

$context = context_system::instance();
require_capability('local/coursecomplete:viewreports', $context);

$PAGE->set_url(new moodle_url('/local/completecourse/viewuser.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('course_reports', 'local_coursecomplete'));
$PAGE->set_heading(get_string('list_of_courses', 'local_coursecomplete'));

$userid = optional_param('userid', null, PARAM_INT);

if ($userid) {
    // Add extra data to the form.
    $manager = new manager();
    $completionsdb = $manager->get_user_courses_completions($userid);

    $completions = [];
    foreach ($completionsdb as $completiondb) {
        $completion[$completiondb->course] = $completiondb->timecompleted;
    }

    $courses = [];
    if ($mycourses = enrol_get_all_users_courses($userid, true, null, 'visible DESC,sortorder ASC')) {
        foreach ($mycourses as $mycourse) {
            if ($mycourse->category) {
                context_helper::preload_from_record($mycourse);
                $ccontext = context_course::instance($mycourse->id);
                $class = '';
                if ($mycourse->visible == 0) {
                    if (!has_capability('moodle/course:viewhiddencourses', $ccontext)) {
                        continue;
                    }
                    $class = 'class="dimmed"';
                }
                $url = new moodle_url("/user/view.php?id={$userid}&amp;course={$mycourse->id}");
                $courselisting = "<a href=\"{$url}\">" . $ccontext->get_context_name(false) . "</a>";

                $courses[$mycourse->fullname] = [
                    'title' => $courselisting,
                    'name' => $mycourse->fullname,
                    'state' => $completion[$mycourse->id] ? get_string('complete', 'local_coursecomplete') :
                        get_string('not_complete', 'local_coursecomplete'),
                    'date' => $completion[$mycourse->id] ? userdate($completion[$mycourse->id]) : '',
                ];

            }
        }
    }

    ksort($courses);
} else {
    throw new invalid_parameter_exception(get_string('user_not_found', 'local_coursecomplete'));
}


echo $OUTPUT->header();

$templatecontext = (object)[
    'table_heading_course' => get_string('table_heading_course', 'local_coursecomplete'),
    'table_heading_state' => get_string('table_heading_state', 'local_coursecomplete'),
    'table_heading_date' => get_string('table_heading_date', 'local_coursecomplete'),
    'courses' => array_values($courses),
    'back_url_text' => get_string('back_url_text', 'local_coursecomplete'),
    'backurl' => new moodle_url('/local/coursecomplete/index.php')
];

echo $OUTPUT->render_from_template('local_coursecomplete/viewuser', $templatecontext);


echo $OUTPUT->footer();
