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

        print_object($categories);
        die;

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

    /**
     * Retorna os detalhes completos de uma categoria específica.
     *
     * Esta função busca uma categoria no banco de dados e retorna informações detalhadas 
     * sobre ela, incluindo o caminho completo (`path`), IDs das categorias ao longo do caminho (`pathids`), 
     * e o nome do segundo nível da categoria (`name_level`).
     *
     * @param int $categoryid O ID da categoria que será processada.
     *
     * @return array Retorna um array com os seguintes detalhes da categoria:
     *  - 'id': (int) O ID da categoria.
     *  - 'name': (string) O nome da categoria.
     *  - 'path': (string) O caminho completo da categoria (ex.: "PROPPI > Pesquisa").
     *  - 'pathids': (array) IDs das categorias ao longo do caminho (ex.: [1, 5, 7]).
     *  - 'name_level': (string) O nome do segundo nível da categoria (se aplicável). 
     *    Caso a categoria não tenha um segundo nível, retorna o nome da própria categoria.
     *
     * @throws dml_exception Lança uma exceção caso ocorra um erro ao buscar a categoria no banco de dados.
     */

    public static function get_category_details(int $category_id): array {
        global $DB;
    
        $category = $DB->get_record('course_categories', ['id' => $category_id], '*', IGNORE_MISSING);
    
        if ($category) {
            $pathids = explode('/', trim($category->path, '/'));
            $pathnames = [];
    
            // Buscar os nomes de cada ID no caminho
            foreach ($pathids as $id) {
                $cat = $DB->get_record('course_categories', ['id' => $id], 'name', IGNORE_MISSING);
                if ($cat) {
                    $pathnames[] = $cat->name;
                }
            }
    
            return [
                'id' => $category->id,
                'name' => $category->name,
                'path' => implode(' > ', $pathnames), // Nomes amigáveis do caminho
                'pathids' => $pathids, // IDs das categorias no caminho
                'name_level' => (count($pathnames) > 1) ? $pathnames[1] : $pathnames[0], // Nome do segundo nível
            ];
        }
    
        // Categoria não encontrada
        return [
            'id' => $category_id,
            'name' => 'Categoria não encontrada',
            'path' => 'Caminho não encontrado',
            'pathids' => [],
            'name_level' => 'Nível não encontrado',
        ];
    }
    
}
