<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Busca os dados de autoinscrição (enrolment) para todos os cursos.
 *
 * @return array Dados de enrolamento organizados por ID do curso.
 */
function local_catalogo_get_enrolment_data_for_course($course_id) {
    global $DB;

    // Busca o método de autoinscrição.
    $enrolmethod = $DB->get_record('enrol', [
        'courseid' => $course_id,
        'enrol' => 'self'
    ], 'enrolstartdate, enrolenddate', IGNORE_MISSING);

    return [
        'enrolstart' => $enrolmethod && $enrolmethod->enrolstartdate
            ? userdate($enrolmethod->enrolstartdate, '%d/%m/%Y %H:%M')
            : 'Sem data de início',
        'enrolend' => $enrolmethod && $enrolmethod->enrolenddate
            ? userdate($enrolmethod->enrolenddate, '%d/%m/%Y %H:%M')
            : 'Sem data de término',
    ];
}
