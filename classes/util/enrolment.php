<?php
namespace local_catalogo\util;

defined('MOODLE_INTERNAL') || die();

class enrolment {

    /**
     * Verifica se o usuário está inscrito no curso.
     *
     * @param int $courseid O ID do curso.
     * @param int|null $userid O ID do usuário (opcional, padrão é o usuário atual).
     * @return bool Retorna true se o usuário estiver inscrito, false caso contrário.
     */
    public static function is_user_enrolled(int $courseid, ?int $userid = null): bool {
        global $USER;

        // Se nenhum usuário for passado, usa o usuário atual.
        if ($userid === null) {
            $userid = $USER->id;
        }

        // Obtém o contexto do curso.
        $context = \context_course::instance($courseid);

        // Verifica se o usuário está inscrito no contexto do curso.
        return is_enrolled($context, $userid, '', true);
    }

    /**
     * Retorna os detalhes de inscrição para um curso.
     *
     * @param int $courseid ID do curso.
     * @return array Dados de inscrição com validação do self enrolment.
     */
    public static function get_enrolment_details(int $courseid): array {
        global $DB;

        $self_enrolment = $DB->get_record('enrol', [
            'courseid' => $courseid,
            'enrol' => 'self'
        ]);

        $customformat = '%d/%m/%Y, %Hh%M';

        return [
            'enrolstart' => (!empty($self_enrolment) && !empty($self_enrolment->enrolstartdate)) 
                ? userdate($self_enrolment->enrolstartdate, $customformat) 
                : null,
            
            'enrolend' => (!empty($self_enrolment) && !empty($self_enrolment->enrolenddate)) 
                ? userdate($self_enrolment->enrolenddate, $customformat) 
                : null,

            'status' => (!empty($self_enrolment) && $self_enrolment->status == 0) ? 'Ativo' : 'Inativo',
            'self_enrol' => (!empty($self_enrolment) && $self_enrolment->status == 0),
            'button' => self::get_enrolment_button($courseid) // 🔹 Obtém os dados do botão de inscrição
        ];
    }
    
    /**
     * Gera os dados do botão de inscrição/acesso ao curso.
     *
     * @param int $courseid O ID do curso.
     * @param int|null $userid O ID do usuário (opcional, padrão é o usuário atual).
     * @return array Contendo 'text' (texto do botão), 'icon' (ícone do botão), e 'url' (URL do botão).
     */
    public static function get_enrolment_button(int $courseid, $userid = null): array {
        global $DB, $USER;

        if ($userid === null) {
            $userid = $USER->id;
        }

        // Obtém o contexto do curso e da categoria
        $context_course = \context_course::instance($courseid);
        $course = $DB->get_record('course', ['id' => $courseid], 'category');
        $context_category = $course ? \context_coursecat::instance($course->category) : null;

        // Verifica se o usuário está inscrito ou se tem permissão de gerente/administrador
        $is_enrolled = self::is_user_enrolled($courseid, $userid);
        $is_manager = (
            has_capability('moodle/course:update', $context_course, $userid) || 
            ($context_category && has_capability('moodle/category:manage', $context_category, $userid))
        );

        if ($is_enrolled || $is_manager) {
            return [
                'text' => 'Acesse',
                'icon' => 'fa-solid fa-right-to-bracket',
                'url' => (new \moodle_url('/course/view.php', ['id' => $courseid]))->out()
            ];
        }

        // Busca o método de autoinscrição
        $enrol = $DB->get_record('enrol', [
            'courseid' => $courseid,
            'enrol' => 'self'
        ], 'id, status', IGNORE_MISSING);

        if ($enrol && $enrol->status == 0) {
            return [
                'text' => 'Inscreva-se',
                'icon' => 'fa-solid fa-cloud-arrow-up',
                'url' => (new \moodle_url('/enrol/index.php', [
                    'id' => $courseid,
                    'enrolid' => $enrol->id
                ]))->out()
            ];
        }

        return [
            'text' => 'Indisponível',
            'icon' => 'fa-solid fa-circle-xmark',
            'url' => null
        ];
    }
}
