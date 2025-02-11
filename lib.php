<?php
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../config.php'); // Configuração do Moodle.
require_once(__DIR__ . '/classes/util/category.php');
require_once(__DIR__ . '/classes/util/course.php');
require_once(__DIR__ . '/classes/util/enrolment.php');
require_once(__DIR__ . '/classes/util/custom_field.php');

/**
 * Função para coletar todos os dados processados e formatados para o template.
 *
 * @param int|null $categoryfilter ID da categoria para filtrar (opcional).
 * @return array Dados organizados para o template.
 */
function local_catalogo_get_data_for_template(?int $categoryfilter = null, ?string $search = null, ?int $courseid = null): array {
    // Carrega as categorias de segundo nível (somente se não estivermos buscando um curso específico).
    $categories = $courseid ? [] : \local_catalogo\util\category::get_categories_for_menu($categoryfilter);

    // Carrega os cursos com os parâmetros fornecidos (agora pode buscar por ID específico).
    $courses = \local_catalogo\util\course::get_courses_with_details($categoryfilter, $search, $courseid);

    // Conta o número de cursos filtrados.
    $course_count = count($courses);

    return [
        'courses' => $courses,
        'course_count' => $course_count,
        'categories' => $categories,
        'search_term' => $search,
        'baseurl' => (new moodle_url('/local/catalogo'))->out(),
    ];
}