<?php
if (!defined('ABSPATH')) exit;

$output = '';
$hero_url = $news['hero_url'] ?? ($news['image']['url'] ?? '');
$description = $news['description'] ?? ($news['lead'] ?? '');
$exhibitions = is_array($news['exhibitions'] ?? null) ? $news['exhibitions'] : [];
$partners = is_array($news['partners'] ?? null) ? $news['partners'] : [];
$program = is_array($news['program'] ?? null) ? $news['program'] : [];
$program_items = is_array($program['items'] ?? null) ? $program['items'] : [];
$highlights = is_array($news['highlights'] ?? null) ? $news['highlights'] : [];
$sections = is_array($news['sections'] ?? null) ? $news['sections'] : [];
$closing = is_array($news['closing'] ?? null) ? $news['closing'] : [];
$registrations = array_filter(is_array($news['registration_links'] ?? null) ? $news['registration_links'] : [], static function ($link) {
    return is_array($link) && !empty($link['label']) && !empty($link['url']) && esc_url($link['url']) !== '';
});
if (empty($registrations) && !empty($news['event']['url']) && !empty($news['event']['link_label'])) {
    $registrations[] = ['url' => $news['event']['url'], 'label' => $news['event']['link_label']];
}

// Prepare lists only; section markup is built directly in $output below.
foreach ($exhibitions as $index => $exhibition) {
    if (!is_array($exhibition)) { unset($exhibitions[$index]); continue; }
    $exhibitions[$index]['topics'] = array_filter(is_array($exhibition['topics'] ?? null) ? $exhibition['topics'] : [], static function ($topic) {
        return is_string($topic) && trim($topic) !== '';
    });
    $exhibitions[$index]['exhibitors'] = array_filter(is_array($exhibition['exhibitors'] ?? null) ? $exhibition['exhibitors'] : [], static function ($company) {
        return is_array($company) && !empty($company['name']);
    });
    if (empty($exhibition['title']) && empty($exhibition['description']) && empty($exhibitions[$index]['topics']) && empty($exhibitions[$index]['exhibitors'])) unset($exhibitions[$index]);
}
foreach ($partners as $index => $partner) {
    if (!is_array($partner)) { unset($partners[$index]); continue; }
    $partners[$index]['names'] = array_filter(is_array($partner['names'] ?? null) ? $partner['names'] : [], static function ($name) {
        return is_string($name) && trim($name) !== '';
    });
    if (empty($partners[$index]['names'])) unset($partners[$index]);
}
foreach ($program_items as $index => $item) {
    if (!is_array($item)) { unset($program_items[$index]); continue; }
    $program_items[$index]['speakers'] = array_filter(is_array($item['speakers'] ?? null) ? $item['speakers'] : [], static function ($speaker) {
        return is_string($speaker) && trim($speaker) !== '';
    });
    if (empty($item['title']) && empty($item['description']) && empty($item['day']) && empty($program_items[$index]['speakers'])) unset($program_items[$index]);
}
$highlights = array_filter($highlights, static function ($item) {
    return is_array($item) && (!empty($item['title']) || !empty($item['description']));
});

$output .= '
<article id="pwePostPreview" class="pwe-post-preview">
    ' . (!empty($hero_url) || !empty($news['title']) || !empty($description) ? '
        <header class="pwe-post-preview__hero">
            <div class="pwe-post-preview__wrap">
                ' . (!empty($hero_url) ? '
                    <img class="pwe-post-preview__hero-image" src="' . esc_url($hero_url) . '" alt="' . esc_attr($news['hero_alt'] ?? ($news['title'] ?? '')) . '" fetchpriority="high" decoding="async">
                ' : '') . '
                ' . (!empty($news['title']) ? '<h1>' . esc_html($news['title']) . '</h1>' : '') . '
                ' . (!empty($description) ? '<div class="pwe-post-preview__description">' . wp_kses_post($description) . '</div>' : '') . '
            </div>
        </header>
    ' : '') . '
    <div class="pwe-post-preview__wrap pwe-post-preview__body">';

if (!empty($exhibitions)) {
    $output .= '
        <section>
            ' . (!empty($news['exhibitions_title'] ?? PWE_Functions::multi_translation("exhibitions_title")) ? '<h2>' . esc_html($news['exhibitions_title'] ?? PWE_Functions::multi_translation("exhibitions_title")) . '</h2>' : '') . '
            <div class="pwe-post-preview__exhibitions">';
    foreach ($exhibitions as $exhibition) {
        $output .= '
                <article class="pwe-post-preview__exhibition">
                    ' . (!empty($exhibition['title']) ? '<h3>' . esc_html($exhibition['title']) . '</h3>' : '') . '
                    ' . (!empty($exhibition['description']) ? '<div class="pwe-post-preview__description">' . wp_kses_post($exhibition['description']) . '</div>' : '');
        if (!empty($exhibition['topics'])) {
            $output .= '<ul class="pwe-post-preview__topics" aria-label="' . esc_attr(PWE_Functions::multi_translation("exhibition_scope")) . '">';
            foreach ($exhibition['topics'] as $topic) $output .= '<li>' . esc_html($topic) . '</li>';
            $output .= '</ul>';
        }
        if (!empty($exhibition['exhibitors'])) {
            $output .= '<div class="pwe-post-preview__exhibitors"><h4>' . esc_html(PWE_Functions::multi_translation("among_exhibitors")) . '</h4><ul>';
            foreach ($exhibition['exhibitors'] as $company) {
                $output .= '
                    <li>
                        <strong>' . esc_html($company['name']) . '</strong>
                        ' . (!empty($company['description']) ? '<span>' . esc_html($company['description']) . '</span>' : '') . '
                    </li>';
            }
            $output .= '</ul></div>';
        }
        $output .= '</article>';
    }
    $output .= '</div></section>';
}

