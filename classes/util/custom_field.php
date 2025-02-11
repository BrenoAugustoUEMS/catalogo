<?php
namespace local_catalogo\util;

defined('MOODLE_INTERNAL') || die();

class custom_field {

    /**
     * Busca os valores de campos personalizados para um curso.
     *
     * @param int $courseid O ID do curso.
     * @param array $shortnames Lista de nomes curtos dos campos personalizados que queremos buscar.
     * @return array Um array associativo contendo os valores dos campos personalizados solicitados.
     */
    public static function get_custom_fields(int $courseid, array $shortnames): array {
        global $DB;

        // 🔹 Cria placeholders para os campos que queremos buscar (ex: "fluxo", "target", "ods")
        list($in_sql, $params) = $DB->get_in_or_equal($shortnames, SQL_PARAMS_NAMED);
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
            // Se for o campo ODS, tratamos os valores separadamente
            if ($record->shortname === 'ods') {
                $custom_fields['ods'] = self::process_ods_field($record->value);
            } else {
                $custom_fields[$record->shortname] = trim($record->value);
            }
        }

        return $custom_fields;
    }

    /**
     * Processa o campo "ods", extraindo os números válidos de 1 a 17.
     *
     * @param string|null $ods_value O valor bruto do campo "ods".
     * @return array Um array contendo os números de 1 a 17 extraídos do campo "ods".
     */
    public static function process_ods_field(?string $ods_value): array {
        if (empty($ods_value)) {
            return [];
        }

        // 🔹 Divide a string em partes usando a vírgula como delimitador
        $ods_parts = explode(',', $ods_value);

        // 🔹 Remove espaços extras, converte para inteiros e filtra os números válidos (1-17)
        return array_filter(array_map('intval', array_map('trim', $ods_parts)), function ($num) {
            return $num >= 1 && $num <= 17;
        });
    }
}
