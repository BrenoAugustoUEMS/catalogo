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
    public static function get_custom_fields(int $courseid, array $shortnames): array {
        global $DB;

        // 🔹 Cria placeholders para os campos que queremos buscar (ex: "fluxo", "target")
        list($in_sql,$params) = $DB->get_in_or_equal($shortnames, SQL_PARAMS_NAMED);
        $params['courseid'] = $courseid;

        // 🔹 Consulta todos os campos personalizados de uma só vez
        $sql = "SELECT f.shortname, d.value
                FROM {customfield_data} d
                JOIN {customfield_field} f ON d.fieldid = f.id
                WHERE f.shortname $in_sql
                  AND d.instanceid = :courseid";

        $records = $DB->get_records_sql($sql, $params);

        // 🔹 Constrói um array associativo com os valores dos campos
        $custom_fields = [];
        foreach ($records as $record) {
            $custom_fields[$record->shortname] = trim($record->value);
        }

        return $custom_fields;
    }
}
