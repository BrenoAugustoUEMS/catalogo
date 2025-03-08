# Visão Geral

O **local_catalogo** foi criado para unificar as funcionalidades de exibir e introduzir cursos/“editais” dentro do Moodle. Ele oferece:

- **Listagem de Cursos** com busca por múltiplas palavras e filtro por categoria.
- **Página de Introdução** de cada curso, detalhando inscrições, _custom fields_, entre outros.

O plugin mantém a interface simples e focada no usuário, além de **facilitar manutenção e extensões futuras**.


# Classes

## classes/util/category.php

Este arquivo concentra a lógica de gerenciamento de **categorias** no plugin `local_catalogo`. Ele fornece três métodos principais:

1. **`get_hierarchical_categories(?int $categoryfilter = null): array`**  
   - Retorna todas as categorias **visíveis** no Moodle, organizadas em formato **hierárquico** (pais e filhos).  
   - Usa a coluna `depth` para identificar níveis, monta um **caminho amigável** (ex.: `CategoriaPai > CategoriaFilha`) e marca se a categoria está selecionada (`is_selected`).  
   - É útil para exibir as categorias de forma aninhada em menus ou seletores.

2. **`get_categories_for_menu(?int $categoryfilter = null): array`**  
   - Converte a hierarquia construída por `get_hierarchical_categories()` em uma **lista plana** (sem subníveis).  
   - Adiciona propriedades como `is_parent` e `is_selected` para auxiliar na renderização, por exemplo, em `<select>` de filtro.  
   - Assim, pais e filhos ficam no mesmo array, facilitando o loop em templates Mustache ou formulários.

3. **`get_single_category_details(int $categoryid): array`**  
   - Carrega detalhes de uma categoria específica, incluindo **nome**, **caminho completo** (`path`), **pai** (`parent_id`) e, opcionalmente, uma **imagem** associada.  
   - Usa um mapa de imagens (`$category_images`) para associar certos IDs de categoria a arquivos de imagem localizados em `pix/category-img/`.  
   - Caso não haja correspondência, aplica uma imagem padrão (`default.png`).  
   - É útil quando se deseja exibir informações mais completas ou personalizadas de apenas uma categoria.



## classes/util/course.php

Este arquivo se concentra na **busca e formatação** dos cursos dentro do plugin `local_catalogo`. Ele oferece o método principal:



### `get_courses_with_details(?int $categoryfilter = null, ?string $search = '', ?int $courseid = null): array`

- **Objetivo**: retornar uma lista de cursos “visíveis” com detalhes adicionais (categoria, inscrição, custom fields).
- **Parâmetros**:
  - `categoryfilter (int|null)`: se informado, filtra os cursos por esta categoria e suas subcategorias.
  - `search (string|null)`: se informado, faz uma busca por múltiplas palavras no campo `fullname`.
  - `courseid (int|null)`: se informado, retorna apenas os dados de **um curso específico** (ignora os demais filtros).
- **Como funciona**:
  1. Define as **condições básicas** para filtrar cursos visíveis (`visible = 1`) e excluir o curso de ID 1 (página inicial).
  2. Se `courseid` for passado, busca apenas esse curso.
  3. Caso contrário, aplica o **filtro de categoria** (incluindo subcategorias) e, se houver, um **termo de busca** que permite múltiplas palavras:
     ```sql
     LOWER(fullname) LIKE LOWER(:search0) AND ...
     ```
     garantindo case-insensitive e proteção contra caracteres especiais (`%`, `_`).
  4. Monta a consulta SQL e executa para obter os registros de curso do banco de dados.
  5. Para cada curso, obtém:
     - **Detalhes da categoria** (`get_single_category_details()`).
     - **Dados de inscrição** (`\local_catalogo\util\enrolment::get_enrolment_details()`).
     - **Campos personalizados** (`fluxo`, `target`, `ods`, `edital_url`) por meio de `custom_field::get_custom_fields()`.
     - Formata esses dados, gerando um array que inclui:
       - `fullname`, `summary` (limpo de tags HTML),
       - caminhos e URLs de interesse (`intropage_url`, entre outros),
       - flags (`hasfluxo`, `hastarget`) e listas (como `target_tags`).
- **Retorno**:
  - Um **array** de cursos com todas as informações necessárias para exibir no catálogo ou na introdução do curso.

Em resumo, `get_courses_with_details()` concentra a lógica de montagem dos dados do curso, permitindo que a camada de apresentação (templates Mustache, por exemplo) possa exibir todas as informações de forma simples. Ele evita duplicações de código, pois reúne, em um só local, filtros, buscas e outras informações contextuais de cada curso.


## classes/util/custom_field.php

Esta classe lida com **campos personalizados** (custom fields) associados aos cursos no Moodle, centralizando toda a lógica de busca e tratamento desses campos dentro do plugin `local_catalogo`. Ela fornece os seguintes métodos:


### `get_custom_fields(int $courseid, array $shortnames): array`
- **Objetivo**: retornar os valores de campos personalizados específicos de um curso.
- **Parâmetros**:
  - `courseid`: ID do curso no Moodle.
  - `shortnames`: lista de *shortnames* dos campos que se deseja recuperar (por exemplo, `['fluxo', 'target', 'ods']`).
- **Funcionamento**:
  1. Monta a query utilizando `WHERE f.shortname IN (...)` para obter apenas os campos de interesse.
  2. Retorna um array associativo onde a chave é o *shortname* e o valor é o conteúdo do campo.
  3. Se o campo for “ods”, o valor é processado por `process_ods_field()` (ver abaixo).
- **Retorno**: 
  - Um array com as chaves correspondentes aos *shortnames* solicitados e seus valores (já limpos/tratados).

---

### `process_ods_field(?string $ods_value): array`
- **Objetivo**: processar especificamente o campo “ods”, retornando apenas números válidos (1 a 17).
- **Parâmetros**:
  - `ods_value`: valor bruto do campo “ods”, geralmente uma string com números separados por vírgula.
- **Funcionamento**:
  1. Divide a string por “,”.
  2. Remove espaços em branco e converte cada parte para inteiro.
  3. Filtra apenas valores de 1 a 17.
- **Retorno**:
  - Um array de inteiros, ex.: `[1, 2, 5]` se a string contiver “1, 2, 5, 30”.

---

### `get_edital_url(int $courseid): ?string`
- **Objetivo**: retornar o valor do campo personalizado “edital_url” de um curso.
- **Parâmetros**:
  - `courseid`: ID do curso no Moodle.
- **Funcionamento**:
  1. Chama `get_custom_fields()` para obter o valor de `edital_url`.
  2. Se não existir, retorna `null`.
- **Retorno**:
  - A URL do edital ou `null` caso o campo não esteja configurado.

Em resumo, `custom_field.php` abstrai a lógica de recuperação e processamento de campos personalizados, fornecendo funções específicas para campos como “ods” e “edital_url”. Isso evita duplicações e mantém o código mais organizado no plugin.
