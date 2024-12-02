<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Renderizador personalizado para o Catálogo de Cursos.
 *
 * Este arquivo define a classe responsável por formatar os dados do catálogo
 * para que eles possam ser exibidos no template Mustache.
 *
 * @package   local_catalogo
 * @author    Breno Augusto
 * @email     brenoaugusto@uems.br
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die();
 
 /**
  * Renderizador personalizado para o Catálogo.
  */
 class local_catalogo_renderer {
     private $courses;
 
     /**
      * Construtor.
      *
      * @param array $courses Lista de cursos a serem exibidos.
      */
     public function __construct($courses) {
         $this->courses = $courses;
     }
 
     /**
      * Retorna os dados formatados para o template.
      *
      * @return array Dados organizados para o template.
      */
     public function get_data() {
         $formattedcourses = [];
         foreach ($this->courses as $course) {
             $formattedcourses[] = [
                 'id' => $course->id,
                 'fullname' => $course->fullname,
                 'url' => (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
             ];
         }
         return ['courses' => $formattedcourses];
     }
 }
 