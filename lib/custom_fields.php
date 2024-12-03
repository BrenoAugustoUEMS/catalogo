<?php

/**
 * Busca e formata os campos personalizados dos cursos.
 *
 * @return array Dados dos campos personalizados.
 */
function local_catalogo_get_custom_fields_for_course($course_id) {
    $handler = \core_customfield\handler::get_handler('core_course', 'course');
    $data = $handler->get_instance_data($course_id, true);

    $fields = [];
    foreach ($data as $field_data) {
        $fields[$field_data->get_field()->get('shortname')] = $field_data->get_value();
    }

    return $fields;
}