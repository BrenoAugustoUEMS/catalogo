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

        // Busca os cursos principais.
        $sql = "SELECT id, fullname, summary, category
                FROM {course}
                WHERE visible = 1 AND id != 1";
        $params = [];

        if ($categoryfilter) {
            $sql .= " AND category = :categoryfilter";
            $params['categoryfilter'] = $categoryfilter;
        }

        $courses = $DB->get_records_sql($sql, $params);
        $formatted_courses = [];

        foreach ($courses as $course) {
            // Adiciona detalhes ao curso.
            $formatted_courses[] = [
                'id' => $course->id,
                'fullname' => $course->fullname,
                'summary' => $course->summary,
                'category_id' => $course->category,
                'url' => (new \moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
            ];
        }

        return $formatted_courses;
    }
}
