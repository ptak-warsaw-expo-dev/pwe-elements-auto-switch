<?php
if (!defined('ABSPATH')) exit;

$first_page = array_slice($exhibitors_prepared ?? [], 0, $exhibitors_per_page);

if (!function_exists('pl_select')) {
  function pl_select($n) {
    $n = abs((int)$n);
    if ($n === 1) return 'one';
    $mod10  = $n % 10;
    $mod100 = $n % 100;
    if ($mod10 >= 2 && $mod10 <= 4 && !($mod100 >= 12 && $mod100 <= 14)) return 'few';
    if ($n === 0 || $mod10 === 0 || $mod10 >= 5 || ($mod100 >= 12 && $mod100 <= 14)) return 'many';
    return 'other';
  }

  function pl($key, $n) {
    static $NOUNS = [
      'Wyszukanie' => ['one'=>'Wyszukanie','few'=>'Wyszukania','many'=>'Wyszukań','other'=>'Wyszukań'],
    ];
    $cat = pl_select($n);
    return $NOUNS[$key][$cat] ?? '';
  }

  function fmt_count($n, $key) {
    return $n . ' ' . pl($key, $n);
  }
}

$output .= '
<div id="exhibitorCatalog" class="exhibitor-catalog">
  <div class="exhibitor-catalog__heading">
    <h1 class="exhibitor-catalog__title">Katalog wystawców</h1>
    <p class="exhibitor-catalog__heading_description">
      Wszystkie firmy, które zarezerwowały powierzchnię wystawienniczą, są wymienione alfabetycznie.
      Lista jest regularnie aktualizowana i może ulec zmianie.
    </p>
  </div>

  <div class="exhibitor-catalog__content">
    <div class="exhibitor-catalog__search">
      <input type="text" class="exhibitor-catalog__search-input" placeholder="Search exhibitors" />
      <div class="exhibitor-catalog__search-icon">
        <img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/search.png" alt="Szukaj" />
      </div>
    </div>

    <div class="exhibitor-catalog__container">

      <div class="exhibitor-catalog__filters" style="visibility:hidden;">
        <h2 class="exhibitor-catalog__filters-title">Wszystkie kategorie</h2>';

        // $output .= '
        // <div class="exhibitor-catalog__category-group">
        //   <div class="exhibitor-catalog__heading-container">
        //     <h3 class="exhibitor-catalog__category-heading">Wyróżnienia</h3>
        //   </div>
        //   <label class="exhibitor-catalog__checkbox">
        //     <input type="checkbox" class="exhibitor-catalog__checkbox-input" name="featured" value="1" />
        //     <div class="exhibitor-catalog__checkmark"></div>
        //     <span class="exhibitor-catalog__checkbox-label">Najwięksi wystawcy</span>
        //   </label>
        //   <label class="exhibitor-catalog__checkbox">
        //     <input type="checkbox" class="exhibitor-catalog__checkbox-input" name="newest" value="1" />
        //     <div class="exhibitor-catalog__checkmark"></div>
        //     <span class="exhibitor-catalog__checkbox-label">Nowi wystawcy</span>
        //   </label>
        // </div>';

        if (!empty($halls)) {
            $output .= '
                <div class="exhibitor-catalog__category-group">
                <div class="exhibitor-catalog__heading-container">
                    <h3 class="exhibitor-catalog__category-heading">Hale</h3>
                </div>';
            foreach ($halls as $hall_name) {
                $output .= '
                <label class="exhibitor-catalog__checkbox">
                    <input type="checkbox" class="exhibitor-catalog__checkbox-input" name="hall[]" value="' . $hall_name . '" />
                    <div class="exhibitor-catalog__checkmark"></div>
                    <span class="exhibitor-catalog__checkbox-label">' . $hall_name . '</span>
                </label>';
            }
            $output .= '</div>';
        }

        if (!empty($sectors)) {
            $output .= '
                <div class="exhibitor-catalog__category-group">
                <div class="exhibitor-catalog__heading-container">
                    <h3 class="exhibitor-catalog__category-heading">Sektory technologiczne</h3>
                </div>';
            foreach ($sectors as $sector_name) {
                $output .= '
                <label class="exhibitor-catalog__checkbox">
                    <input type="checkbox" class="exhibitor-catalog__checkbox-input" name="sector[]" value="' . $sector_name . '" />
                    <div class="exhibitor-catalog__checkmark"></div>
                    <span class="exhibitor-catalog__checkbox-label">' . $sector_name . '</span>
                </label>';
            }
            $output .= '</div>';
        }

        if (!empty($products_tags)) {
            $output .= '
                <div class="exhibitor-catalog__category-group">
                <div class="exhibitor-catalog__heading-container">
                    <h3 class="exhibitor-catalog__category-heading">Kategorie Produktów</h3>
                </div>';
            foreach ($products_tags as $products_tag) {
                $output .= '
                <label class="exhibitor-catalog__checkbox">
                    <input type="checkbox" class="exhibitor-catalog__checkbox-input" name="products_tag[]" value="' . $products_tag . '" />
                    <div class="exhibitor-catalog__checkmark"></div>
                    <span class="exhibitor-catalog__checkbox-label">' . $products_tag . '</span>
                </label>';
            }
            $output .= '</div>';
        }

