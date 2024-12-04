<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Busca e formata os cursos visíveis, com suporte a filtro de categorias.
 *
 * @param int|null $categoryfilter ID da categoria para filtrar (opcional).
 * @return array Dados dos cursos formatados.
 */
function local_catalogo_get_courses($categoryfilter = null) : array {
    global $DB;

    // Condição para o filtro de categoria.
    $categorycondition = '';
    if (!empty($categoryfilter)) {
        $categorycondition = "AND category = :categoryid";
    }

    // Consulta para buscar cursos visíveis.
    $sql = "
        SELECT id, fullname, summary, category
        FROM {course}
        WHERE visible = 1 AND id != 1
        $categorycondition
        ORDER BY fullname ASC
    ";

    // Parâmetros para a consulta.
    $params = [];
    if (!empty($categoryfilter)) {
        $params['categoryid'] = $categoryfilter;
    }

    // Executa a consulta.
    $courses = $DB->get_records_sql($sql, $params);

    // Formata os cursos.
    $formatted_courses = [];
    foreach ($courses as $course) {
        $formatted_courses[] = [
            'id' => $course->id,
            'fullname' => $course->fullname,
            'summary' => $course->summary,
            'category_id' => $course->category,
            'url' => (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
            'intropage_url' => (new moodle_url('/local/intropage/index.php', ['courseid' => $course->id]))->out(false),
        ];
    }

    return $formatted_courses;
}
