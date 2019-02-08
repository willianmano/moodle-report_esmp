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

class index implements renderable, templatable {

    public $course;
    public $groupedstudents;

    /**
     * Constructor.
     *
     * @param $courseid
     */
    public function __construct($course) {
        $this->course = $course;

        $this->groupedstudents = esmplib::get_course_students_count_by_cohort($course->id);
    }

    /**
     * Exports the data.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        $piechart = new \core\chart_pie();
        $piechart->set_title('Gráfico de inscritos');

        $labels = [];
        $series = [];
        $totalinscritos = 0;
        foreach ($this->groupedstudents as $group) {
            if (is_null($group->name)) {
                $group->name = 'Público externo';
            }

            $labels[] = $group->name;
            $series[] = $group->qtd;

            $totalinscritos += $group->qtd;
        }

        $piechart->set_labels($labels);
        $piechart->add_series(new \core\chart_series('Total', $series));

        return [
            'courseid' => $this->course->id,
            'piechart' => $output->render_chart($piechart, false),
            'groupedstudents' => $this->groupedstudents,
            'totalinscritos' => $totalinscritos
        ];
    }
}