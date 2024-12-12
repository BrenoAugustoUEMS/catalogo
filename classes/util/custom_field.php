<?php
namespace local_catalogo\util;

defined('MOODLE_INTERNAL') || die();

class custom_field {

    /**
     * Busca o valor do campo personalizado "target" para um curso.
     *
     * @param int $courseid O ID do curso.
     * @return array Um array com os dados do público-alvo (hastarget e target).
     */
    public static function get_target_custom_field(int $courseid): array {
        global $DB;

        // Consulta o valor do campo personalizado com shortname "target".
        $sql = "SELECT cfd.value
                FROM {customfield_data} cfd
                JOIN {customfield_field} cff ON cfd.fieldid = cff.id
                WHERE cff.shortname = :shortname
                  AND cfd.instanceid = :courseid";

        $params = [
            'shortname' => 'target',
            'courseid' => $courseid,
        ];

        $record = $DB->get_record_sql($sql, $params);

        // Verifica se o campo personalizado foi encontrado.
        if ($record && !empty($record->value)) {
            // Processa o valor retirando as vírgulas e espaços.
            $target_tags = array_map('trim', explode(',', $record->value));

            return [
                'hastarget' => true,
                'target' => $target_tags,
            ];
        }

        // Caso o campo não esteja configurado, retorna valores padrão.
        return [
            'hastarget' => false,
            'target' => [],
        ];
    }
}
