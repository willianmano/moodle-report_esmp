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
 * Students index page.
 *
 * @package    report_esmp
 * @copyright  2018 Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_esmp\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use report_esmp\esmplib;

class students_index implements renderable, templatable {

    public $course;
    public $students;
    public $cohort;
    public $cohorts;

    // Cohorts usados para gerar as estatísticas.
    protected $cohortsenabled = [2, 3];

    /**
     * Constructor.
     *
     * @param $courseid
     */
    public function __construct($course, $cohort = null) {
        global $CFG;

        $this->course = $course;

        $this->cohort = $cohort;

        $this->students = esmplib::get_course_students($course->id, $cohort);

        require_once($CFG->dirroot . "/cohort/lib.php");

        $this->cohorts = cohort_get_all_cohorts();
    }

    /**
     * Exports the data.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        $outputstudents = [];

        foreach ($this->students as $student) {
            $outputstudents[] = [
                'id' => $student->id,
                'fullname' => $student->firstname . ' ' . $student->lastname,
                'email' => $student->email,
                'cohort' => $student->cohort,
                'userpicture' => $output->user_picture($student, array('size' => 24, 'alttext' => false))
            ];
        }

        if (isset($this->cohorts['cohorts'])) {
            foreach ($this->cohorts['cohorts'] as $cohort) {
                if (in_array($cohort->id, $this->cohortsenabled)) {
                    $outputcohorts[] = ['id' => $cohort->id, 'name' => $cohort->name];
                }
            }
        }

        array_unshift($outputcohorts, ['id' => 0, 'name' => 'Todos os inscritos']);
        array_push($outputcohorts, ['id' => -1, 'name' => 'Público externo']);

        return [
            'courseid' => $this->course->id,
            'students' => $outputstudents,
            'cohorts' => $outputcohorts,
            'cohort' => $this->cohort
        ];
    }
}