$output .= '
      </div><!-- /filters -->

      <div class="exhibitor-catalog__list">
        <h2 class="exhibitor-catalog__counter">' . fmt_count($exhibitors_count, 'Wyszukanie') . '</h2>';

if (empty($first_page)) {
    $output .= '<p class="exhibitor-catalog__empty">Brak wyników.</p>';
} else {
    foreach ($first_page as $exhibitor) {
        // Spłaszczone pola (takie jak w exhibitor_catalog.php)
        $exhibitor_id        = (string)($exhibitor['exhibitor_id'] ?? '');
        $name                = $exhibitor['name'] ?? '';
        $logo_url            = $exhibitor['logo_url'] ?? '';
        $description         = $exhibitor['description'] ?? '';
        $website             = $exhibitor['website'] ?? '';
        $contact_phone       = $exhibitor['contact_phone'] ?? '';
        $contact_email       = $exhibitor['contact_email'] ?? '';

        $brands              = $exhibitor['brands'];
        $catalog_tags        = $exhibitor['catalog_tags'];
        $brands_text         = implode(', ', $brands);
        $catalog_tags_text   = implode(', ', $catalog_tags);
        $data_tags_attr      = implode(',', array_map('strtolower', array_map('trim', $catalog_tags)));

        $hall_name           = $exhibitor['hall_name'] ?? '';
        $stand_number        = $exhibitor['stand_number'] ?? '';
        $products_count      = (int)($exhibitor['products_count'] ?? 0);
        $documents_count     = (int)($exhibitor['documents_count'] ?? 0);
        $products_preview    = $exhibitor['products_preview']  ?? [];
        $documents_preview   = $exhibitor['documents_preview'] ?? [];
        $id_numeric          = (int)($exhibitor['id_numeric'] ?? 0);
        $total_booth_area    = (float)($exhibitor['total_booth_area'] ?? 0);

        // HTML karty (pasujący do renderCard w JS + data-* na filtry/sort)
        $output .= '
        <div class="exhibitor-catalog__item" data-id="' . $id_numeric . '" data-hall="' . $hall_name . '" data-tags="' . $data_tags_attr . '" data-created="' . $id_numeric . '" data-area="' . $total_booth_area . '">';

        $output .= '
          <div class="exhibitor-catalog__item-container">
            <div class="exhibitor-catalog__info">
              <div class="exhibitor-catalog__company-info">
                <div class="exhibitor-catalog__logo-tile">';

        if ($logo_url !== '') {
            $output .= '<img src="' . $logo_url . '" alt="' . $name . '" />';
        }

        $output .= '
                  <div class="exhibitor-catalog__stand">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 21C15.5 17.4 19 14.1764 19 10.2C19 6.22355 15.866 3 12 3C8.13401 3 5 6.22355 5 10.2C5 14.1764 8.5 17.4 12 21Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                    <p>Stoisko ' . $stand_number . '</p>
                  </div>
                </div>

                <div class="exhibitor-catalog__contact">';

        if (!empty($website)) {
            $output .= '
            <div class="exhibitor-catalog__contact-item">
                <a href="' . $website . '" target="_blank" rel="noopener">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.8 9h22.4M1.8 17h22.4M1 13a12 12 0 1 0 24 0 12 12 0 0 0-24 0" stroke="var(--main2-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12.333 1a22.67 22.67 0 0 0 0 24m1.333-24a22.67 22.67 0 0 1 0 24" stroke="var(--main2-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Strona www
                </a>
            </div>';
        }
        if (!empty($contact_email)) {
            $output .= '
            <div class="exhibitor-catalog__contact-item">
                <a href="mailto:' . $contact_email . '">
                    <svg width="28" height="22" viewBox="0 0 28 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.2 21.5a2.64 2.64 0 0 1-1.906-.77Q.5 19.96.5 18.875V3.125q0-1.083.794-1.853A2.64 2.64 0 0 1 3.2.5h21.6q1.113 0 1.907.772.795.771.793 1.853v15.75q0 1.083-.793 1.855a2.63 2.63 0 0 1-1.907.77zM14 12.313 24.8 5.75V3.125L14 9.688 3.2 3.125V5.75z" fill="var(--main2-color)"/></svg>
                    Email
                </a>
            </div>';
        }
        if (!empty($contact_phone)) {
            $output .= '
            <div class="exhibitor-catalog__contact-item">
                <a href="tel:' . $contact_phone . '">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22.6 24q-4.167 0-8.233-1.816t-7.4-5.15-5.15-7.4T0 1.4q0-.6.4-1t1-.4h5.4q.467 0 .833.317.368.318.434.75l.866 4.666q.068.534-.033.9a1.4 1.4 0 0 1-.367.634L5.3 10.533a16 16 0 0 0 1.583 2.383q.916 1.149 2.017 2.217a24 24 0 0 0 2.167 1.918 21 21 0 0 0 2.4 1.616l3.133-3.134q.3-.3.784-.449t.95-.084l4.6.933q.465.134.766.484.3.351.3.783v5.4q0 .6-.4 1t-1 .4" fill="var(--main2-color)"/></svg>
                    Telefon
                </a>
            </div>';
        }

        $output .= '
                </div>
              </div>

              <div class="exhibitor-catalog__details">
            <a class="exhibitor-catalog__open-modal-name" href="' . esc_url( add_query_arg( 'exhibitor_id', $exhibitor_id ) ) . '" target="_blank"><h3 class="exhibitor-catalog__name">' . $name . '</h3></a>';

        if (!empty($description)) {
            $output .= '<p class="exhibitor-catalog__description">' . $description . '</p>';
        }
        if (!empty($brands_text)) {
            $output .= '
                <div class="exhibitor-catalog__brands">
                  <p class="exhibitor-catalog__label">Brands</p>
                  <p class="exhibitor-catalog__value">' . $brands_text . '</p>
                </div>';
        }
        if (!empty($catalog_tags_text)) {
            $output .= '
                <div class="exhibitor-catalog__categories">
                  <p class="exhibitor-catalog__label">Categories</p>
                  <p class="exhibitor-catalog__value">' . $catalog_tags_text . '</p>
                </div>';
        }

        $output .= '
              </div>
            </div>

            <div class="exhibitor-catalog__extra">';

        if ($products_count > 0) {
            $output .= '
              <div class="exhibitor-catalog__products">
                <h4 class="exhibitor-catalog__products-title">Produkty (' . $products_count . ')</h4>
                <div class="exhibitor-catalog__products-list">';
            foreach ($products_preview as $product_item) {
                $product_img  = $product_item['img']  ?? '';
                $product_name = $product_item['name'] ?? '';
                $output .= '<div class="exhibitor-catalog__products-list-element">';
                if ($product_img !== '') {
                    $output .= '<img src="' . $product_img . '" alt="' . $product_name . '" />';
                }
                $output .= '</div>';
            }
            $output .= '</div></div>';
        }

        if ($documents_count > 0) {
            $output .= '
            <div class="exhibitor-catalog__materials">
                <h4 class="exhibitor-catalog__materials-title">MATERIAŁY DO POBRANIA (' . $documents_count . ')</h4>
                <div class="exhibitor-catalog__materials-list exhibitor-catalog__documents-list">';
            foreach ($documents_preview as $doc_item) {
                $doc_title    = $doc_item['title'] ?? '';
                $doc_file_url = $doc_item['fileUrl'] ?? '';
                $doc_file_view = $doc_item['viewUrl'] ?? '';
                $output .= '
                <div class="exhibitor-catalog__material exhibitor-catalog__documents-list-element" data-url="' . $doc_file_view . '" data-title="' . $doc_title . '">
                    <p>Dokument</p>
                    <div class="exhibitor-catalog__material-img">
                    <img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/document.png" alt="' . $doc_title . '" />
                    </div>
                </div>';
            }
            $output .= '</div></div>';
        }

        $output .= '
            </div>
            <a class="exhibitor-catalog__open-modal" href="' . esc_url( add_query_arg( 'exhibitor_id', $exhibitor_id ) ) . '" target="_blank">Zobacz szczegóły</a>
          </div>
        </div>';
    }
}

$output .= '
        <div id="infiniteSentinel"></div>
        <div id="infiniteLoader" class="exhibitor-loader" style="display:none;">Ładowanie…</div>
      </div>
    </div>
  </div>
</div>';

echo $output;