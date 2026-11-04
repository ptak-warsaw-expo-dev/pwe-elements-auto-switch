<?php
if (!defined('ABSPATH')) exit;

$output = '';
$image = is_array($news['image'] ?? null) ? $news['image'] : [];
$hero_url = $news['hero_url'] ?? ($image['url'] ?? '');
$hero_alt = $news['hero_alt'] ?? ($image['alt'] ?? ($news['title'] ?? ''));
$ceremony = is_array($news['ceremony'] ?? null) ? $news['ceremony'] : [];
$people = array_filter(is_array($ceremony['people'] ?? null) ? $ceremony['people'] : [], static function ($person) {
    return is_array($person) && !empty($person['name']);
});
$categories = is_array($news['categories'] ?? null) ? $news['categories'] : [];
$distinctions = is_array($news['distinctions'] ?? null) ? $news['distinctions'] : [];
$gallery = array_filter(is_array($news['gallery'] ?? null) ? $news['gallery'] : [], static function ($photo) {
    return is_array($photo) && !empty($photo['url']) && esc_url($photo['url']) !== '';
});
$sections = is_array($news['sections'] ?? null) ? $news['sections'] : [];

// Support older API data containing individual awards instead of categories.
if (empty($categories) && is_array($news['awards'] ?? null)) {
    foreach ($news['awards'] as $award) {
        if (!is_array($award) || empty($award['winner'])) continue;
        $category = !empty($award['category']) ? $award['category'] : PWE_Functions::multi_translation("laureates");
        $categories[$category]['title'] = $category;
        $categories[$category]['awards'][] = $award;
    }
}

// Remove empty records before deciding whether to display a section.
foreach ($categories as $index => $category) {
    if (!is_array($category)) {
        unset($categories[$index]);
        continue;
    }
    $categories[$index]['winners'] = array_filter(is_array($category['winners'] ?? null) ? $category['winners'] : [], static function ($winner) {
        return is_string($winner) && trim($winner) !== '';
    });
    $categories[$index]['awards'] = array_filter(is_array($category['awards'] ?? null) ? $category['awards'] : [], static function ($award) {
        return is_array($award) && !empty($award['winner']);
    });
    if (empty($categories[$index]['winners']) && empty($categories[$index]['awards'])) unset($categories[$index]);
}

foreach ($distinctions as $index => $distinction) {
    if (!is_array($distinction)) {
        unset($distinctions[$index]);
        continue;
    }
    $distinctions[$index]['winners'] = array_filter(is_array($distinction['winners'] ?? null) ? $distinction['winners'] : [], static function ($winner) {
        return is_string($winner) && trim($winner) !== '';
    });
    if (empty($distinction['title']) && empty($distinction['description']) && empty($distinctions[$index]['winners'])) unset($distinctions[$index]);
}

$output .= '
<article id="pwePostMedalCeremony" class="pwe-post-medal-ceremony">
    ' . (!empty($hero_url) || !empty($news['eyebrow']) || !empty($news['title']) || !empty($news['lead']) ? '
        <header class="pwe-post-medal-ceremony__hero">
            <div class="pwe-post-medal-ceremony__wrap">
                ' . (!empty($hero_url) ? '
                    <img class="pwe-post-medal-ceremony__banner" src="' . esc_url($hero_url) . '" alt="' . esc_attr($hero_alt) . '" fetchpriority="high" decoding="async">
                ' : '') . '
                ' . (!empty($news['eyebrow']) ? '
                    <p class="pwe-post-medal-ceremony__eyebrow">' . esc_html($news['eyebrow']) . '</p>
                ' : '') . '
                ' . (!empty($news['title']) ? '
                    <h1>' . esc_html($news['title']) . '</h1>
                ' : '') . '
                ' . (!empty($news['lead']) ? '
                    <div class="pwe-post-medal-ceremony__copy">' . wp_kses_post($news['lead']) . '</div>
                ' : '') . '
            </div>
        </header>
    ' : '') . '
    <div class="pwe-post-medal-ceremony__wrap pwe-post-medal-ceremony__body">
        ' . (!empty($ceremony['title']) || !empty($ceremony['description']) || !empty($people) ? '
            <section class="pwe-post-medal-ceremony__ceremony">
                ' . (!empty($ceremony['title']) || !empty($ceremony['description']) ? '
                    <div class="pwe-post-medal-ceremony__ceremony-content">
                        ' . (!empty($ceremony['title']) ? '<h2>' . esc_html($ceremony['title']) . '</h2>' : '') . '
                        ' . (!empty($ceremony['description']) ? '<div class="pwe-post-medal-ceremony__copy">' . wp_kses_post($ceremony['description']) . '</div>' : '') . '
                    </div>
                ' : '') . '
        ' : '');

if (!empty($people)) {
    $output .= '<ul class="pwe-post-medal-ceremony__people">';
    foreach ($people as $person) {
        $output .= '
            <li>
                <strong>' . esc_html($person['name']) . '</strong>
                ' . (!empty($person['role']) ? '<span>' . esc_html($person['role']) . '</span>' : '') . '
            </li>';
    }
    $output .= '</ul>';
}

