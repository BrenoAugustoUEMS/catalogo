<?php

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$courseid = required_param('courseid', PARAM_INT); // Obtém o ID do curso a ser exibido

// Configuração da página
$PAGE->set_url(new moodle_url('/local/catalogo/intro.php', ['courseid' => $courseid]));
$PAGE->set_context(context_course::instance($courseid));
$PAGE->set_title('Introdução ao Curso');
$PAGE->set_pagelayout('base');
$PAGE->add_body_class('local-catalogo');
$PAGE->requires->css('/local/catalogo/styles/global.css');
$PAGE->requires->css('/local/catalogo/styles/intro.css'); // 🔹 Apenas para a introdução


// Obtém os dados processados para o template (somente o curso específico)
$data = local_catalogo_get_data_for_template(null, '', $courseid);

// Como a função retorna uma lista, pegamos apenas o primeiro elemento
$course_details = reset($data['courses']);

// Obtém o renderer do plugin
$output = $PAGE->get_renderer('local_catalogo');

$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();
echo $output->render_course_intro($course_details);
echo $OUTPUT->footer();
