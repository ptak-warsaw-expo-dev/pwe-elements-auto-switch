<?php

if ($logotypes_slug === 'patrons-partners') {

  // Helper for slugs
  if (!function_exists('pwe_slugify')) {
    function pwe_slugify($text){
        if (function_exists('sanitize_title')) return sanitize_title($text);
        $text = iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$text);
        $text = strtolower($text);
        $text = preg_replace('~[^a-z0-9]+~','-',$text);
        return trim($text,'-') ?: 'inne';
    }
  }

  $is_pl = (get_locale() === 'pl_PL');

  // Categories dynamically from desc_pl / desc_en
  $cats = [];
  $total = count($logotypes);

  foreach ($logotypes as $logo){
      $label = trim($is_pl ? $logo['desc_pl'] : $logo['desc_en']);
      if ($label === '') {
          $label = $is_pl ? 'Inne' : 'Other';
      }

      $slug = pwe_slugify($label);

      if (!isset($cats[$slug])) {
          $cats[$slug] = [
              'label' => $label,
              'count' => 0
          ];
      }
      $cats[$slug]['count']++;
  }

  $output = '
  <style>
    .pwe-limit-width:has(.pwe-logotypes) {
      max-width: none !important;
      padding:0 !important;
      background-color: #09090b;
      border-top: 1px solid #18181b;
    }
    #pweLogotypesPatronsPartners {
      max-width: 1200px;
      margin: 0 auto;
    }
    #pweLogotypes'. $slug_id .' {
      padding: 6rem 0;
      color: #ffffff;
    }
    #pweLogotypes'. $slug_id .' .pwe-main-title {
      font-size: 2.25rem;
      font-weight: 900;
      color: #ffffff !important;
      text-transform: uppercase;
      letter-spacing: -0.05em;
      margin: 0;
      line-height: 1.1;
    }
    @media (min-width: 768px) {
      #pweLogotypes'. $slug_id .' .pwe-main-title { font-size: 3rem; }
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__title {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-bottom: 2rem;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__text {
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:16px;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__buttons {
      margin-top: 18px;
      display: flex;
      margin-bottom: 3rem;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__filters {
      display:flex;
      width: 100%;
      gap:10px;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__filter-all {
      flex: 0 0 auto;
      white-space: nowrap;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__filter-all .swiper-buttons-arrows {
      display: none !important;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__filter-other {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      background-color: #18181b;
      border: 1px solid #27272a;
      border-radius: 9999px;
      padding: 0.375rem;
      width: fit-content;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__filter {
      padding: 0.625rem 1.25rem;
      border: 0;
      border-radius: 9999px;
      background: transparent;
      color: #a1a1aa;
      cursor:pointer;
      font-weight:700;
      font-size: 0.875rem;
      line-height:1;
      display: inline-flex;
      align-items: center;
      text-align: left;
      transition: all 0.3s ease;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__filter-all .pwe-logotypes__filter {
      background-color: #18181b;
      border: 1px solid #27272a;
      height: 100%;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__filter.is-active,
    #pweLogotypes'. $slug_id .' .pwe-logotypes__filter-all .pwe-logotypes__filter.is-active {
      background: var(--accent-color) !important;
      border-color: var(--accent-color) !important;
      color: #ffffff;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__filter-other .pwe-logotypes__filter {
      font-size: 12px;
      padding: 0.5rem 1rem;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__filters span {
      font-size: 11px;
      background: rgba(255, 255, 255, 0.1);
      padding: 2px 6px;
      border-radius: 4px;
      margin-left: 6px;
      color: #ffffff;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__items {
      width:100%;
      margin-top:18px;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__item {
      background: white;
      border: 1px solid #27272a;
      border-radius: 1.5rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      aspect-ratio: 3/2;
      text-decoration: none;
      overflow: hidden;
      transition: border-color 0.3s ease, transform 0.3s ease;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__item:hover {
      border-color: var(--accent-color);
      transform: translateY(-2px);
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__item img {
      aspect-ratio: 3 / 1.6;
      flex: 1;
      object-fit: contain;
      padding: 20px;
      max-width: 100%;
      height: auto;
      transition: filter 0.3s ease;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__item:hover img {
      filter: grayscale(0) brightness(1);
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__item p {
      text-transform: uppercase;
      font-size: 11px;
      font-weight: 900;
      color: #a1a1aa;
      white-space: break-spaces;
      text-align: center;
      line-height: 1.2;
      margin: 0 0 12px 0;
      padding: 0 10px;
      flex-shrink: 0;
    }
    #pweLogotypes'. $slug_id .' .swiper-buttons-arrows {
      display: flex;
      gap: 0.75rem;
    }
    #pweLogotypes'. $slug_id .' .swiper-button-prev,
    #pweLogotypes'. $slug_id .' .swiper-button-next {
      position: static !important;
      margin-top: 0 !important;
      width: 3rem !important;
      height: 3rem !important;
      background-color: #18181b !important;
      border: 1px solid #27272a !important;
      border-radius: 9999px !important;
      color: #ffffff !important;
      display: inline-flex !important;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 14px;
      transition: all 0.3s ease !important;
    }
    #pweLogotypes'. $slug_id .' .swiper-button-prev::after,
    #pweLogotypes'. $slug_id .' .swiper-button-next::after {
      display: none !important;
    }
    #pweLogotypes'. $slug_id .' .swiper-button-prev:hover,
    #pweLogotypes'. $slug_id .' .swiper-button-next:hover {
      background-color: var(--accent-color) !important;
      border-color: var(--accent-color) !important;
    }
    #pweLogotypes'. $slug_id .' .swiper-nav {
      display: flex;
      justify-content: center;
      margin-top: 2.5rem;
    }
    // #pweLogotypes'. $slug_id .' .swiper-dots .swiper-pagination-bullet {
    //   background: #27272a !important;
    //   opacity: 1 !important;
    //   width: 10px;
    //   height: 10px;
    //   margin: 0 5px !important;
    //   transition: all 0.3s ease;
    // }
    // #pweLogotypesPatronsPartners .swiper-pagination-bullet {
    //   background:white !important;
    // }
    // #pweLogotypesPatronsPartners .swiper-pagination-bullet-active {
    //   background: var(--accent-color) !important;
    //   transform: scale(1.2);
    // }
    @media(max-width:960px) {
      #pweLogotypes'. $slug_id .' .pwe-logotypes__filter-all {
        width: 100%;
      }
      #pweLogotypes'. $slug_id .' .pwe-logotypes__filter-all .swiper-buttons-arrows {
        display: flex !important;
      }
      #pweLogotypes'. $slug_id .' .pwe-logotypes__title .swiper-buttons-arrows {
        display: none !important;
      }
      #pweLogotypes'. $slug_id .' .pwe-logotypes__filters {
        flex-direction: column;
      }
      #pweLogotypes'. $slug_id .' .pwe-logotypes__filter-all {
        display: flex;
        justify-content: space-between;
      }
      #pweLogotypes'. $slug_id .' .pwe-logotypes__filter-other  {
        display: grid;
        grid-template-columns: 1fr 1fr;
        border-radius: 1rem;
        width: 100%;
      }
    }
    @media(max-width:450px) {
      #pweLogotypes'. $slug_id .' .pwe-logotypes__filters {
        gap: 8px;
      }
    }
  </style>';

  $output .= '
  <div id="pweLogotypes'. $slug_id .'" class="pwe-logotypes '. $logotypes_slug .'">
      <div class="pwe-logotypes__wrapper">

          <div class="pwe-logotypes__title">
            <h4 class="pwe-main-title">'. esc_html($title) .'</h4>
          </div>

          <div class="pwe-logotypes__buttons">
              <div class="pwe-logotypes__filters" role="tablist" aria-label="Filtry logotypów">

                  <div class="pwe-logotypes__filter-all">
                    <button class="pwe-logotypes__filter is-active" data-filter="all">
                      ' . PWE_Functions::multi_translation('all') . ' <span>'. intval($total) .'</span>
                    </button>
                    <div class="swiper-buttons-arrows">
                      <div class="swiper-button-prev"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg></div>
                      <div class="swiper-button-next"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></div>
                    </div>
                  </div>

                  <div class="pwe-logotypes__filter-other">';

                      foreach ($cats as $slug=>$data){

                        $output .= '
                        <button class="pwe-logotypes__filter" data-filter="'. esc_attr($slug) .'">'
                          . esc_html($data['label']) .'
                          <span>'. intval($data['count']) .'</span>
                        </button>';
                      }

                      $output .= '
                  </div>
              </div>
          </div>

          <div class="pwe-logotypes__items swiper">
              <div class="swiper-wrapper">';

                  foreach ($logotypes as $logo){
                      $label = trim($is_pl ? $logo['desc_pl'] : $logo['desc_en']);
                      if ($label === '') {
                          $label = $is_pl ? 'Inne' : 'Other';
                      }

                      $cat = pwe_slugify($label);
                      $name  = $logo['name'] ?? '';
                      $alt   = $logo['alt']  ?? $name;

                      if (empty($logo['link'])) {
                          $output .= '
                          <div class="swiper-slide" data-cat="'. esc_attr($cat) .'">
                            <div class="pwe-logotypes__item">
                                <img
                                  src="'. esc_url($logo['url']) .'"
                                  alt="'. esc_attr($alt) .'"
                                  onerror="this.onerror=null; this.closest(\'.swiper-slide\').style.display=\'none\';"
                                />';
                                $output .= ($name ? '<p>'. esc_html($name) .'</p>' : '');
                                $output .= '
                            </div>
                          </div>';
                      } else {
                          $output .= '
                          <div class="swiper-slide" data-cat="'. esc_attr($cat) .'">
                            <a class="pwe-logotypes__item" target="_blank" href="'. esc_url($logo['link']) .'">
                                <img
                                  src="'. esc_url($logo['url']) .'"
                                  alt="'. esc_attr($alt) .'"
                                  onerror="this.onerror=null; this.closest(\'.swiper-slide\').style.display=\'none\';"
                                />';
                                $output .= ($name ? '<p>'. esc_html($name) .'</p>' : '');
                                $output .= '
                            </a>
                          </div>';
                      }
                  }

              $output .= '
              </div>
          </div>

          <div class="swiper-nav">
            <div class="swiper-dots" aria-label="Slider navigation" role="tablist"></div>
          </div>

      </div>
  </div>';

  $output .= PWE_Swiper::swiperScripts('#pweLogotypes'. $slug_id, [0=>['slidesPerView'=>2],650=>['slidesPerView'=>3],960=>['slidesPerView'=>5]], true, true, 3);

  $logos_data = array_map(function($l) use ($is_pl){
      $label = trim($is_pl ? $l['desc_pl'] : $l['desc_en']);
      if ($label === '') {
          $label = $is_pl ? 'Inne' : 'Other';
      }

      return [
          'cat'  => pwe_slugify($label),
          'url'  => $l['url'],
          'name' => $l['name'] ?? '',
          'alt'  => $l['alt'] ?? ($l['name'] ?? ''),
          'link' => $l['link'] ?? ''
      ];
  }, $logotypes);

  $gap = preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $_SERVER['HTTP_USER_AGENT']) ? 10 : 18;

  $output .= '<script>window.PWE_LOGOS = window.PWE_LOGOS || {};window.PWE_LOGOS["'.esc_js($slug_id).'"] = '.wp_json_encode($logos_data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).';</script>';

  $output .= '
  <script>
    jQuery(function($){
      const idSel = "#pweLogotypes'. $slug_id .'";
      const $root = $(idSel);
      if (!$root.length) return;

      const dataAll = (window.PWE_LOGOS && window.PWE_LOGOS["'. esc_js($slug_id) .'"]) || [];

      function buildSlidesHtml(items){
        return items.map(function(d){
          const inner = d.link
            ? `<a class="pwe-logotypes__item" target="_blank" href="${d.link}">
                <img src="${d.url}" alt="${d.alt||""}">
                ${d.name ? `<p>${d.name}</p>` : ``}
              </a>`
            : `<div class="pwe-logotypes__item">
                <img src="${d.url}" alt="${d.alt||""}">
                ${d.name ? `<p>${d.name}</p>` : ``}
              </div>`;
          return `<div class="swiper-slide" data-cat="${d.cat}">${inner}</div>`;
        }).join("");
      }

      function equalizeHeights(){
        let maxH = 0;
        const $slides = $root.find(".swiper-slide");
        $slides.css({height:"auto", minHeight:""});
        $slides.each(function(){ maxH = Math.max(maxH, $(this).outerHeight()); });
        $slides.css("minHeight", maxH);
      }

      function reinitSwiper(){
        const old = $root[0].__wcSwiper;
        let params = null;
        if (old && old.params){
          params = JSON.parse(JSON.stringify(old.params));
          try{ old.destroy(true, true); }catch(e){}
        }
        if (!params){
          params = {
            spaceBetween: '. $gap .',
            grabCursor: true,
            loop: false,
            grid: { rows: 3, fill: "row" },
            autoplay: { delay: 3000, disableOnInteraction: false, pauseOnMouseEnter: true },
            navigation: {
              nextEl: idSel + " .swiper-button-next",
              prevEl: idSel + " .swiper-button-prev"
            },
            breakpoints: '. wp_json_encode([0=>['slidesPerView'=>2],650=>['slidesPerView'=>3],960=>['slidesPerView'=>5]], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) .'
          };
        } else {
          params.loop = false;
        }

        const swiper = new Swiper(idSel + " .swiper", params);
        $root[0].__wcSwiper = swiper;
        if ($root[0].__buildDots) $root[0].__buildDots(swiper);
        setTimeout(equalizeHeights, 30);
      }

      $root.on("click", ".pwe-logotypes__filter", function(){
        const filter = this.dataset.filter || "all";
        $root.find(".pwe-logotypes__filter").removeClass("is-active");
        $(this).addClass("is-active");

        const subset = (filter === "all") ? dataAll : dataAll.filter(d => d.cat === filter);
        const $wrap = $root.find(".swiper-wrapper");
        $wrap.html( buildSlidesHtml(subset.length ? subset : dataAll) );

        reinitSwiper();
      });

      setTimeout(equalizeHeights, 50);
      $(window).on("resize", equalizeHeights);
    });
  </script>';
} else if ($logotypes_slug === 'patrons-partners-international') {
    $output = '
      <style>
        #pweLogotypes'. $slug_id .' {
          padding: 6rem 0;
          background-color: #09090b;
          border-top: 1px solid #18181b;
          color: #ffffff;
        }
        #pweLogotypes'. $slug_id .' .pwe-main-title {
          font-size: 2.25rem;
          font-weight: 900;
          color: #ffffff !important;
          text-transform: uppercase;
          letter-spacing: -0.05em;
          margin: 0;
          line-height: 1.1;
        }
        @media (min-width: 768px) {
          #pweLogotypes'. $slug_id .' .pwe-main-title { font-size: 3rem; }
        }
        #pweLogotypes'. $slug_id .' .pwe-logotypes__title {
          display: flex;
          justify-content: space-between;
          align-items: flex-end;
          margin-bottom: 2rem;
        }
        #pweLogotypes'. $slug_id .' .pwe-logotypes__items {
          margin-top: 18px;
        }
        #pweLogotypes'. $slug_id .' .pwe-logotypes__item {
          background: #18181b;
          border: 1px solid #27272a;
          border-radius: 1.5rem;
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
          text-decoration: none;
          overflow: hidden;
          aspect-ratio: 3/2;
          transition: border-color 0.3s ease, transform 0.3s ease;
        }
        #pweLogotypes'. $slug_id .' .pwe-logotypes__item:hover {
          border-color: var(--accent-color);
          transform: translateY(-2px);
        }
        #pweLogotypes'. $slug_id .' .pwe-logotypes__item img {
          aspect-ratio: 3/1.6;
          object-fit: contain;
          padding: 20px;
          filter: grayscale(1) brightness(0.9);
          transition: filter 0.3s ease;
        }
        #pweLogotypes'. $slug_id .' .pwe-logotypes__item:hover img {
          filter: grayscale(0) brightness(1);
        }
        #pweLogotypes'. $slug_id .' .pwe-logotypes__item p {
          text-transform: uppercase;
          font-size: 11px;
          font-weight: 900;
          color: #ffffff;
          white-space: break-spaces;
          text-align: center;
          line-height: 1.2;
          margin: 0;
          width: 100%;
          background: var(--accent-color);
          padding: 8px;
        }
        #pweLogotypes'. $slug_id .' .swiper-buttons-arrows {
          display: flex;
          gap: 0.75rem;
        }
        #pweLogotypes'. $slug_id .' .swiper-button-prev,
        #pweLogotypes'. $slug_id .' .swiper-button-next {
          position: static !important;
          margin-top: 0 !important;
          width: 3rem !important;
          height: 3rem !important;
          background-color: #18181b !important;
          border: 1px solid #27272a !important;
          border-radius: 9999px !important;
          color: #ffffff !important;
          display: inline-flex !important;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          font-size: 14px;
          transition: all 0.3s ease !important;
        }
        #pweLogotypes'. $slug_id .' .swiper-button-prev::after,
        #pweLogotypes'. $slug_id .' .swiper-button-next::after {
          display: none !important;
        }
        #pweLogotypes'. $slug_id .' .swiper-button-prev:hover,
        #pweLogotypes'. $slug_id .' .swiper-button-next:hover {
          background-color: var(--accent-color) !important;
          border-color: var(--accent-color) !important;
        }
        #pweLogotypes'. $slug_id .' .swiper-nav {
          display: flex;
          justify-content: center;
          margin-top: 2.5rem;
        }
        // #pweLogotypes'. $slug_id .' .swiper-dots .swiper-pagination-bullet {
        //   background: #27272a !important;
        //   opacity: 1 !important;
        //   width: 10px;
        //   height: 10px;
        //   margin: 0 5px !important;
        //   transition: all 0.3s ease;
        // }
        // #pweLogotypes'. $slug_id .' .swiper-dots .swiper-pagination-bullet-active {
        //   background: var(--accent-color) !important;
        //   transform: scale(1.2);
        // }
        @media(max-width:960px) {
          #pweLogotypes'. $slug_id .' .pwe-logotypes__items {
            padding: 0;
          }
        }
      </style>';

  $output .= '
  <div id="pweLogotypes'. $slug_id .'" class="pwe-logotypes '. $logotypes_slug .'">
      <div class="pwe-logotypes__wrapper">

          <div class="pwe-logotypes__title">
            <h4 class="pwe-main-title">'. esc_html($title) .'</h4>

            <div class="swiper-buttons-arrows">
                <div class="swiper-button-prev"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg></div>
                <div class="swiper-button-next"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></div>
            </div>
          </div>

          <div class="pwe-logotypes__items swiper">
              <div class="swiper-wrapper">';

                  foreach ($logotypes as $logo){
                      $name  = $logo['name'] ?? '';
                      $alt   = $logo['alt']  ?? $name;

                      if (empty($logo['link'])) {
                          $output .= '
                          <div class="swiper-slide">
                          <div class="pwe-logotypes__item">
                              <img src="'. esc_url($logo['url']) .'" alt="'. esc_attr($alt) .'">';
                              $output .= ($name ? '<p>'. esc_html($name) .'</p>' : '');
                              $output .= '
                          </div>
                          </div>';
                      } else {
                          $output .= '
                          <div class="swiper-slide">
                          <a class="pwe-logotypes__item" target="_blank" href="'. esc_url($logo['link']) .'">
                              <img src="'. esc_url($logo['url']) .'" alt="'. esc_attr($alt) .'">';
                              $output .= ($name ? '<p>'. esc_html($name) .'</p>' : '');
                              $output .= '
                          </a>
                          </div>';
                      }
                  }

              $output .= '
              </div>
          </div>

          <div class="swiper-nav">
            <div class="swiper-dots" aria-label="Slider navigation" role="tablist"></div>
          </div>

      </div>
  </div>';

  $output .= PWE_Swiper::swiperScripts('#pweLogotypes'. $slug_id, [0=>['slidesPerView'=>2],650=>['slidesPerView'=>3],960=>['slidesPerView'=>5]], true, true);
}

return $output;