<?php

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib.php');

/**
 * Renderer personalizado para o plugin Catálogo.
 */
class local_catalogo_renderer extends plugin_renderer_base {

    /**
     * Renderiza a lista de cursos com os dados do template.
     *
     * @return string HTML do catálogo.
     */
    public function render_course_catalog() {
        // Busca os dados processados para o template.
        $data = local_catalogo_get_data_for_template();
        
        // Testar %data
        // print_object($data);
        // die;

        // Renderiza o template.
        return $this->render_from_template('local_catalogo/catalogo', $data);
    }
}
