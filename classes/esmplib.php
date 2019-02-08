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

namespace report_esmp;

defined('MOODLE_INTERNAL') || die();

use user_picture;

/**
 * Samba report class.
 *
 * @package    report_esmp
 * @copyright  2018 Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class esmplib
{
    /**
     * Return a general course students list.
     *
     * @param int $courseid The course id
     * @return array
     */
    public static function get_course_students($courseid, $cohort = null)
    {
        global $DB;

        $userfields = user_picture::fields('u', array('username'));

        $sql = "SELECT
                    DISTINCT $userfields, co.name as cohort
                FROM {user} u
                INNER JOIN {role_assignments} ra ON ra.userid = u.id
                INNER JOIN {context} c ON c.id = ra.contextid
                LEFT JOIN {cohort_members} cm ON cm.userid = u.id AND (cm.cohortid = 2 OR cm.cohortid = 3)
                LEFT JOIN {cohort} co ON co.id = cm.cohortid";

        $sql .= " WHERE
                    ra.contextid = :contextid
                    AND ra.userid = u.id
                    AND ra.roleid = :roleid
                    AND c.instanceid = :courseinstanceid";

        if ($cohort && isset($cohort->id)) {
            $sql .= " AND cm.cohortid = :cohortid";

            $params['cohortid'] = $cohort->id;
        }

        $sql .= "ORDER BY u.firstname ASC, u.lastname ASC";

        $params['contextid'] = \context_course::instance($courseid)->id;
        $params['roleid'] = 5;
        $params['courseinstanceid'] = $courseid;

        return array_values($DB->get_records_sql($sql, $params));
    }

    public static function get_course_students_count_by_cohort($courseid)
    {
        global $DB;

        $sql = "SELECT
                  co.name, count(*) as qtd
                FROM {user} u
                INNER JOIN {role_assignments} ra ON ra.userid = u.id
                INNER JOIN {context} c ON c.id = ra.contextid
                LEFT JOIN {cohort_members} cm ON cm.userid = u.id AND (cm.cohortid = 2 OR cm.cohortid = 3)
                LEFT JOIN {cohort} co ON co.id = cm.cohortid
                WHERE
                  ra.contextid = :contextid
                  AND ra.userid = u.id
                  AND ra.roleid = :roleid
                  AND c.instanceid = :courseinstanceid
                GROUP BY co.name";

        $params['contextid'] = \context_course::instance($courseid)->id;
        $params['roleid'] = 5;
        $params['courseinstanceid'] = $courseid;

        return array_values($DB->get_records_sql($sql, $params));
    }
}