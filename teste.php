<?php

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Configurar a página.
$PAGE->set_url(new moodle_url('/local/catalogo/teste.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Catálogo de Cursos - Depuração');
$PAGE->set_pagelayout('base');
$PAGE->add_body_class('local-catalogo');
$PAGE->requires->css('/local/catalogo/styles.css');

// Captura o filtro enviado pela URL e garante que seja um inteiro ou null.
$categoryfilter = optional_param('category', 0, PARAM_INT);
$categoryfilter = ($categoryfilter > 0) ? $categoryfilter : null;

// Gera os dados para o template.
$data = local_catalogo_get_data_for_template($categoryfilter);

// Exibe o cabeçalho padrão do Moodle.
echo $OUTPUT->header();

// Container principal
echo '<div class="container mt-4">';

// Título da página.
echo '<div class="text-center mb-4">';
echo '<h1 class="text-primary">Depuração do Catálogo de Cursos</h1>';
echo '<p class="text-muted">Visualize os dados processados para análise e testes.</p>';
echo '</div>';

// Seção: Filtro de Categoria Ativo
echo '<div class="card mb-4">';
echo '<div class="card-header bg-primary text-white">';
echo '<h4>Filtro de Categoria Ativo</h4>';
echo '</div>';
echo '<div class="card-body">';
if ($categoryfilter !== null) {
    echo '<p class="text-success">ID da Categoria: <strong>' . $categoryfilter . '</strong></p>';
} else {
    echo '<p class="text-danger">Nenhuma categoria ativa (sem filtro).</p>';
}
echo '</div>';
echo '</div>';

// Seção: Categorias
echo '<div class="card mb-4">';
echo '<div class="card-header bg-info text-white">';
echo '<h4>Categorias Processadas</h4>';
echo '</div>';
echo '<div class="card-body">';
if (!empty($data['categories'])) {
    echo '<pre class="bg-light p-3 rounded border">' . htmlspecialchars(print_r($data['categories'], true)) . '</pre>';
} else {
    echo '<p class="text-danger">Nenhuma categoria encontrada.</p>';
}
echo '</div>';
echo '</div>';

// Seção: Cursos
echo '<div class="card mb-4">';
echo '<div class="card-header bg-warning text-white">';
echo '<h4>Cursos Processados</h4>';
echo '</div>';
echo '<div class="card-body">';
if (!empty($data['courses'])) {
    echo '<pre class="bg-light p-3 rounded border">' . htmlspecialchars(print_r($data['courses'], true)) . '</pre>';
} else {
    echo '<p class="text-danger">Nenhum curso encontrado.</p>';
}
echo '</div>';
echo '</div>';

// Fecha o container principal
echo '</div>';

// Exibe o rodapé padrão do Moodle.
echo $OUTPUT->footer();
