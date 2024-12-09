<?php

/**
 * Processa uma categoria específica, retornando seu caminho completo e o nome do segundo nível.
 *
 * @param int $category_id O ID da categoria.
 * @return array Um array com o caminho completo e o nome do segundo nível.
 */
function local_catalogo_process_category(int $category_id) : array {
    global $DB;

    // Busca a categoria usando o ID
    $category = $DB->get_record('course_categories', ['id' => $category_id], 'id, name, path', IGNORE_MISSING);
    $pathids = explode('/', trim($category->path, '/'));

    if ($category) {
        // Inicializa o array de nomes por nível
        $category_name_by_level = [];

        // Popula o array de nomes por nível com as categorias ao longo do caminho
        foreach ($pathids as $catid) {
            $cat = $DB->get_record('course_categories', ['id' => $catid], 'name', IGNORE_MISSING);
            if ($cat) {
                $category_name_by_level[] = $cat->name;
            }
        }

        return [
            'path' => implode(' > ', $category_name_by_level),  // Caminho completo
            'pathids' => $pathids,
            'name_level' => (count($category_name_by_level) > 1) ? $category_name_by_level[1] : $category_name_by_level[0],  // Nome do segundo nível, ou o nome original
        ];
    }

    // Caso a categoria não seja encontrada
    return [
        'path' => 'Caminho não encontrado',
        'pathids' => $pathids,
        'name_level' => 'Categoria não encontrada',
    ];
}


/**
 * Retorna informações detalhadas sobre uma categoria específica.
 *
 * @param int $category_id O ID da categoria.
 * @return array Um array com o caminho completo e o nome do segundo nível.
 */
function local_catalogo_get_category(int $category_id) : array {
    return local_catalogo_process_category($category_id);
}


/**
 * Processa categorias de segundo nível e retorna dados detalhados.
 *
 * @param string|null $path O caminho completo da categoria (ex: "/2/5/7"). Se for null, retorna todas as categorias de segundo nível.
 * @param ?int $categoryfilter O ID da categoria selecionada no filtro (opcional).
 * @return array Se $path for fornecido, retorna os dados do segundo nível específico. Caso contrário, retorna todas as categorias de segundo nível.
 */
function local_catalogo_get_second_level_categories(?string $path = null, ?int $categoryfilter = null) : array {
    global $DB;

    // Se um caminho específico for passado, processa apenas aquele caminho.
    if ($path !== null) {
        $pathids = explode('/', trim($path, '/'));

        // Obtém o segundo nível.
        $second_level_id = $pathids[1] ?? null;

        if ($second_level_id) {
            // Busca o segundo nível pelo ID.
            $second_level_category = $DB->get_record('course_categories', ['id' => $second_level_id], 'id, name');
            if ($second_level_category) {
                return [
                    'id' => $second_level_category->id,
                    'name' => $second_level_category->name,
                    'is_selected' => ((int) $second_level_category->id === (int) $categoryfilter), // Corrigido
                ];
            }
        }

        // Retorno padrão se o segundo nível não for encontrado.
        return [
            'id' => null,
            'name' => 'Nível não encontrado',
            'is_selected' => false,
        ];
    }

    // Caso contrário, retorna todas as categorias de segundo nível para o filtro no menu suspenso.
    $categories = $DB->get_records('course_categories', ['visible' => 1], 'id, name, path');
    $formatted_categories = [];

    foreach ($categories as $category) {
        $pathids = explode('/', trim($category->path, '/'));

        // Verifica se há pelo menos 2 níveis no caminho.
        if (count($pathids) > 1) {
            $second_level_id = $pathids[1]; // Segundo nível.

            // Garante que cada segundo nível apareça apenas uma vez no array.
            if (!isset($formatted_categories[$second_level_id])) {
                $second_level_category = $DB->get_record('course_categories', ['id' => $second_level_id], 'id, name');
                if ($second_level_category) {
                    $formatted_categories[$second_level_id] = [
                        'id' => $second_level_category->id,
                        'name' => $second_level_category->name,
                        'is_selected' => ((int) $second_level_category->id === (int) $categoryfilter), // Corrigido
                    ];
                }
            }
        }
    }

    // Retorna as categorias formatadas como array numérico.
    return array_values($formatted_categories);
}

