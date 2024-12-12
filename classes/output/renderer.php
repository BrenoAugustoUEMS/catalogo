<?php

namespace local_catalogo\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Renderer personalizado para o plugin Catálogo.
 */
class renderer extends \plugin_renderer_base {

    /**
     * Renderiza o conteúdo principal.
     *
     * @param array $data Dados para o template.
     * @return string HTML renderizado.
     */
    public function render_course_catalog($data) {
        #print_object($data);
        #die;
        return $this->render_from_template('local_catalogo/catalogo', $data);
    }
}
