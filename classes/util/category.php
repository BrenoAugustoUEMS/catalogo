<?php
namespace local_catalogo\util;

defined('MOODLE_INTERNAL') || die();

class category {

    /**
     * Retorna todas as categorias organizadas hierarquicamente (pais e filhos),
     * com informações adicionais, como o caminho completo e se estão selecionadas.
     *
     * @param int|null $categoryfilter ID da categoria selecionada (opcional).
     * @return array Lista hierárquica de categorias.
     */
    public static function get_hierarchical_categories(?int $categoryfilter = null): array {
        global $DB;

        // Busca todas as categorias visíveis.
        $categories = $DB->get_records('course_categories', ['visible' => 1], 'id, name, parent, depth, path');
        $hierarchical_categories = [];
        $pathnames_cache = []; // Cache para evitar buscas repetidas.

        // Processa as categorias de nível 1 (pais).
        foreach ($categories as $category) {
            if ((int) $category->depth === 1) {
                $hierarchical_categories[$category->id] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'path' => $category->name,
                    'is_selected' => ((int) $category->id === (int) $categoryfilter),
                    'is_parent' => true,
                    'children' => [],
                ];

                // Cache do caminho para o pai.
                $pathnames_cache[$category->id] = $category->name;
            }
        }

        // Adiciona as categorias de nível 2 (filhos) aos seus pais.
        foreach ($categories as $category) {
            if ((int) $category->depth > 1) {
                $parent_id = $category->parent; // ID do pai.
                if (isset($hierarchical_categories[$parent_id])) {
                    // Monta o caminho amigável: "Pai > Filho".
                    $friendly_path = $pathnames_cache[$parent_id] . ' > ' . $category->name;
                    // Adiciona ao cache do caminho.
                    $pathnames_cache[$category->id] = $friendly_path;

                    $hierarchical_categories[$parent_id]['children'][] = [
                        'id' => $category->id,
                        'name' => $category->name,
                        'path' => $friendly_path, // Caminho amigável do filho.
                        'parent_id' => $category->parent,
                        'is_selected' => ((int) $category->id === (int) $categoryfilter),
                        'is_parent' => false,
                    ];
                }
            }
        }
        
        return $hierarchical_categories;
    }

    /**
     * Retorna categorias em formato plano, com pais e filhos em sequência.
     *
     * @param int|null $categoryfilter ID da categoria selecionada (opcional).
     * @return array Lista plana de categorias para o Mustache.
     */
    public static function get_categories_for_menu(?int $categoryfilter = null): array {
        $hierarchical_categories = self::get_hierarchical_categories($categoryfilter);
        $formatted_categories = [];

        foreach ($hierarchical_categories as $parent) {
            // Adiciona o pai ao menu, **exceto se for o selecionado**.
            if ($parent['id'] !== $categoryfilter) {
                $formatted_categories[] = [
                    'id' => $parent['id'],
                    'name' => $parent['name'],
                    'path' => $parent['path'],
                    'is_selected' => $parent['is_selected'],
                    'is_parent' => $parent['is_parent'],
                ];
            }
        

            foreach ($parent['children'] as $child) {
                    // Adiciona os filhos ao menu, **exceto se for o selecionado**.
                if ($child['id'] !== $categoryfilter) {

                    $formatted_categories[] = [
                        'id' => $child['id'],
                        'name' => $child['name'],
                        'path' => $child['path'],
                        'parent_id' => $child['parent_id'],
                        'is_selected' => $child['is_selected'],
                        'is_parent' => $child['is_parent'],
                    ];
                }
            }
        }

        return $formatted_categories;
    }


    /**
     * Retorna os detalhes de uma única categoria específica.
     *
     * @param int $categoryid O ID da categoria.
     * @return array Detalhes da categoria (id, name, path, etc.).
     */
    public static function get_single_category_details(int $categoryid): array {
        global $DB;

        // 🔹 Definindo imagens específicas para as categorias filhas (associadas pelo ID da categoria)
        $category_images = [
            309 => 'P-IC.png',
            310 => 'P-PP.png',
            306 => 'EX-PEX.png',
            308 => 'EX-CUR.png',
            307 => 'EX-CEL.png',
            312 => 'EN-E.png',
            313 => 'EN-PEN.png',
            314 => 'EN-PIM.png',
            311 => 'DRI-MOB.png',
            // Adicione mais categorias filhas e suas imagens aqui
        ];

        // Busca a categoria pelo ID.
        $category = $DB->get_record('course_categories', ['id' => $categoryid], 'id, name, path, parent', IGNORE_MISSING);

        if ($category) {
            // Divide o caminho em IDs e monta o caminho amigável.
            $pathids = explode('/', trim($category->path, '/'));
            $pathnames = [];

            foreach ($pathids as $id) {
                $cat = $DB->get_record('course_categories', ['id' => $id], 'name', IGNORE_MISSING);
                if ($cat) {
                    $pathnames[] = $cat->name;
                }
            }

            // 🔹 Verifica se a categoria tem uma imagem no mapa; se não, usa "default.png"
            $image_url = isset($category_images[$categoryid]) 
            ? new \moodle_url("/local/catalogo/pix/category-img/{$category_images[$categoryid]}")
            : new \moodle_url("/local/catalogo/pix/category-img/default.png");

            return [
                'id' => $category->id,
                'name' => $category->name,
                'path' => implode(' > ', $pathnames), // Caminho amigável.
                'parent_id' => $category->parent, // ID do pai.
                'image_url' => $image_url->out(false),
            ];
        }

        // Caso a categoria não seja encontrada.
        return [
            'id' => $categoryid,
            'name' => 'Categoria não encontrada',
            'path' => 'Caminho não encontrado',
            'parent_id' => null,
            'image_url' => (new \moodle_url("/local/catalogo/pix/category-img/default.png"))->out(false),
        ];
    }


}


