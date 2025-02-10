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
    
        // 🔹 Busca o método de auto-inscrição (self enrolment) associado ao curso.
        $self_enrolment = $DB->get_record('enrol', [
            'courseid' => $courseid,
            'enrol' => 'self'
        ]);
    
        // 🔹 Define o formato personalizado: "d/m/Y, H\h".
        $customformat = '%d/%m/%Y, %Hh%M';
    
        // 🔹 Garante que o retorno sempre inclua enrolstart e enrolend
        return [
            'enrolstart' => (!empty($self_enrolment) && !empty($self_enrolment->enrolstartdate)) 
                ? userdate($self_enrolment->enrolstartdate, $customformat) 
                : null, // 🔹 Agora retorna NULL se não houver data
            
            'enrolend' => (!empty($self_enrolment) && !empty($self_enrolment->enrolenddate)) 
                ? userdate($self_enrolment->enrolenddate, $customformat) 
                : null, // 🔹 Agora retorna NULL se não houver data
            
            'status' => (!empty($self_enrolment) && $self_enrolment->status == 0) ? 'Ativo' : 'Inativo',
            'self_enrol' => (!empty($self_enrolment) && $self_enrolment->status == 0),
        ];
    }    
}
