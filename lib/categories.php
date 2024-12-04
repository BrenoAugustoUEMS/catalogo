<?php

/**
 * Busca o nome da categoria no segundo nível hierárquico ou a própria categoria, se não houver segundo nível.
 * Além disso, retorna o caminho completo da categoria e o nome da categoria por nível.
 *
 * @param int $category_id O ID da categoria.
 * @return array Um array com o caminho completo e o nome da categoria por nível.
 */
function local_catalogo_get_category($category_id) : array {
    global $DB;

    // Busca a categoria usando o ID
    $category = $DB->get_record('course_categories', ['id' => $category_id], 'id, name, path', IGNORE_MISSING);

    if ($category) {
        // Explode o path para obter os IDs de todas as categorias no caminho
        $pathids = explode('/', trim($category->path, '/'));

        // Inicializa o array de nomes por nível
        $category_name_by_level = [];

        // Popula o array de nomes por nível com as categorias ao longo do caminho
        foreach ($pathids as $catid) {
            $cat = $DB->get_record('course_categories', ['id' => $catid], 'name', IGNORE_MISSING);
            if ($cat) {
                $category_name_by_level[] = $cat->name;
            }
        }

        // Retorna o caminho completo (como string) e o nome do segundo nível
        return [
            'path' => implode(' > ', $category_name_by_level),  // Caminho completo
            'name_level' => (count($category_name_by_level) > 1) ? $category_name_by_level[1] : $category_name_by_level[0],  // Nome do segundo nível, ou o nome original
        ];
    }

    // Caso a categoria não seja encontrada
    return [
        'path' => 'Caminho não encontrado',
        'name_level' => 'Categoria não encontrada',
    ];
}

/**
 * Retorna uma lista das categorias de segundo nível (ou todas as categorias visíveis se não houver segundo nível).
 *
 * @return array Lista de categorias formatadas.
 */
function local_catalogo_get_distinct_second_level_categories() : array {
    global $DB;

    // Busca todas as categorias visíveis.
    $categories = $DB->get_records('course_categories', ['visible' => 1], 'id, name, path');

    // Formata as categorias para incluir o nome de segundo nível.
    $formatted_categories = [];

    foreach ($categories as $category) {
        // Explode o caminho da categoria.
        $pathids = explode('/', trim($category->path, '/'));

        // Verifica se há pelo menos 2 níveis no caminho.
        if (count($pathids) > 1) {
            $second_level_id = $pathids[1]; // Segundo nível.

            // Busca o nome do segundo nível.
            $second_level_category = $DB->get_record('course_categories', ['id' => $second_level_id], 'id, name');
            if ($second_level_category) {
                // Adiciona o segundo nível ao array formatado, garantindo unicidade.
                $formatted_categories[$second_level_id] = [
                    'id' => $second_level_category->id,
                    'name' => $second_level_category->name,
                ];
            }
        }
    }

    // Retorna as categorias formatadas.
    return array_values($formatted_categories); // Remove as chaves para que o Mustache as processe corretamente.
}


/**
 * Busca todas as categorias visíveis e retorna seus IDs e nomes do segundo nível.
 *
 * @return array Lista de categorias no formato [id => name_level].
 */
function local_catalogo_get_categories_for_filter() : array {
    global $DB;

    // Busca todas as categorias visíveis.
    $categories = $DB->get_records('course_categories', ['visible' => 1], 'id ASC', 'id, name, path');

    $formatted_categories = [];

    foreach ($categories as $category) {
        // Usa a função local_catalogo_get_category para obter o path e o segundo nível.
        $category_data = local_catalogo_get_category($category->id);

        // Adiciona ao array formatado.
        $formatted_categories[] = [
            'id' => $category->id, // ID da categoria.
            'name' => $category_data['name_level'], // Nome do segundo nível da categoria.
        ];
    }

    return $formatted_categories;
}
