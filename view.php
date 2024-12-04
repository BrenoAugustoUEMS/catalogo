<?php

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Configurar a página.
$PAGE->set_url(new moodle_url('/local/catalogo/view.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Catálogo de Cursos');
$PAGE->set_pagelayout('base');
$PAGE->add_body_class('local-catalogo');
$PAGE->requires->css('/local/catalogo/styles.css');

// Captura o filtro enviado pela URL e garante que seja um inteiro ou null.
$categoryfilter = optional_param('category', 0, PARAM_INT);
$categoryfilter = ($categoryfilter > 0) ? $categoryfilter : null;

// Gera os dados para o template.
$data = local_catalogo_get_data_for_template($categoryfilter);

// Adiciona verificação de segurança para garantir que $data esteja definido.
if (empty($data)) {
    $data = ['courses' => [], 'categories' => []]; // Inicializa com valores vazios
}

// Obtém o renderer personalizado do plugin.
$output = $PAGE->get_renderer('local_catalogo');

// Renderiza a página com os dados.
echo $OUTPUT->header();
echo $output->render_course_catalog($data);
echo $OUTPUT->footer();
