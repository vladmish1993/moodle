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

$PAGE->set_url(new moodle_url('/local/completecourse/index.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('complete_course', 'local_coursecomplete'));
$PAGE->set_heading(get_string('list_of_users', 'local_coursecomplete'));

// Get all users.
$manager = new manager();
$users = $manager->get_all_users();

echo $OUTPUT->header();
$templatecontext = (object)[
    'users' => array_values($users),
    'detailbuttontext' => get_string('detail_button_text', 'local_coursecomplete'),
    'viewurl' => new moodle_url('/local/coursecomplete/viewuser.php')
];

echo $OUTPUT->render_from_template('local_coursecomplete/index', $templatecontext);
echo $OUTPUT->footer();
