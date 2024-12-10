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
            // Obter os detalhes da categoria usando a classe `category`.
            $category_details = \local_catalogo\util\category::get_category_details($course->category);

            // Obter os detalhes de enrol usando a classe `enrolment`.
            $enrol_details = \local_catalogo\util\enrolment::get_enrolment_details($course->id);
    
            // Formatar o curso com os detalhes da categoria.
            $formatted_courses[] = [
                'id' => $course->id,
                'fullname' => $course->fullname,
                'summary' => $course->summary,
                'category' => $category_details,
                'enrolment' => $enrol_details,
                'url' => (new \moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
                'intropage_url' => (new \moodle_url('/local/intropage/index.php', ['courseid' => $course->id]))->out(false),
            ];
        }

        return $formatted_courses;
    }
}
