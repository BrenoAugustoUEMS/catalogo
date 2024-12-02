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
 * Version information for local_catalogo
 *
 * @package    local_catalogo
 * @copyright  2024 Breno Augusto <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die();
 
 $plugin->component = 'local_catalogo'; // Nome do plugin.
 $plugin->version = 2024120200;         // Data da versão no formato YYYYMMDDXX.
 $plugin->release = 'v0.1';             // Versão amigável para humanos.
 $plugin->requires = 2021051700;        // Versão mínima do Moodle (3.11 ou superior).
 $plugin->maturity = MATURITY_ALPHA;    // Maturidade do plugin: ALPHA, BETA ou STABLE.
 