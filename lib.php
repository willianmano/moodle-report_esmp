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
 * This page lists public api for esmp reports plugin.
 *
 * @package    report_esmp
 * @copyright  2018 Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * This function extends the navigation with the esmp report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass        $course     The course to object for the tool
 * @param context         $context    The context of the course
 * @return void
 */
function report_esmp_extend_navigation_course($navigation, $course, $context) {

    if (has_capability('moodle/site:accessallgroups', $context)) {
        $url = new moodle_url('/report/esmp/students.php', array('course' => $course->id));
        $node = navigation_node::create(get_string('reportpage_students', 'report_esmp'), $url, navigation_node::TYPE_SETTING,
            null, null, new pix_icon('i/report', get_string('reportpage_students', 'report_esmp')));
        $navigation->add_node($node);
    }
}
