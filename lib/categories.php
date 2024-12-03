<?php

/**
 * Busca e formata as categorias visíveis.
 *
 * @return array Dados das categorias formatados.
 */
function local_catalogo_get_categories() {
    global $DB;

    // Busca as categorias visíveis
    $categories = $DB->get_records('course_categories', ['visible' => 1], 'name ASC', 'id, name, path');

    $formatted_categories = [];
    foreach ($categories as $category) {
        $formatted_categories[] = [
            'id' => $category->id,
            'name' => $category->name,
            'path' => $category->path,  // Caminho da categoria
        ];
    }

    return $formatted_categories;
}
