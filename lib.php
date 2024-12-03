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
 * Função para coletar todos os dados processados e formatados para o template.
 *
 * @return array Dados organizados para o template.
 */
function local_catalogo_get_data_for_template($categoryfilter = null) {
    // Chama os cursos já com os detalhes.
    $courses = local_catalogo_get_courses_with_details();

    // Filtra os cursos, se um filtro de categoria for fornecido.
    if ($categoryfilter) {
        $courses = local_catalogo_filter_courses_by_category($courses, $categoryfilter);
    }


    $category_filter_options = local_catalogo_get_distinct_second_level_categories();

    // Retorna apenas os dados que serão usados no Mustache.
    return [
        'courses' => $courses,
        'categories' => $category_filter_options,
    ];
}

/**
 * Filtra os cursos com base em uma categoria fornecida.
 *
 * @param array $courses Lista de cursos completos.
 * @param string $categoryfilter Nome da categoria a ser filtrada.
 * @return array Cursos que pertencem à categoria filtrada.
 */
function local_catalogo_filter_courses_by_category(array $courses, string $categoryfilter) {
    return array_filter($courses, function($course) use ($categoryfilter) {
        // Verifica se o nome da categoria no path contém o filtro.
        return strpos($course['category']['path'], $categoryfilter) !== false;
    });
}