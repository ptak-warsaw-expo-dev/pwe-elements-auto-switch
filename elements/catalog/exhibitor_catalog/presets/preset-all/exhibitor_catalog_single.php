<?php
if (!defined('ABSPATH')) exit;

$name = $single_exhibitor['name'] ?? '';
$logo = $single_exhibitor['logo_url'] ?? '';
$desc = $single_exhibitor['description'] ?? '';
$why_visit = $single_exhibitor['why_visit'] ?? '';
$website = $single_exhibitor['website'] ?? '';
$contact_phone = $single_exhibitor['contact_phone'] ?? '';
$contact_email = $single_exhibitor['contact_email'] ?? '';

$brands = $single_exhibitor['brands'] ?? '';
$catalog_tags = $single_exhibitor['catalog_tags'] ?? '';
$industries = $single_exhibitor['industries'] ?? '';

$hall_name = $single_exhibitor['hall_name'] ?? '';
$stand_number = $single_exhibitor['stand_number'] ?? '';

$industries = $single_exhibitor['industries'] ?? '';

$products = $single_exhibitor['products'] ?? '';
$documents = $single_exhibitor['documents'] ?? '';

$products_count = count($products);
$documents_count = count($documents);


function pwe_normalize_url($url) {
    $url = trim((string)$url);
    if ($url === '') return '';
    if (!preg_match('~^https?://~i', $url)) {
        $url = 'https://' . ltrim($url, '/');
    }
    return $url;
}

$platform_fields = ['facebook','instagram','linkedin','youtube','tiktok','x'];
$social_items = [];

foreach ($platform_fields as $platform) {
    $val = $single_exhibitor[$platform] ?? '';
    $val = pwe_normalize_url($val);
    if ($val !== '') {
        $social_items[] = [
            'platform' => $platform,
            'url'      => $val,
        ];
    }
}

