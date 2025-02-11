<?php

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Configurar a página.
$PAGE->set_url(new moodle_url('/local/catalogo/view.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Catálogo de Cursos');
$PAGE->set_pagelayout('base');
$PAGE->add_body_class('local-catalogo');
$PAGE->requires->css('/local/catalogo/styles/global.css');
$PAGE->requires->css('/local/catalogo/styles/catalogo.css'); // 🔹 Apenas para o catálogo


// Obtém os parâmetros da URL.
$search = optional_param('search', '', PARAM_RAW_TRIMMED);
$categoryfilter = optional_param('category', 0, PARAM_INT);
$categoryfilter = ($categoryfilter > 0) ? $categoryfilter : null;

// Obtém os dados processados para o template.
$data = local_catalogo_get_data_for_template($categoryfilter, $search);

// Obtém o renderer personalizado do plugin.
$output = $PAGE->get_renderer('local_catalogo');

// Renderiza a página com os dados.
echo $OUTPUT->header();
echo $output->render_course_catalog($data);
echo $OUTPUT->footer();
