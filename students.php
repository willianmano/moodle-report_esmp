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
 * View students page.
 *
 * @package    report_esmp
 * @copyright  2018 Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('course', PARAM_INT);
$cohortid = optional_param('cohort', 0, PARAM_INT);

if (!$courseid) {
    print_error('invalidesmp');
}

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

$cohort = null;
if ($cohortid && $cohortid != -1) {
    $cohort = $DB->get_record('cohort', array('id' => $cohortid), '*', MUST_EXIST);
}

if ($cohortid == -1) {
    $cohort = new stdClass();
    $cohort->id = -1;
    $cohort->name = 'Público externo';
}

require_login($course);

$context = context_course::instance($course->id);

$params = array('course' => $courseid);
$url = new \moodle_url('/report/esmp/students.php', $params);

$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('reportpage_students', 'report_esmp'));
$PAGE->set_heading(get_string('reportpage_students', 'report_esmp'));

$renderable = new \report_esmp\output\students_index($course, $cohort);

$output = $PAGE->get_renderer('report_esmp');

echo $output->header();

echo $output->render($renderable);

echo $output->footer();
