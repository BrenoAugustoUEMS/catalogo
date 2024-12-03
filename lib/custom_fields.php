<?php

/**
 * Busca e formata os campos personalizados para um curso.
 *
 * @param int $courseid O ID do curso.
 * @return array Campos personalizados do curso, incluindo o target_tags.
 */
function local_catalogo_get_custom_fields_for_course($courseid) {
    // Obtém todos os campos personalizados do curso.
    $customfield_handler = \core_customfield\handler::get_handler('core_course', 'course');
    $customfields_data = $customfield_handler->get_instance_data($courseid, true);

    $custom_fields = [];

    foreach ($customfields_data as $data) {
        $shortname = $data->get_field()->get('shortname'); // Nome curto do campo.
        $value = $data->get_value(); // Valor do campo.

        // Adiciona o campo ao array de custom_fields.
        $custom_fields[$shortname] = $value;
    }

    // Processa o campo 'target' para extrair tags separadas por vírgula.
    if (!empty($custom_fields['target'])) {
        $custom_fields['target_tags'] = array_map('trim', explode(',', $custom_fields['target']));
    } else {
        $custom_fields['target_tags'] = []; // Caso não haja valores no campo.
    }

    return $custom_fields;
}
