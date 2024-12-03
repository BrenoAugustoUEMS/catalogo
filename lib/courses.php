<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Busca e formata os cursos visíveis.
 *
 * @return array Dados dos cursos formatados.
 */
function local_catalogo_get_courses() {
    global $DB;

    // Busca os cursos visíveis (ignora o curso com ID=1).
    $courses = $DB->get_records_sql(
        "SELECT id, fullname, summary, category 
         FROM {course} 
         WHERE visible = 1 AND id != 1 
         ORDER BY id ASC"
    );

    // Formata os cursos.
    $formatted_courses = [];
    foreach ($courses as $course) {
        $formatted_courses[] = [
            'id' => $course->id,
            'fullname' => $course->fullname,
            'summary' => $course->summary,
            'category_id' => $course->category,
        ];
    }

    return $formatted_courses;
}

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