$output .= (!empty($ceremony['title']) || !empty($ceremony['description']) || !empty($people) ? '</section>' : '');

if (!empty($categories)) {
    $output .= '
        <section class="pwe-post-medal-ceremony__laureates">
            <h2>' . esc_html(PWE_Functions::multi_translation("medal_laureates_title")) . '</h2>
            <div class="pwe-post-medal-ceremony__categories">';

    foreach ($categories as $category) {
        $output .= '
                <article class="pwe-post-medal-ceremony__category">
                    ' . (!empty($category['title']) ? '
                        <div class="pwe-post-medal-ceremony__category-heading"><h3>' . esc_html($category['title']) . '</h3></div>
                    ' : '') . '
                    <div class="pwe-post-medal-ceremony__category-winners">';

        if (!empty($category['winners'])) {
            $output .= '<ul class="pwe-post-medal-ceremony__names">';
            foreach ($category['winners'] as $winner) {
                $output .= '<li>' . esc_html($winner) . '</li>';
            }
            $output .= '</ul>';
        }
        foreach ($category['awards'] as $award) {
            $output .= '
                        <div class="pwe-post-medal-ceremony__legacy-award">
                            <strong>' . esc_html($award['winner']) . '</strong>
                            ' . (!empty($award['description']) ? '<div class="pwe-post-medal-ceremony__copy">' . wp_kses_post($award['description']) . '</div>' : '') . '
                        </div>';
        }
        $output .= '
                    </div>
                </article>';
    }
    $output .= '
            </div>
        </section>';
}

if (!empty($distinctions)) {
    $output .= '
        <section class="pwe-post-medal-ceremony__recognition">
            <div class="pwe-post-medal-ceremony__recognition-heading"><h2>' . esc_html(PWE_Functions::multi_translation("special_distinctions_title")) . '</h2></div>
            <div class="pwe-post-medal-ceremony__distinctions">';

    foreach ($distinctions as $distinction) {
        $output .= '
                <article class="pwe-post-medal-ceremony__distinction">
                    ' . (!empty($distinction['title']) ? '<h3>' . esc_html($distinction['title']) . '</h3>' : '');
        if (!empty($distinction['winners'])) {
            $output .= '<ul class="pwe-post-medal-ceremony__names">';
            foreach ($distinction['winners'] as $winner) {
                $output .= '<li>' . esc_html($winner) . '</li>';
            }
            $output .= '</ul>';
        }
        $output .= '
                    ' . (!empty($distinction['description']) ? '
                        <div class="pwe-post-medal-ceremony__distinction-copy"><div class="pwe-post-medal-ceremony__copy">' . wp_kses_post($distinction['description']) . '</div></div>
                    ' : '') . '
                </article>';
    }
    $output .= '
            </div>
        </section>';
}

if (!empty($gallery)) {
    $output .= '
        <section class="pwe-post-medal-ceremony__gallery-section">
            <h2>' . esc_html(PWE_Functions::multi_translation("gallery_title")) . '</h2>
            <div class="pwe-post-medal-ceremony__gallery">';
    foreach ($gallery as $photo) {
        $output .= '
                <a href="' . esc_url($photo['url']) . '" aria-label="' . esc_attr(PWE_Functions::multi_translation("open_photo") . (!empty($photo['alt']) ? ': ' . $photo['alt'] : '')) . '">
                    <img src="' . esc_url($photo['url']) . '" alt="' . esc_attr($photo['alt'] ?? '') . '" width="800" height="534" loading="lazy" decoding="async">
                    <span aria-hidden="true">↗</span>
                </a>';
    }
    $output .= '
            </div>
        </section>';
}

foreach ($sections as $section) {
    if (!is_array($section)) continue;
    $paragraphs = array_filter(is_array($section['paragraphs'] ?? null) ? $section['paragraphs'] : [], static function ($paragraph) {
        return is_string($paragraph) && trim($paragraph) !== '';
    });
    if (empty($section['title']) && empty($section['description']) && empty($paragraphs)) continue;

    $output .= '
        <section class="pwe-post-medal-ceremony__closing">
            ' . (!empty($section['title']) ? '<h2>' . esc_html($section['title']) . '</h2>' : '') . '
            ' . (!empty($section['description']) ? '<div class="pwe-post-medal-ceremony__copy">' . wp_kses_post($section['description']) . '</div>' : '');
    foreach ($paragraphs as $paragraph) {
        $output .= '<div class="pwe-post-medal-ceremony__copy">' . wp_kses_post($paragraph) . '</div>';
    }
    $output .= '</section>';
}

$output .= '
        ' . (get_the_ID() ? '
            <footer class="pwe-post-medal-ceremony__publication">
                ' . esc_html(PWE_Functions::multi_translation("publication_date")) . ': <time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date('j F Y')) . '</time>
            </footer>
        ' : '') . '
    </div>
</article>';

$output .= include __DIR__ . '/../../assets/photo-viewer.php';

return $output;
