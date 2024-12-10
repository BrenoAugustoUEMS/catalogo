<?php
namespace local_catalogo\util;

defined('MOODLE_INTERNAL') || die();

class enrolment {

    /**
     * Retorna os dados de inscrição para um curso, validando se o método de auto-inscrição (self enrolment) está ativo.
     *
     * @param int $courseid ID do curso.
     * @return array Dados de inscrição com validação do self enrolment.
     */
    public static function get_enrolment_details(int $courseid): array {
        global $DB;

        // Busca o método de auto-inscrição (self enrolment) associado ao curso.
        $self_enrolment = $DB->get_record('enrol', [
            'courseid' => $courseid,
            'enrol' => 'self' // Verifica especificamente o método de auto-inscrição.
        ]);

        // Caso não exista método de auto-inscrição, retorna dados padrão.
        if (!$self_enrolment) {
            return [
                'enrolstart' => 'Sem início definido',
                'enrolend' => 'Sem término definido',
                'status' => 'Indisponível',
                'self_enrol' => false, // Self enrol não está ativo.
            ];
        }

        // Formata os dados de inscrição.
        $formatted_enrolment = [
            'enrolstart' => !empty($self_enrolment->enrolstartdate) ? userdate($self_enrolment->enrolstartdate) : 'Sem início definido',
            'enrolend' => !empty($self_enrolment->enrolenddate) ? userdate($self_enrolment->enrolenddate) : 'Sem término definido',
            'status' => $self_enrolment->status == 0 ? 'Ativo' : 'Inativo',
            'self_enrol' => $self_enrolment->status == 0, // True se o self enrol estiver ativo.
        ];

        return $formatted_enrolment;
    }
}
