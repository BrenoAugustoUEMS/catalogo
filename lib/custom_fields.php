<?php

/**
 * Busca e formata os campos personalizados dos cursos.
 *
 * @return array Dados dos campos personalizados.
 */
function local_catalogo_get_custom_fields() {
    global $DB;

    // Exemplo de como buscar campos personalizados
    $custom_fields = []; // Suponha que você tenha campos customizados a serem buscados
    // Exemplo de busca: $custom_fields = $DB->get_records('course_custom_fields', ...);

    return $custom_fields;
}
