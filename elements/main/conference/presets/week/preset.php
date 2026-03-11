<?php


$conferences = [
    [
        'id' => 1,
        'date_main' => '17 Paź',
        'date_rest' => '18 Paź · 19 Paź',
        'title' => 'Cyfrowa transformacja organizacji – od strategii do wdrożenia',
        'desc' => 'Konferencja poświęcona nowym trendom, innowacjom i wyzwaniom, które kształtują świat jutra.'
    ],
    [
        'id' => 2,
        'date_main' => '18 Paź',
        'date_rest' => '19 Paź',
        'title' => 'Nowe modele pracy i kompetencje przyszłości',
        'desc' => 'Jak zmienia się rynek pracy i jakie kompetencje będą kluczowe w nadchodzących latach.'
    ],
    [
        'id' => 3,
        'date_main' => '19 Paź',
        'date_rest' => '',
        'title' => 'Bezpieczeństwo danych i cyberzagrożenia',
        'desc' => 'Ochrona danych i najlepsze praktyki bezpieczeństwa w świecie cyfrowym.'
    ]
];

$output = '
<div id="pweConference" class="pwe-conference">

</div>';
$output .= '<div class="pwe-conference">';

/* ================= LEFT ================= */
$output .= '<div class="pwe-conference__left">';

foreach ($conferences as $index => $conf) {
    $active = $index === 0 ? ' is-active' : '';

    $output .= '
    <div class="pwe-conference-card'.$active.'" data-id="'.$conf['id'].'">
        <div class="pwe-conference-card__image">
            <span class="badge">Nowość</span>
        </div>

        <div class="pwe-conference-card__content">
            <span class="meta">Conference room · Hall D</span>

            <div class="dates">
                <strong>'.$conf['date_main'].'</strong>
                <span>'.$conf['date_rest'].'</span>
            </div>

            <div class="organizers">
                <div class="icon"></div>
                <div class="icon"></div>
                <div class="icon"></div>
            </div>

            <h3>'.$conf['title'].'</h3>
            <p>'.$conf['desc'].'</p>

            <a href="#" class="button">Dowiedz się teraz →</a>
        </div>
    </div>';
}

$output .= '</div>';

/* ================= RIGHT ================= */
$output .= '<div class="pwe-conference__right">';
$output .= '<ol class="pwe-conference-list">';

foreach ($conferences as $index => $conf) {
    $active = $index === 0 ? ' is-active' : '';

    $output .= '
    <li class="'.$active.'" data-id="'.$conf['id'].'">
        <span class="number">'.$conf['id'].'</span>
        <span class="text">'.$conf['title'].'</span>
    </li>';
}

$output .= '</ol></div></div>';

$output .= '<script>
document.querySelectorAll(\'.pwe-conference-list li\').forEach(item => {
    item.addEventListener(\'click\', () => {
        const id = item.dataset.id;

        document.querySelectorAll(\'.pwe-conference-list li\').forEach(li =>
            li.classList.remove(\'is-active\')
        );

        document.querySelectorAll(\'.pwe-conference-card\').forEach(card =>
            card.classList.remove(\'is-active\')
        );

        item.classList.add(\'is-active\');
        document.querySelector(\'.pwe-conference-card[data-id="\' + id + \'"]\')
            .classList.add(\'is-active\');
    });

});
</script>';


return $output;
