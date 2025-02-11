<?php
namespace local_catalogo\util;

defined('MOODLE_INTERNAL') || die();

class course {

    /**
     * Retorna uma lista de cursos visíveis com detalhes.
     *
     * @param int|null $categoryfilter ID da categoria para filtrar (opcional).
     * @param string|null $search Termo de busca pelo título do curso (opcional).
     * @param int|null $courseid ID específico de um curso (opcional).
     * @return array Lista de cursos com detalhes.
     */
    public static function get_courses_with_details(?int $categoryfilter = null, ?string $search = '', ?int $courseid = null): array {
        global $DB;

        $params = [];
        $conditions = ["visible = 1", "id != 1"]; // Filtra cursos visíveis e exclui a página inicial (id = 1)

        // 🔹 Se um courseid for passado, busca apenas esse curso
        if ($courseid) {
            $conditions[] = "id = :courseid";
            $params['courseid'] = $courseid;
        } else {
            // 🔹 Se um filtro de categoria for aplicado, inclui também as categorias filhas.
            $categoryids = [];
            if ($categoryfilter) {
                $categories = category::get_hierarchical_categories($categoryfilter);
                $categoryids[] = $categoryfilter;

                if (isset($categories[$categoryfilter]['children'])) {
                    foreach ($categories[$categoryfilter]['children'] as $child) {
                        $categoryids[] = $child['id'];
                    }
                }
            }

            // 🔹 Aplica o filtro de categorias, se existir.
            if (!empty($categoryids)) {
                list($insql, $inparams) = $DB->get_in_or_equal($categoryids, SQL_PARAMS_NAMED);
                $conditions[] = "category $insql";
                $params = array_merge($params, $inparams);
            }

            // 🔍 Aplica filtro de busca pelo título (fullname)
            if (!empty($search)) {
                $search = trim($search);
                $search = str_replace(['%', '_'], ['\%', '\_'], $search); // Escapa caracteres especiais
                $searchterms = explode(' ', $search);
                $searchconditions = [];

                foreach ($searchterms as $index => $term) {
                    $paramname = "search{$index}";
                    $searchconditions[] = "LOWER(fullname) LIKE LOWER(:{$paramname})";
                    $params[$paramname] = "%{$term}%";
                }

                $conditions[] = "(" . implode(" AND ", $searchconditions) . ")";
            }
        }

        // 🔹 Monta a consulta SQL final
        $sql = "SELECT id, fullname, summary, category
                FROM {course}
                WHERE " . implode(" AND ", $conditions) . "
                ORDER BY id DESC"; // Ordena por cursos mais recentes

        // 🔹 Executa a consulta para buscar os cursos.
        $courses = $DB->get_records_sql($sql, $params);
        $formatted_courses = [];

        // 🔹 Formata os cursos encontrados.
        foreach ($courses as $course) {
            // 🔹 Obtém os detalhes da categoria.
            $category_details = \local_catalogo\util\category::get_single_category_details($course->category);

            // 🔹 Obtém os detalhes de inscrição.
            $enrol_details = \local_catalogo\util\enrolment::get_enrolment_details($course->id);

            // 🔹 Obtém os campos personalizados "fluxo" e "target".
            $custom_fields = \local_catalogo\util\custom_field::get_custom_fields($course->id, ['fluxo', 'target', 'ods']);

            // 🔹 Processando "fluxo"
            $fluxo_value = isset($custom_fields['fluxo']) ? trim($custom_fields['fluxo']) : null;
            $fluxo_map = ['1' => 'sim', '2' => 'não'];
            $hasfluxo = isset($fluxo_map[$fluxo_value]) && $fluxo_map[$fluxo_value] === 'sim';

            // 🔹 Processando "target"
            $target_tags = [];
            if (!empty($custom_fields['target'])) {
                $target_tags = array_map('trim', explode(',', $custom_fields['target']));
            }
            $hastarget = !empty($target_tags);

            // 🔹 Formata o curso com os detalhes necessários.
            $formatted_courses[] = [
                'id' => $course->id,
                'fullname' => $course->fullname,
                'summary' => strip_tags($course->summary),
                'category' => $category_details,
                'enrolment' => $enrol_details,
                'custom_fields' => [
                    'hasfluxo' => $hasfluxo,
                    'hastarget' => $hastarget,
                    'target' => $target_tags,
                    'ods' => $custom_fields['ods'] ?? null,
                ],
                'baseurl' => (new \moodle_url('/local/catalogo'))->out(false),
                'url' => (new \moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
                'intropage_url' => (new \moodle_url('/local/catalogo/intro.php', ['courseid' => $course->id]))->out(false),
            ];
        }

        return $formatted_courses;
    }
}
