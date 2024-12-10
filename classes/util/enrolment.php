<?php
namespace local_catalogo\util;

defined('MOODLE_INTERNAL') || die();

class enrolment {

    /**
     * Retorna os dados de inscrição para um curso.
     *
     * @param int $courseid ID do curso.
     * @return array Dados de inscrição.
     */
    public static function get_enrolment_data_for_course(int $courseid): array {
        global $DB;

        // Exemplo de busca de inscrições.
        $enrolments = $DB->get_records('enrol', ['courseid' => $courseid]);

        $formatted_enrolments = [];
        foreach ($enrolments as $enrolment) {
            $formatted_enrolments[] = [
                'id' => $enrolment->id,
                'enrol' => $enrolment->enrol,
            ];
        }

        return $formatted_enrolments;
    }
}
