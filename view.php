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

// Obter os cursos usando a função do lib.php.
$courses = local_catalogo_get_courses();

// Obter o renderer do plugin.
$output = $PAGE->get_renderer('local_catalogo');

// Passar os cursos ao renderer.
$renderedhtml = $output->render_course_catalog();

// Renderizar a página.
echo $OUTPUT->header();
echo $renderedhtml;
echo $OUTPUT->footer();
