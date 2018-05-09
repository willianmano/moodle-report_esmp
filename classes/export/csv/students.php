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

namespace report_esmp\export\csv;

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->dirroot}/lib/csvlib.class.php");

/**
 * Export students report
 *
 * @package    report_esmp
 * @copyright  2018 Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class students extends \csv_export_writer {

    protected $course;

    protected $user;

    protected $reportheaders;

    /**
     * Students constructor.
     *
     * @param string $delimiter
     * @param string $enclosure
     * @param string $mimetype
     */
    public function __construct($delimiter = 'comma', $enclosure = '"', $mimetype = 'application/download') {
        parent::__construct($delimiter, $enclosure, $mimetype);

        $this->reportheaders = [
            get_string('fullname', 'core')
        ];
    }


    /**
     * Builds and exports CSV file.
     *
     * @param $renderable
     * @param $output
     */
    public function export($renderable, $output) {
        $reportname = $renderable->course->shortname . '-' . get_string('enrolled_students', 'report_esmp');

        if ($renderable->cohort && isset($renderable->cohort->id)) {
            $reportname .= ' - ' . $renderable->cohort->name;
        }

        $this->filename = $reportname . ".csv";

        $data = $renderable->export_for_template($output);

        $this->add_data($this->reportheaders);

        foreach ($data['students'] as $student) {
            $row = [];

            $row[] = $student['fullname'];

            $this->add_data($row);
        }

        return $this->download_file();
    }
}