if (!empty($partners)) {
    $output .= '
        <section class="pwe-post-preview__partners">
            ' . (!empty($news['partners_title'] ?? PWE_Functions::multi_translation("partners_title")) ? '<h2>' . esc_html($news['partners_title'] ?? PWE_Functions::multi_translation("partners_title")) . '</h2>' : '') . '
            <div class="pwe-post-preview__partner-groups">';
    foreach ($partners as $partner) {
        $output .= '
                <div class="pwe-post-preview__partner-group">
                    ' . (!empty($partner['title']) ? '<h3>' . esc_html($partner['title']) . '</h3>' : '') . '
                    <ul>';
        foreach ($partner['names'] as $name) $output .= '<li>' . esc_html($name) . '</li>';
        $output .= '</ul></div>';
    }
    $output .= '</div></section>';
}

if (!empty($program['title']) || !empty($program['description']) || !empty($program_items)) {
    $output .= '
        <section class="pwe-post-preview__program">
            ' . (!empty($program['title']) || !empty($program['description']) ? '
                <div class="pwe-post-preview__program-intro">
                    ' . (!empty($program['title']) ? '<h2>' . esc_html($program['title']) . '</h2>' : '') . '
                    ' . (!empty($program['description']) ? '<div class="pwe-post-preview__description">' . wp_kses_post($program['description']) . '</div>' : '') . '
                </div>
            ' : '');
    if (!empty($program_items)) {
        $output .= '<div class="pwe-post-preview__program-grid">';
        foreach ($program_items as $item) {
            $output .= '
                <article class="pwe-post-preview__program-item">
                    ' . (!empty($item['day']) ? '<p class="pwe-post-preview__day">' . esc_html($item['day']) . '</p>' : '') . '
                    ' . (!empty($item['title']) ? '<h3>' . esc_html($item['title']) . '</h3>' : '') . '
                    ' . (!empty($item['description']) ? '<div class="pwe-post-preview__description">' . wp_kses_post($item['description']) . '</div>' : '');
            if (!empty($item['speakers'])) {
                $output .= '<ul class="pwe-post-preview__speakers" aria-label="' . esc_attr(PWE_Functions::multi_translation("program_participants")) . '">';
                foreach ($item['speakers'] as $speaker) $output .= '<li>' . esc_html($speaker) . '</li>';
                $output .= '</ul>';
            }
            $output .= '</article>';
        }
        $output .= '</div>';
    }
    $output .= '</section>';
}

if (!empty($highlights)) {
    $output .= '
        <section>
            ' . (!empty($news['highlights_title'] ?? PWE_Functions::multi_translation("highlights_title")) ? '<h2>' . esc_html($news['highlights_title'] ?? PWE_Functions::multi_translation("highlights_title")) . '</h2>' : '') . '
            <div class="pwe-post-preview__highlights">';
    foreach ($highlights as $item) {
        $output .= '
                <article class="pwe-post-preview__highlight">
                    ' . (!empty($item['title']) ? '<h3>' . esc_html($item['title']) . '</h3>' : '') . '
                    ' . (!empty($item['description']) ? '<div class="pwe-post-preview__description">' . wp_kses_post($item['description']) . '</div>' : '') . '
                </article>';
    }
    $output .= '</div></section>';
}

foreach ($sections as $section) {
    if (!is_array($section)) continue;
    $paragraphs = array_filter(is_array($section['paragraphs'] ?? null) ? $section['paragraphs'] : [], static function ($paragraph) {
        return is_string($paragraph) && trim($paragraph) !== '';
    });
    if (empty($section['title']) && empty($section['description']) && empty($paragraphs)) continue;
    $output .= '
        <section class="pwe-post-preview__additional">
            ' . (!empty($section['title']) ? '<h2>' . esc_html($section['title']) . '</h2>' : '') . '
            ' . (!empty($section['description']) ? '<div class="pwe-post-preview__description">' . wp_kses_post($section['description']) . '</div>' : '');
    foreach ($paragraphs as $paragraph) $output .= '<div class="pwe-post-preview__description">' . wp_kses_post($paragraph) . '</div>';
    $output .= '</section>';
}

if (!empty($closing['title']) || !empty($closing['description']) || !empty($registrations)) {
    $output .= '
        <section class="pwe-post-preview__closing">
            ' . (!empty($closing['title']) || !empty($closing['description']) ? '
                <div class="pwe-post-preview__closing-content">
                    ' . (!empty($closing['title']) ? '<h2>' . esc_html($closing['title']) . '</h2>' : '') . '
                    ' . (!empty($closing['description']) ? '<div class="pwe-post-preview__description">' . wp_kses_post($closing['description']) . '</div>' : '') . '
                </div>
            ' : '');
    if (!empty($registrations)) {
        $output .= '<div class="pwe-post-preview__actions">';
        foreach ($registrations as $link) {
            $output .= '
                <a class="pwe-post-preview__button pwe-post-preview__button--white" href="' . esc_url($link['url']) . '">
                    <span>' . esc_html($link['label']) . '</span><span aria-hidden="true">↗</span>
                </a>';
        }
        $output .= '</div>';
    }
    $output .= '</section>';
}

$output .= '
        ' . (get_the_ID() ? '
            <footer class="pwe-post-preview__publication">
                ' . esc_html(PWE_Functions::multi_translation("publication_date")) . ': <time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date('j F Y')) . '</time>
            </footer>
        ' : '') . '
    </div>
</article>';

return $output;
