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

    $category_processed = local_catalogo_process_category($categoryfilter);
    // Busca as categorias para o menu suspenso
    $categories = local_catalogo_get_second_level_categories($category_processed['path'],$categoryfilter);
    // Identifica a categoria ativa (caso exista).
    $active_category = null;
    foreach ($categories as $category) {
        if (!empty($category['is_selected']) && $category['is_selected'] === true) {
            $active_category = $category['name']; // Nome da categoria ativa.
            break;
        }
    }

    return [
        'courses' => $courses,
        'categories' => $categories,
        'active_category' => $active_category,
        'baseurl' => (new moodle_url('/local/catalogo/view.php'))->out(),
    ];
}

/**
 * Busca e formata os cursos visíveis.
 *
 * @param int|null $categoryfilter Filtro de categoria opcional.
 * @return array Dados dos cursos formatados.
 */
function local_catalogo_get_courses_with_details(?int $categoryfilter = null) : array {
    // Busca os cursos principais.
    $courses = local_catalogo_get_courses($categoryfilter); // Função interna ao próprio courses.php.

    // Itera pelos cursos para enriquecer os dados.
    foreach ($courses as &$course) {
        // Adiciona os detalhes da categoria do curso.
        $course['category'] = local_catalogo_get_category($course['category_id']);
      
        // Processa o segundo nível, se houver, e adiciona os dados ao índice "category".
        $pathids = $course['category']['pathids'] ?? [];
        $second_level_id = $pathids[1] ?? null; // ID do segundo nível.

        if ($second_level_id) {
            // Busca os detalhes do segundo nível.
            $second_level_data = local_catalogo_get_second_level_categories($course['category']['path']);
            
            // Mescla as informações do segundo nível na chave 'category'.
            $course['category'] = array_merge($course['category'], [
                'second_level_id' => $second_level_data['id'] ?? null,
                'second_level_name' => $second_level_data['name'] ?? null,
            ]);
        }

        // Adiciona dados de inscrição.
        $course['enrolment'] = local_catalogo_get_enrolment_data_for_course($course['id']);

        // Adiciona campos personalizados.
        $course['custom_fields'] = local_catalogo_get_custom_fields_for_course($course['id']);
    }

    return $courses; // Retorna os cursos com dados completos.
}


