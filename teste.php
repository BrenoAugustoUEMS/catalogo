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

// Exibe os dados processados para fins de depuração.
echo html_writer::tag('h2', 'Depuração dos Dados Processados');

// Se quiser um visual mais limpo e organizado para os dados, utilize uma estrutura de visualização.
echo html_writer::tag('h3', 'Filtro de Categoria Ativo:');
echo html_writer::tag('p', ($categoryfilter !== null) ? "ID da Categoria: {$categoryfilter}" : "Nenhuma categoria ativa (sem filtro)");

// Exibe as categorias processadas.
echo html_writer::tag('h3', 'Categorias:');
echo html_writer::start_tag('pre'); // Caixa para formatar a saída.
print_object($data['categories']);
echo html_writer::end_tag('pre');

// Exibe os cursos processados.
echo html_writer::tag('h3', 'Cursos:');
echo html_writer::start_tag('pre'); // Caixa para formatar a saída.
print_object($data['courses']);
echo html_writer::end_tag('pre');

// Exibe o rodapé padrão do Moodle.
echo $OUTPUT->footer();
