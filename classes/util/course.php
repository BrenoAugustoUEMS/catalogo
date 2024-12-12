<?php
namespace local_catalogo\util;

defined('MOODLE_INTERNAL') || die();

class course {

    /**
     * Retorna uma lista de cursos visíveis com detalhes.
     *
     * @param int|null $categoryfilter ID da categoria para filtrar (opcional).
     * @return array Lista de cursos com detalhes.
     */
    public static function get_courses_with_details(?int $categoryfilter = null): array {
        global $DB;

        // Inicializa o array de IDs de categorias.
        $categoryids = [];

        // Se um filtro de categoria for aplicado, inclua também as categorias filhas.
        if ($categoryfilter) {
            $categories = category::get_hierarchical_categories($categoryfilter);

            // Adiciona o ID da categoria pai.
            $categoryids[] = $categoryfilter;

            // Adiciona os IDs das categorias filhas.
            if (isset($categories[$categoryfilter]['children'])) {
                foreach ($categories[$categoryfilter]['children'] as $child) {
                    $categoryids[] = $child['id'];
                }
            }
        }       


        // Monta a consulta SQL para buscar os cursos.
        $sql = "SELECT id, fullname, summary, category
                FROM {course}
                WHERE visible = 1 AND id != 1"; // Exclui o "front page course" (id = 1).
        $params = [];

        // Aplica o filtro de categorias, se existir.
        if (!empty($categoryids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($categoryids, SQL_PARAMS_NAMED);
            $sql .= " AND category $insql";
            $params = array_merge($params, $inparams);
        }

        // Executa a consulta para buscar os cursos.
        $courses = $DB->get_records_sql($sql, $params);
        $formatted_courses = [];

        // Formata os cursos encontrados.
        foreach ($courses as $course) {
            // Obter os detalhes da categoria usando a classe `category`.
            $category_details = \local_catalogo\util\category::get_categories_for_menu($course->category);

            // Obter os detalhes de inscrição usando a classe `enrolment`.
            $enrol_details = \local_catalogo\util\enrolment::get_enrolment_details($course->id);

            // Formatar o curso com os detalhes necessários.
            $formatted_courses[] = [
                'id' => $course->id,
                'fullname' => $course->fullname,
                'summary' => $course->summary,
                'category' => $category_details, // Detalhes da categoria.
                'enrolment' => $enrol_details, // Detalhes da inscrição.
                'url' => (new \moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
                'intropage_url' => (new \moodle_url('/local/intropage/index.php', ['courseid' => $course->id]))->out(false),
            ];
        }

        return $formatted_courses;
    }
}
