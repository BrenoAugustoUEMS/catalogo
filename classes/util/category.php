<?php
namespace local_catalogo\util;

defined('MOODLE_INTERNAL') || die();

class category {

    /**
     * Retorna todas as categorias de segundo nível para o menu suspenso.
     *
     * @return array Lista de categorias de segundo nível.
     */
    public static function get_second_level_categories_for_menu(): array {
        global $DB;

        // Busca todas as categorias visíveis.
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
                        ];
                    }
                }
            }
        }

        return array_values($formatted_categories); // Retorna um array numérico para o Mustache.
    }

    /**
     * Processa o caminho de uma categoria e retorna dados do segundo nível.
     *
     * @param string $path Caminho completo da categoria (ex: "/2/5/7").
     * @param int|null $categoryfilter ID da categoria selecionada.
     * @return array Dados do segundo nível.
     */
    public static function get_second_level_from_path(string $path, ?int $categoryfilter = null): array {
        global $DB;

        $pathids = explode('/', trim($path, '/'));

        // Obtém o segundo nível.
        $second_level_id = $pathids[1] ?? null;

        if ($second_level_id) {
            $second_level_category = $DB->get_record('course_categories', ['id' => $second_level_id], 'id, name');
            if ($second_level_category) {
                return [
                    'id' => $second_level_category->id,
                    'name' => $second_level_category->name,
                    'is_selected' => ((int) $second_level_category->id === (int) $categoryfilter),
                ];
            }
        }

        // Caso o segundo nível não seja encontrado.
        return [
            'id' => null,
            'name' => 'Nível não encontrado',
            'is_selected' => false,
        ];
    }
}
