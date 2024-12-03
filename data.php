<?php
// Dados de mock para testar o renderer.
return [
    (object)[
        'id' => 1,
        'fullname' => 'Edital de Cultura 2024',
        'imageurl' => 'https://placecats.com/millie_neo/300/200',
        'organization' => 'Fundação Nacional de Artes',
        'tags' => ['Cultura', 'Artes'],
        'deadline' => '03/12/2024',
        'extended' => false,
    ],
    (object)[
        'id' => 2,
        'fullname' => 'Edital de Esportes 2024',
        'imageurl' => 'https://placecats.com/millie/300/150',
        'organization' => 'Ministério do Esporte',
        'tags' => ['Esportes', 'Educação'],
        'deadline' => '05/12/2024',
        'extended' => true,
    ],
    (object)[
        'id' => 3,
        'fullname' => 'Edital de Tecnologia 2024',
        'imageurl' => 'https://placecats.com/bella/300/200',
        'organization' => 'Agência Nacional de Tecnologia',
        'tags' => ['Tecnologia', 'Inovação'],
        'deadline' => '15/12/2024',
        'extended' => false,
    ],
];
