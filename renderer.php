<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Renderer personalizado para o plugin Catálogo.
 */
class local_catalogo_renderer extends plugin_renderer_base {

    /**
     * Renderiza a lista de cursos com os dados do template.
     *
     * @param array $data Dados processados para o template.
     * @return string HTML do catálogo.
     */
    public function render_course_catalog($data) : string {
        // Renderiza o template com os dados fornecidos.
        return $this->render_from_template('local_catalogo/catalogo', $data);
    }
}
