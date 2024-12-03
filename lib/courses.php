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
            'url' => (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
            'intropage_url' => (new moodle_url('/local/intropage/index.php', ['courseid' => $course->id]))->out(false),
        ];

    }

    return $formatted_courses;
}