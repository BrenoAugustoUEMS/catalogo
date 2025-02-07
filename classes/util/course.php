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
    
        // 🔹 Inicializa o array de IDs de categorias.
        $categoryids = [];
    
        // 🔹 Se um filtro de categoria for aplicado, inclua também as categorias filhas.
        if ($categoryfilter) {
            $categories = category::get_hierarchical_categories($categoryfilter);
            $categoryids[] = $categoryfilter;
    
            if (isset($categories[$categoryfilter]['children'])) {
                foreach ($categories[$categoryfilter]['children'] as $child) {
                    $categoryids[] = $child['id'];
                }
            }
        }       
    
        // 🔹 Monta a consulta SQL para buscar os cursos.
        $sql = "SELECT id, fullname, summary, category
                FROM {course}
                WHERE visible = 1 AND id != 1"; // Exclui o "front page course" (id = 1).
        $params = [];
    
        // 🔹 Aplica o filtro de categorias, se existir.
        if (!empty($categoryids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($categoryids, SQL_PARAMS_NAMED);
            $sql .= " AND category $insql";
            $params = array_merge($params, $inparams);
        }
    
        $sql .= " ORDER BY id DESC"; // Para pegar os cursos mais recentes primeiro.
    
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
            $custom_fields = \local_catalogo\util\custom_field::get_custom_fields($course->id, ['fluxo', 'target']);
    
            // 🔹 Processando "fluxo"
            $fluxo_value = isset($custom_fields['fluxo']) ? trim($custom_fields['fluxo']) : null;

            // 🔹 Converter valores numéricos para "Sim" ou "Não"
            $fluxo_map = [
                '1' => 'sim',
                '2' => 'não'
            ];

            $hasfluxo = isset($fluxo_map[$fluxo_value]) && $fluxo_map[$fluxo_value] === 'sim';
    
            // 🔹 Processando "target" corretamente
            $target_tags = [];
            if (!empty($custom_fields['target'])) {
                $target_tags = array_map('trim', explode(',', $custom_fields['target']));
            }
    
            // 🔹 Garante que `hastarget` só será `true` se houver valores em `target`
            $hastarget = !empty($target_tags);
    
            // 🔹 Formata o curso com os detalhes necessários.
            $formatted_courses[] = [
                'id' => $course->id,
                'fullname' => $course->fullname,
                'summary' => strip_tags($course->summary),
                'category' => $category_details,
                'enrolment' => $enrol_details,
                'custom_fields' => [
                    'hasfluxo' => $hasfluxo,  // ✅ Agora é sempre `true` ou `false`
                    'hastarget' => $hastarget, // ✅ Somente `true` se realmente houver tags
                    'target' => $target_tags   // ✅ Lista de tags separadas corretamente
                ],
                'url' => (new \moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
                'intropage_url' => (new \moodle_url('/local/intropage/index.php', ['courseid' => $course->id]))->out(false),
            ];
        }
    
        return $formatted_courses;
    }
    
}
