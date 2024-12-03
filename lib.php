<?php
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib/categories.php');
require_once(__DIR__ . '/lib/courses.php');
require_once(__DIR__ . '/lib/custom_fields.php');
require_once(__DIR__ . '/lib/enrolment.php');

/**
 * Função para coletar todos os dados processados e formatados para o template.
 *
 * @return array Dados organizados para o template.
 */
function local_catalogo_get_data_for_template() {
    // Chama os cursos já com os detalhes.
    $courses = local_catalogo_get_courses_with_details();

    // Retorna apenas os dados que serão usados no Mustache.
    return [
        'courses' => $courses,
    ];
}