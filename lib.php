<?php
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib/categories.php');
require_once(__DIR__ . '/lib/courses.php');
require_once(__DIR__ . '/lib/custom_fields.php');
require_once(__DIR__ . '/lib/enrolment.php');

/**
 * Função para coletar todos os dados processados e formatados para o template.
 *
 * @param int|null $categoryfilter ID da categoria para filtrar (opcional).
 * @return array Dados organizados para o template.
 */
function local_catalogo_get_data_for_template($categoryfilter) : array {
    
    // Busca cursos com ou sem filtro de categoria.
    $courses = local_catalogo_get_courses_with_details($categoryfilter);
    // Busca as categorias para o menu suspenso.
    $categories = local_catalogo_get_second_level_categories($categoryfilter);

    return [
        'courses' => $courses,
        'categories' => $categories,
        'selected_category' => $categoryfilter, // ID da categoria ativa
    ];
}

/**
 * Busca e formata os cursos visíveis.
 *
 * @return array Dados dos cursos formatados.
 */
function local_catalogo_get_courses_with_details(?int $categoryfilter = null) : array {
    // Busca os cursos principais.
    $courses = local_catalogo_get_courses($categoryfilter); // Função interna ao próprio courses.php.

    // Itera pelos cursos para enriquecer os dados.
    foreach ($courses as &$course) {
        
        // Adiciona a categoria, dados de inscrição e campos personalizados ao curso.
        $course['category'] = local_catalogo_get_category($course['category_id']);
      
        // Processa o segundo nível, se houver.
        $pathids = $course['category']['pathids'] ?? [];
        $second_level_id = $pathids[1] ?? null; // ID do segundo nível.
        if ($second_level_id) {
            $course['second_level_category'] = local_catalogo_get_second_level_categories($course['category']['path']);
        }
        $course['enrolment'] = local_catalogo_get_enrolment_data_for_course($course['id']);
        $course['custom_fields'] = local_catalogo_get_custom_fields_for_course($course['id']);
        
    }

    return $courses; // Retorna os cursos com dados completos.
}


