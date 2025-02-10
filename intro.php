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
 * TODO describe file intro
 *
 * @package    local_catalogo
 * @copyright  2025 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$courseid = required_param('courseid', PARAM_INT); // Obtém o ID do curso a ser exibido

// Configuração da página
$PAGE->set_url(new moodle_url('/local/catalogo/intro.php', ['courseid' => $courseid]));
$PAGE->set_context(context_course::instance($courseid));
$PAGE->set_title('Introdução ao Curso');
$PAGE->set_pagelayout('base');
$PAGE->add_body_class('local-catalogo');
$PAGE->requires->css('/local/catalogo/styles.css');

// Gera os dados para o template.
$data = local_catalogo_get_data_for_template();

// Obtém o renderer do plugin
$output = $PAGE->get_renderer('local_catalogo');

$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();
echo $output->render_course_intro($course_details);
echo $OUTPUT->footer();