function pwe_social_svg($platform){
    // Proste, lekkie ikony SVG (możesz podmienić na własne)
    $svg = [
        'facebook' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#a)"><path d="M24 12c0-6.627-5.373-12-12-12S0 5.373 0 12c0 5.99 4.388 10.954 10.125 11.854V15.47H7.078V12h3.047V9.356c0-3.007 1.792-4.668 4.533-4.668 1.313 0 2.686.234 2.686.234v2.953H15.83c-1.491 0-1.956.925-1.956 1.874V12h3.328l-.532 3.469h-2.796v8.385C19.612 22.954 24 17.99 24 12" fill="#1877F2"/><path d="M16.671 15.469 17.203 12h-3.328V9.75c0-.95.465-1.875 1.956-1.875h1.513V4.922s-1.374-.234-2.687-.234c-2.74 0-4.532 1.66-4.532 4.668V12H7.078v3.469h3.047v8.385a12.1 12.1 0 0 0 3.75 0V15.47z" fill="#fff"/></g><defs><clipPath id="a"><path fill="#fff" d="M0 0h24v24H0z"/></clipPath></defs></svg>',
        'instagram'=> '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#a)"><path d="M18.375 0H5.625A5.625 5.625 0 0 0 0 5.625v12.75A5.625 5.625 0 0 0 5.625 24h12.75A5.625 5.625 0 0 0 24 18.375V5.625A5.625 5.625 0 0 0 18.375 0" fill="url(#b)"/><path d="M18.375 0H5.625A5.625 5.625 0 0 0 0 5.625v12.75A5.625 5.625 0 0 0 5.625 24h12.75A5.625 5.625 0 0 0 24 18.375V5.625A5.625 5.625 0 0 0 18.375 0" fill="url(#c)"/><path d="M12 2.625c-2.545 0-2.865.011-3.865.057-.998.045-1.68.203-2.275.435A4.6 4.6 0 0 0 4.2 4.198a4.6 4.6 0 0 0-1.083 1.66c-.232.597-.39 1.279-.436 2.276-.044 1-.056 1.32-.056 3.866s.011 2.865.057 3.865c.046.998.204 1.68.435 2.275.24.617.56 1.14 1.081 1.66a4.6 4.6 0 0 0 1.66 1.083c.597.232 1.279.39 2.276.436 1 .045 1.32.056 3.866.056s2.865-.011 3.865-.056c.998-.046 1.68-.204 2.276-.436a4.6 4.6 0 0 0 1.66-1.082 4.6 4.6 0 0 0 1.082-1.66c.23-.597.389-1.278.435-2.276.045-1 .057-1.319.057-3.865s-.012-2.866-.057-3.866c-.047-.998-.205-1.679-.435-2.275A4.6 4.6 0 0 0 19.8 4.2a4.6 4.6 0 0 0-1.66-1.082c-.598-.232-1.28-.39-2.278-.435-1-.046-1.318-.057-3.865-.057zm-.84 1.69H12c2.504 0 2.8.008 3.79.053.913.042 1.41.195 1.74.323.437.17.75.373 1.077.702.328.328.531.64.702 1.078.128.33.281.826.323 1.74.044.988.054 1.285.054 3.787s-.01 2.799-.054 3.787c-.042.914-.195 1.41-.323 1.74a2.9 2.9 0 0 1-.702 1.077c-.328.328-.64.532-1.077.701-.33.13-.827.282-1.74.324-.99.045-1.286.054-3.79.054-2.502 0-2.8-.01-3.787-.054-.914-.043-1.41-.195-1.741-.324a2.9 2.9 0 0 1-1.078-.7 2.9 2.9 0 0 1-.702-1.078c-.128-.33-.281-.827-.323-1.74-.045-.989-.054-1.286-.054-3.79s.01-2.798.054-3.787c.042-.914.195-1.41.323-1.74.17-.438.373-.75.702-1.078s.64-.532 1.078-.702c.33-.129.827-.281 1.74-.323.865-.04 1.2-.05 2.948-.053zm5.845 1.556a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25m-5.004 1.315a4.815 4.815 0 1 0 0 9.629 4.815 4.815 0 0 0 0-9.63m0 1.689a3.125 3.125 0 1 1 0 6.25 3.125 3.125 0 0 1 0-6.25" fill="#fff"/></g><defs><radialGradient id="b" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="matrix(0 -23.7858 22.1227 0 6.35 25.855)"><stop stop-color="#FD5"/><stop offset=".1" stop-color="#FD5"/><stop offset=".5" stop-color="#FF543E"/><stop offset="1" stop-color="#C837AB"/></radialGradient><radialGradient id="c" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="rotate(78.681 -3.065 -1.588)scale(10.6324 43.827)"><stop stop-color="#3771C8"/><stop offset=".128" stop-color="#3771C8"/><stop offset="1" stop-color="#60F" stop-opacity="0"/></radialGradient><clipPath id="a"><path fill="#fff" d="M0 0h24v24H0z"/></clipPath></defs></svg>',
        'linkedin' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#a)"><path d="M21.75.562H2.25a1.67 1.67 0 0 0-1.687 1.65v19.58a1.67 1.67 0 0 0 1.687 1.645h19.5a1.674 1.674 0 0 0 1.688-1.651V2.206A1.674 1.674 0 0 0 21.75.563" fill="#0076B2"/><path d="M3.949 9.137h3.395v10.926H3.95zm1.698-5.438a1.969 1.969 0 1 1 0 3.938 1.969 1.969 0 0 1 0-3.938m3.827 5.438h3.255v1.5h.045c.454-.859 1.56-1.765 3.212-1.765 3.439-.007 4.076 2.256 4.076 5.19v6h-3.395v-5.315c0-1.266-.023-2.895-1.765-2.895s-2.038 1.38-2.038 2.812v5.399h-3.39z" fill="#fff"/></g><defs><clipPath id="a"><path fill="#fff" d="M0 0h24v24H0z"/></clipPath></defs></svg>',
        'youtube'  => '<svg width="24" height="24" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="none"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path fill="red" d="M14.712 4.633a1.754 1.754 0 00-1.234-1.234C12.382 3.11 8 3.11 8 3.11s-4.382 0-5.478.289c-.6.161-1.072.634-1.234 1.234C1 5.728 1 8 1 8s0 2.283.288 3.367c.162.6.635 1.073 1.234 1.234C3.618 12.89 8 12.89 8 12.89s4.382 0 5.478-.289a1.754 1.754 0 001.234-1.234C15 10.272 15 8 15 8s0-2.272-.288-3.367z"></path><path fill="#ffffff" d="M6.593 10.11l3.644-2.098-3.644-2.11v4.208z"></path></g></svg>',
        'tiktok'   => '<svg width="22" height="24" viewBox="0 0 22 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#a)"><path d="M15.703 8.642a9.4 9.4 0 0 0 5.484 1.754V6.462q-.582 0-1.151-.12v3.096a9.4 9.4 0 0 1-5.485-1.754v8.027a7.273 7.273 0 0 1-7.274 7.271 7.24 7.24 0 0 1-4.05-1.23 7.25 7.25 0 0 0 5.201 2.188 7.273 7.273 0 0 0 7.275-7.271zm1.42-3.969a5.48 5.48 0 0 1-1.42-3.21V.959H14.61a5.5 5.5 0 0 0 2.513 3.715M5.769 18.671a3.327 3.327 0 0 1 3.658-5.183V9.466A7 7 0 0 0 8.275 9.4v3.13a3.327 3.327 0 0 0-2.507 6.141" fill="#FF004F"/><path d="M14.551 7.684a9.4 9.4 0 0 0 5.485 1.754V6.342a5.5 5.5 0 0 1-2.912-1.669A5.5 5.5 0 0 1 14.61.958h-2.867v15.71a3.327 3.327 0 0 1-5.976 2.003 3.325 3.325 0 0 1 1.497-6.297c.352 0 .691.055 1.01.156V9.4a7.272 7.272 0 0 0-5.048 12.353 7.24 7.24 0 0 0 4.05 1.23 7.273 7.273 0 0 0 7.274-7.272z" fill="#000"/><path d="M20.036 6.342v-.837a5.5 5.5 0 0 1-2.912-.832 5.5 5.5 0 0 0 2.912 1.669M14.61.958a6 6 0 0 1-.06-.452V0h-3.959v15.711a3.327 3.327 0 0 1-4.824 2.96 3.327 3.327 0 0 0 5.977-2.002V.958zM8.275 9.4v-.892a7.273 7.273 0 0 0-8.273 7.203 7.26 7.26 0 0 0 3.225 6.041A7.272 7.272 0 0 1 8.275 9.4" fill="#00F2EA"/></g><defs><clipPath id="a"><path fill="#fff" d="M0 0h21.19v24H0z"/></clipPath></defs></svg>',
        'x'        => '<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#a)"><path d="M14.424 10.163 23.167 0h-2.072l-7.591 8.824L7.441 0H.448l9.168 13.343L.448 24H2.52l8.016-9.318L16.94 24h6.993zm-2.837 3.298-.93-1.329L3.268 1.56H6.45l5.964 8.532.929 1.329 7.754 11.09h-3.182z" fill="#000"/></g><defs><clipPath id="a"><path fill="#fff" d="M.19 0h24v24h-24z"/></clipPath></defs></svg>',
    ];
    return $svg[$platform] ?? $svg['generic'];
}


