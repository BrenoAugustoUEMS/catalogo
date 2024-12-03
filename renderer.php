<?php

defined('MOODLE_INTERNAL') || die();

class local_catalogo_renderer extends plugin_renderer_base {

    /**
     * Função para renderizar o catálogo de cursos.
     *
     * @return string HTML gerado pelo template.
     */
    public function render_course_catalog() {
        // Chama a função que coleta todos os dados processados do lib.php
        $data = local_catalogo_get_data_for_template();

        // Passa os dados para o template renderizar
        return $this->render_from_template('local_catalogo/catalogo', $data);
    }
}