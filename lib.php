<?php
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib/categories.php');
require_once(__DIR__ . '/lib/courses.php');
require_once(__DIR__ . '/lib/custom_fields.php');
require_once(__DIR__ . '/lib/enrolment.php');

/**
 * Função para coletar todos os dados processados e formatados para o template.
 *
 * @return array Dados organizados para o template.
 */
function local_catalogo_get_data_for_template() {
    // 1. Chama as funções para obter os dados processados
    $categories = local_catalogo_get_categories();
    $courses = local_catalogo_get_courses();
    $custom_fields = local_catalogo_get_custom_fields();
    $enrolment = local_catalogo_get_enrolment_data();

    // 2. Organiza tudo em um único pacote de dados
    $data = [
        'categories' => $categories,
        'courses' => $courses,
        'custom_fields' => $custom_fields,
        'enrolment' => $enrolment,
    ];

    // Retorna o pacote de dados
    return $data;
}