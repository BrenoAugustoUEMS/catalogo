<?php

/**
 * Busca e formata as categorias visíveis.
 *
 * @return array Dados das categorias formatados.
 */
function local_catalogo_get_category($category_id) {
    global $DB;

    // Busca o nome e o path da categoria.
    $category = $DB->get_record('course_categories', ['id' => $category_id], 'id, name, path', IGNORE_MISSING);

    if ($category) {
        $pathids = explode('/', trim($category->path, '/'));

        // Busca os nomes das categorias no caminho.
        $categories = [];
        foreach ($pathids as $catid) {
            $cat = $DB->get_record('course_categories', ['id' => $catid], 'name', IGNORE_MISSING);
            if ($cat) {
                $categories[] = $cat->name;
            }
        }

        return implode(' > ', $categories);
    }

    return 'Categoria não encontrada';
}