$output .= '
<div id="exhibitorModal" class="exhibitor-single">
  <div class="exhibitor-single__heading"></div>
  <div class="exhibitor-single__container">
    <div class="exhibitor-single__sidebar">
        <div class="exhibitor-single__logo">
            <img src="' . $logo . '" alt="' . $name . '">
        </div>
        <div class="exhibitor-single__company-conatiner">';
            if(!empty($hall_name)) {
                $output .= '
                <div class="exhibitor-single__sidebar-tile">
                    <p class="exhibitor-single__sidebar-tile-label">Lokalizacja</p>
                    <div class="exhibitor-single__sidebar-tile-title">' . $hall_name . ', ' . $stand_number . '</div>
                </div>';
            }
            if(!empty($website)) {
                $output .= '
                <div class="exhibitor-single__sidebar-tile">
                    <p class="exhibitor-single__sidebar-tile-label">Strona</p>
                    <h2 class="exhibitor-single__sidebar-tile-title"><a href="' . $website . '" target="_blank" rel="noopener">' . $website . '</a></h2>
                </div>';
            }
            if(!empty($contact_email)) {
                $output .= '
                <div class="exhibitor-single__sidebar-tile">
                    <p class="exhibitor-single__sidebar-tile-label">E-mail</p>
                    <h2 class="exhibitor-single__sidebar-tile-title"><a href="mailto:' . $contact_email . '">' . $contact_email . '</a></h2>
                </div>';
            }
            if(!empty($contact_phone)) {
                $output .= '
                <div class="exhibitor-single__sidebar-tile">
                    <p class="exhibitor-single__sidebar-tile-label">Telefon</p>
                    <h2 class="exhibitor-single__sidebar-tile-title"><a href="tel:' . $contact_phone . '">' . $contact_phone . '</a></h2>
                </div>';
            }
            if (!empty($social_items)) {
                $output .= '
                <div class="exhibitor-single__sidebar-socials">';
                    foreach ($social_items as $item) {
                        $platform = $item['platform'];
                        $url      = $item['url'];
                        $label    = ucfirst($platform);
                        $icon     = pwe_social_svg($platform);

                        $output .= '
                            <div class="exhibitor-single__socials-item exhibitor-social--'. esc_attr($platform) .'">
                                <a class="exhibitor-single__socials-link" href="'. esc_url($url) .'" target="_blank" rel="noopener noreferrer" aria-label="'. esc_attr($label) .'">
                                    '.$icon.'
                                </a>
                            </div>';
                    }
                    $output .= '
                </div>';
            }
            $output .= '
        </div>
    </div>
    <div class="exhibitor-single__content">

        <div class="exhibitor-single__header">
            <h1 class="exhibitor-single__title">' . $name . '</h1>
        </div>

        <div class="exhibitor-single__description">';
            if(!empty($desc)) {
                $output .= '
                <h3 class="exhibitor-single__subtitle">Opis</h3>
                <p class="exhibitor-single__text">' . $desc . '</p>';
            }
            if(!empty($why_visit)) {
                $output .= '
                <h3 class="exhibitor-single__subtitle">Dlaczego warto odwiedzić nasze stoisko</h3>
                <p class="exhibitor-single__text">' . $why_visit . '</p>';
            }
            $output .= '
        </div>';

        if(!empty($brands)) {
            $output .= '
            <div class="exhibitor-single__brands">
                <h3 class="exhibitor-single__brands-title">Marki, które zaprezentujemy</h3>
                <ul class="exhibitor-single__brands-list">';
                    foreach ($brands as $brand) {
                        $output .= '
                        <li class="exhibitor-single__brand">' . $brand . '</li>';
                    }
                $output .= '
                </ul>
            </div>';
        }

        if (!empty($products) || !empty($catalog_tags)) {
            $output .= '
            <div class="exhibitor-single__separator"></div>
            <div class="exhibitor-single__categories">
                <h3 class="exhibitor-single__categories-title">Kategorie</h3>';

                if (!empty($products) && !empty($hall_name)) {
                    $product_tags = [];

                    foreach ($products as $product) {
                        if (!empty($product['tags']) && is_array($product['tags'])) {
                            foreach ($product['tags'] as $tag) {
                                $product_tags[] = trim($tag);
                            }
                        }
                    }

                    $product_tags = array_unique(array_filter($product_tags));

                    if (!empty($product_tags)) {
                        $output .= '
                        <div class="exhibitor-single__category">
                            <h4 class="exhibitor-single__category-name">Wyszukiwanie produktów</h4>
                            <div class="exhibitor-single__category-list">';

                        foreach ($product_tags as $tag) {
                            $output .= '
                                <div class="exhibitor-single__category-value">' . $tag . '</div>';
                        }

                        $output .= '
                            </div>
                        </div>';
                    }
                }

                if (!empty($catalog_tags) && is_array($catalog_tags)) {
                    $unique_catalog_tags = array_unique(array_filter(array_map('trim', $catalog_tags)));

                    if (!empty($unique_catalog_tags)) {
                        $output .= '
                        <div class="exhibitor-single__category">
                            <h4 class="exhibitor-single__category-name">Sektory Technologiczne</h4>
                            <div class="exhibitor-single__category-list">';

                        foreach ($unique_catalog_tags as $tag) {
                            $output .= '
                                <div class="exhibitor-single__category-value">' . $tag . '</div>';
                        }

                        $output .= '
                            </div>
                        </div>';
                    }
                }

            $output .= '
            </div>';
        }

        if (!empty($products)) {
            $output .= '
            <div class="exhibitor-single__separator"></div>
            <div class="exhibitor-single__products">
                <h3 class="exhibitor-single__products-title">Produkty <span class="exhibitor-single__products-title-count">(' . $products_count . ')</span></h3>
                <div class="exhibitor-single__products-list">';
                    foreach ($products as $product) {

                        $product_name = $product['name'];
                        $product_desc = $product['description'];
                        $product_img  = $product['img'];

                        $output .= '
                        <div class="exhibitor-single__product" role="button" tabindex="0" data-desc="' . $product_desc . '">
                            <div class="exhibitor-single__product-img">
                                <img src="' . $product_img . '" alt="' . $product_name . '">
                            </div>
                            <p class="exhibitor-single__product-title">' . $product_name . '</p>
                        </div>';
                    }
                    $output .= '
                </div>
            </div>';
        }

        if(!empty($documents)) {
        
            $output .= '
            <div class="exhibitor-single__separator"></div>
            <div class="exhibitor-single__documents">
                <h3 class="exhibitor-single__documents-title">Dokumenty <span class="exhibitor-single__documents-title-count">(' . $documents_count . ')</span></h3>
                <div class="exhibitor-single__documents-list">';
                    foreach($documents as $document) {
    
                        $document_view = $document['viewUrl'];
                        $document_url = $document['downloadUrl'];
                        $document_title = $document['title'];
    
                        $output .='
                        <div class="exhibitor-single__documents-element" data-view="' . $document_view . '" data-url="'  . $document_url  . '" data-title="' . $document_title . '">
                            <div class="exhibitor-single__documents-element-container">
                                <p>Broszura</p>
                                <div class="exhibitor-catalog__documents-img">
                                    <img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/document.png" alt="">
                                </div>
                            </div>
                        </div>';
                    }
                    $output .='
                </div>
            </div>';
        }
        $output .='
    </div>
  </div>
</div>
';

echo $output;