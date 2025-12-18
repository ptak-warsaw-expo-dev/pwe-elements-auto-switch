<?php
if (!defined('ABSPATH')) exit;

/**
 * Data formatting helpers for exhibitor catalog.
 *
 * Provides:
 *  - Company suffix normalization
 *  - Product tag normalization
 *  - Label/text shortening
 *  - Tag key normalization (aligned with JS)
 */


/**
 * Normalize long Polish company suffixes to short forms.
 *
 * @param string $name
 * @return string
 */
function ec_normalize_company_suffix($name) {
    $patterns = [
        '/spółka\s+z\s+ograniczoną\s+odpowiedzialnością/iu' => 'Sp. z o.o.',
        '/spółka\s+akcyjna/iu'                              => 'S.A.',
        '/spółka\s+komandytowo-akcyjna/iu'                  => 'S.K.A.',
        '/spółka\s+komandytowa/iu'                          => 'Sp.k.',
    ];

    foreach ($patterns as $regex => $replacement) {
        $name = preg_replace($regex, $replacement, $name);
    }

    return $name;
}

/**
 * Normalize product tags by splitting separators, cleaning invisible chars,
 * lowercasing, and ensuring uniqueness.
 *
 * @param array $tags
 * @return array
 */
function ec_normalize_product_tags($tags) {
    if (!is_array($tags)) return [];

    $out  = [];
    $seen = [];

    foreach ($tags as $tag) {
        if (!is_string($tag)) continue;

        $parts = preg_split(
            '/[!#\/•\|\(\)\[\]\{\}\:\;\+\_\=\*\&\^%\$@\?]+|\s{2,}/u',
            $tag
        );

        foreach ($parts as $p) {
            $p = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $p);
            $p = trim($p);
            $p = rtrim($p, ".,;");
            $p = preg_replace('/\s+/u', ' ', $p);

            if ($p === '') continue;

            $canon = mb_strtolower($p, 'UTF-8');

            if (!isset($seen[$canon])) {
                $seen[$canon] = true;
                $out[] = $canon;
            }
        }
    }

    return $out;
}


/**
 * Shorten a label using word limit and optional character limit.
 *
 * @param string $text
 * @param int $wordLimit
 * @param int|null $charLimit
 * @return string
 */
function ec_limit_labels($text, $wordLimit = 3, $charLimit = null) {
    $text = trim(preg_replace('/\s+/', ' ', $text));
    if ($text === '') return $text;

    $original = $text;
    $words    = explode(' ', $text);
    $short    = array_slice($words, 0, $wordLimit);

    if ($charLimit === null) {
        $result = implode(' ', $short);
        return ($result === $original) ? $result : $result . '...';
    }

    if (count($short) === 1) {
        $only = $short[0];
        if (mb_strlen($only, 'UTF-8') > $charLimit) {
            return mb_substr($only, 0, $charLimit, 'UTF-8') . '...';
        }
        return ($only === $original) ? $only : $only . '...';
    }

    while (!empty($short)) {
        $candidate = implode(' ', $short);
        if (mb_strlen($candidate, 'UTF-8') <= $charLimit) break;
        array_pop($short);
    }

    if (empty($short)) {
        return mb_substr($original, 0, $charLimit, 'UTF-8') . '...';
    }

    $connectors = ['i','a','o','w','z','u','na','do','po','od','dla'];

    if (count($short) > 1) {
        $last      = end($short);
        $lastLower = mb_strtolower($last, 'UTF-8');

        if (!(mb_strlen($last, 'UTF-8') === 1 && preg_match('/^[A-Z]$/u', $last))) {
            if (in_array($lastLower, $connectors, true)) {
                array_pop($short);
            }
        }
    }

    $result = implode(' ', $short);
    return ($result === $original) ? $result : $result . '...';
}


/**
 * Shorten text to a given character limit.
 *
 * @param string $text
 * @param int $limit
 * @return string
 */
function ec_shorten_text($text, $limit = 60) {
    $text = trim($text);

    if (mb_strlen($text) > $limit) {
        return mb_substr($text, 0, $limit - 3) . '...';
    }

    return $text;
}


/**
 * Normalize a tag to a lowercase canonical key.
 *
 * @param mixed $tag
 * @return string
 */
function ec_normalize_tag_key($tag) {
    if (!is_string($tag) && !is_numeric($tag)) return '';

    $tag = (string) $tag;
    $tag = trim(mb_strtolower($tag, 'UTF-8'));
    $tag = preg_replace('/[\x00-\x1F\x7F]/u', '', $tag);
    $tag = preg_replace('/\s+/u', ' ', $tag);

    return trim($tag);
}

/**
 * Normalize an array of tags into unique canonical forms.
 *
 * @param array $tags
 * @return array
 */
function ec_normalize_tags_array($tags) {
    if (!is_array($tags)) return [];

    $out = [];

    foreach ($tags as $t) {
        $key = ec_normalize_tag_key($t);
        if ($key !== '') {
            $out[] = $key;
        }
    }

    return array_values(array_unique($out));
}