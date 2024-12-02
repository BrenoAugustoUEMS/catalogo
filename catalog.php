<?php
/**
 * Página principal do Catálogo de Cursos.
 *
 * Este arquivo é responsável por carregar e exibir os cursos disponíveis na plataforma.
 *
 * @package   local_catalogo
 * @author    Breno Augusto
 * @email     brenoaugusto@uems.br
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/renderer.php'); // Inclui o renderizador.

// Configurar a página.
$PAGE->set_url(new moodle_url('/local/catalogo/catalog.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Catálogo de Cursos');
$PAGE->set_heading('Catálogo de Cursos');

// Buscar todos os cursos visíveis.
global $DB;
$courses = $DB->get_records('course', ['visible' => 1], 'id ASC');

// Processar os dados com o renderizador.
$renderer = new local_catalogo_renderer($courses);
$data = $renderer->get_data(); // Pega os dados formatados.

// Renderizar a página com o template.
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_catalogo/catalog', $data);
echo $OUTPUT->footer();

