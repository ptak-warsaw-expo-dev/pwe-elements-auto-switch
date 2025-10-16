<?php
if (!defined('ABSPATH')) exit;

$first_page = array_slice($exhibitors_prepared ?? [], 0, $exhibitors_per_page);

$output .= '
<div id="exhibitorCatalog" class="exhibitor-catalog">
  <div class="exhibitor-catalog__heading">
    <h1 class="exhibitor-catalog__title">Exhibitor list 2025</h1>
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

      <div class="exhibitor-catalog__filters">
        <h2 class="exhibitor-catalog__filters-title">Wszystkie kategorie</h2>

        <div class="exhibitor-catalog__category-group">
          <div class="exhibitor-catalog__heading-container">
            <h3 class="exhibitor-catalog__category-heading">Wyróżnienia</h3>
          </div>
          <label class="exhibitor-catalog__checkbox">
            <input type="checkbox" class="exhibitor-catalog__checkbox-input" name="featured" value="1" />
            <span class="exhibitor-catalog__checkbox-label">Najwięksi wystawcy</span>
          </label>
          <label class="exhibitor-catalog__checkbox">
            <input type="checkbox" class="exhibitor-catalog__checkbox-input" name="newest" value="1" />
            <span class="exhibitor-catalog__checkbox-label">Najnowsi wystawcy</span>
          </label>
        </div>';

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
            <span class="exhibitor-catalog__checkbox-label">' . $products_tag . '</span>
          </label>';
    }
    $output .= '</div>';
}

$output .= '
      </div><!-- /filters -->

      <div class="exhibitor-catalog__list">
        <h2 class="exhibitor-catalog__counter">' . $exhibitors_count . ' Wyszukiwań</h2>';

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
        $brands              = $exhibitor['brands'] ?? '';
        $catalog_tags        = $exhibitor['catalog_tags'] ?? '';
        $hall_name           = $exhibitor['hall_name'] ?? '';
        $stand_number        = $exhibitor['stand_number'] ?? '';
        $products_count      = (int)($exhibitor['products_count'] ?? 0);
        $documents_count     = (int)($exhibitor['documents_count'] ?? 0);
        $products_preview    = $exhibitor['products_preview']  ?? [];
        $documents_preview   = $exhibitor['documents_preview'] ?? [];
        $id_numeric          = (int)($exhibitor['id_numeric'] ?? 0);
        $total_booth_area    = (float)($exhibitor['total_booth_area'] ?? 0);
        $is_featured         = !empty($featured_set[$exhibitor_id]);

        // HTML karty (pasujący do renderCard w JS + data-* na filtry/sort)
        $output .= '
        <div class="exhibitor-catalog__item" data-hall="' . mb_strtolower($hall_name) . '" data-tags="' . mb_strtolower($catalog_tags) . '" data-created="' . $id_numeric . '" data-area="' . $total_booth_area . '">';

        if ($is_featured) {
            $output .= '<div class="exhibitor-catalog__item-heading">Wyróżnieni wystawcy</div>';
        }

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
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="10" r="3" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle><path d="M19 9.75C19 15.375 12 21 12 21C12 21 5 15.375 5 9.75C5 6.02208 8.13401 3 12 3C15.866 3 19 6.02208 19 9.75Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                    <p>Stoisko ' . $stand_number . '</p>
                  </div>
                </div>

                <div class="exhibitor-catalog__contact">';

        if (!empty($website)) {
            $output .= '<div class="exhibitor-catalog__contact-item"><img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/world.png" alt=""><a href="' . $website . '" target="_blank">Strona www</a></div>';
        }
        if (!empty($contact_email)) {
            $output .= '<div class="exhibitor-catalog__contact-item"><img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/email.png" alt=""><a href="mailto:' . $contact_email . '">Email</a></div>';
        }
        if (!empty($contact_phone)) {
            $output .= '<div class="exhibitor-catalog__contact-item"><img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/phone.png" alt=""><a href="tel:' . $contact_phone . '">Telefon</a></div>';
        }

        $output .= '
                </div>
              </div>

              <div class="exhibitor-catalog__details">
                <h3 class="exhibitor-catalog__name">' . $name . '</h3>';

        if (!empty($description)) {
            $output .= '<p class="exhibitor-catalog__description">' . $description . '</p>';
        }
        if (!empty($brands)) {
            $output .= '
                <div class="exhibitor-catalog__brands">
                  <p class="exhibitor-catalog__label">Brands</p>
                  <p class="exhibitor-catalog__value">' . $brands . '</p>
                </div>';
        }
        if (!empty($catalog_tags)) {
            $output .= '
                <div class="exhibitor-catalog__categories">
                  <p class="exhibitor-catalog__label">Categories</p>
                  <p class="exhibitor-catalog__value">' . $catalog_tags . '</p>
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
                <div class="exhibitor-catalog__materials-list">';
            foreach ($documents_preview as $doc_item) {
                $doc_category = $doc_item['category'] ?? '';
                $doc_title    = $doc_item['title']    ?? '';
                $output .= '
                  <div class="exhibitor-catalog__material">
                    <p>' . $doc_category . '</p>
                    <div class="exhibitor-catalog__material-img">
                      <img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/document.png" alt="' . $doc_title . '" />
                    </div>
                  </div>';
            }
            $output .= '</div></div>';
        }

        $output .= '
            </div>
            <button type="button" class="exhibitor-catalog__open-modal">Zobacz szczegóły</button>
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