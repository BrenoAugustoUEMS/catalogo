<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Retorna uma lista básica de cursos visíveis no Moodle.
 *
 * @return array Lista de cursos visíveis com o campo "fullname".
 */
function local_catalogo_get_courses() {
    global $DB;

    // Obter todos os cursos visíveis.
    $courses = get_courses('all', 'c.fullname ASC', 'c.id, c.fullname, c.visible');

    // Filtrar apenas os cursos visíveis.
    $visiblecourses = array_filter($courses, function($course) {
        return $course->visible; // Inclui apenas cursos visíveis.
    });

    // Retornar os cursos em um formato simples.
    $formattedcourses = [];
    foreach ($visiblecourses as $course) {
        $formattedcourses[] = [
            'id' => $course->id,           // ID do curso.
            'fullname' => $course->fullname // Nome completo do curso.
        ];
    }

    return $formattedcourses;
}
