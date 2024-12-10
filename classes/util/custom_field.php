<?php
namespace local_catalogo\util;

defined('MOODLE_INTERNAL') || die();

class custom_field {

    /**
     * Retorna os campos personalizados de um curso.
     *
     * @param int $courseid ID do curso.
     * @return array Lista de campos personalizados.
     */
    public static function get_custom_fields_for_course(int $courseid): array {
        global $DB;

        // Busca os campos personalizados do curso.
        $custom_fields = $DB->get_records('customfield_data', ['instanceid' => $courseid]);
        $formatted_fields = [];

        foreach ($custom_fields as $field) {
            $formatted_fields[$field->fieldid] = $field->value;
        }

        return $formatted_fields;
    }
}
