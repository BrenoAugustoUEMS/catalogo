<?php

/**
 * Busca e formata os cursos visíveis.
 *
 * @return array Dados dos cursos formatados.
 */
function local_catalogo_get_courses() {
    global $DB;

    // Busca os cursos visíveis
    $courses = $DB->get_records('course', ['visible' => 1], 'id ASC', 'id, fullname, summary, category');

    $formatted_courses = [];
    foreach ($courses as $course) {
        // Formatar o curso
        $formatted_courses[] = [
            'id' => $course->id,
            'fullname' => $course->fullname,
            'summary' => $course->summary,
            'category_id' => $course->category,
        ];
    }

    return $formatted_courses;
}
