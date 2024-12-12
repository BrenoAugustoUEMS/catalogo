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

        // Processa as categorias de nível 1 (pais).
        foreach ($categories as $category) {
            if ((int) $category->depth === 1) {
                $hierarchical_categories[$category->id] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'path' => $category->path,
                    'is_selected' => ((int) $category->id === (int) $categoryfilter),
                    'is_parent' => true,
                    'children' => [],
                ];
            }
        }

        // Adiciona as categorias de nível 2 (filhos) aos seus pais.
        foreach ($categories as $category) {
            if ((int) $category->depth > 1) {
                $parent_id = $category->parent; // ID do pai.
                if (isset($hierarchical_categories[$parent_id])) {
                    $hierarchical_categories[$parent_id]['children'][] = [
                        'id' => $category->id,
                        'name' => $category->name,
                        'path' => $category->path,
                        'parent_id' => $category->parent,
                        'is_selected' => ((int) $category->id === (int) $categoryfilter),
                        'is_parent' => false,
                    ];
                }
            }
        }
        
        #print_object($hierarchical_categories);
        #die;
        
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
                        'parent_id' => $child['parent_id'],
                        'is_selected' => $child['is_selected'],
                        'is_parent' => $child['is_parent'],
                    ];
                }
            }
        }

        #print_object($formatted_categories);
        #die;

        return $formatted_categories;
    }

}