<?php
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib/categories.php');
require_once(__DIR__ . '/lib/courses.php');
require_once(__DIR__ . '/lib/custom_fields.php');
require_once(__DIR__ . '/lib/enrolment.php');

/**
 * Busca e formata os cursos visíveis.
 *
 * @return array Dados dos cursos formatados.
 */
function local_catalogo_get_courses_with_details() {
    // Busca os cursos principais.
    $courses = local_catalogo_get_courses(); // Função interna ao próprio courses.php.

    // Itera pelos cursos para enriquecer os dados.
    foreach ($courses as &$course) {
        // Adiciona a categoria ao curso.
        $course['category'] = local_catalogo_get_category($course['category_id']);

        // Adiciona dados de enrolment.
        $course['enrolment'] = local_catalogo_get_enrolment_data_for_course($course['id']);

        // Adiciona campos personalizados.
        $course['custom_fields'] = local_catalogo_get_custom_fields_for_course($course['id']);
    }

    return $courses; // Retorna os cursos com dados completos.
}


/**
 * Coleta todos os dados processados e formatados para o template.
 *
 * @param int|null $categoryfilter ID da categoria a ser filtrada (opcional).
 * @return array Dados organizados para o template.
 */
function local_catalogo_get_data_for_template($categoryfilter = null) {
    // Busca os cursos filtrados (se houver filtro).
    $courses = local_catalogo_get_courses($categoryfilter);

    // Busca as categorias distintas do segundo nível para o filtro.
    $distinct_second_levels = local_catalogo_get_distinct_second_level_categories();

    // Retorna os dados no formato esperado pelo template.
    return [
        'courses' => $courses,
        'categories' => $distinct_second_levels,
    ];
}