<?php
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Configurar a página.
$PAGE->set_url(new moodle_url('/local/catalogo/view.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Catálogo de Cursos');
$PAGE->set_pagelayout('base');
$PAGE->requires->css('/local/catalogo/styles.css');
$PAGE->add_body_class('local-catalogo'); // Injeta uma classe no <body>.

// Captura o filtro enviado pela URL.
$categoryfilter = optional_param('category', '', PARAM_INT);

// Busca os cursos com base no filtro.
$courses = local_catalogo_get_courses($categoryfilter);

// Obtém o renderer.
$output = $PAGE->get_renderer('local_catalogo');

// Renderiza a página.
echo $OUTPUT->header();
echo $output->render_course_catalog($data); // Passa $data diretamente para o renderer.
echo $OUTPUT->footer();