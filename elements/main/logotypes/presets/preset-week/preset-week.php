<?php

if ($logotypes_slug === 'patrons-partners') {

  // Helper for slugs
  function pwe_slugify($text){
      if (function_exists('sanitize_title')) return sanitize_title($text);
      $text = iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$text);
      $text = strtolower($text);
      $text = preg_replace('~[^a-z0-9]+~','-',$text);
      return trim($text,'-') ?: 'inne';
  }

  // Categories dynamically from desc_pl / desc_en
  $cats = [];          // ['slug' => ['label' => 'Partner Merytoryczny', 'count' => 7]]
  $total = count($logotypes);

  foreach ($logotypes as $it){
      $label = trim($it['desc_pl'] ?? $it['desc_en'] ?? '');
      if ($label === '') $label = 'Inne';
      $slug  = pwe_slugify($label);
      if (!isset($cats[$slug])) $cats[$slug] = ['label'=>$label,'count'=>0];
      $cats[$slug]['count']++;
  }

  $output = '
  <style>
    #pweLogotypes'. $slug_id .' {
      padding: 18px 0;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__title {
      display: flex;
      justify-content: space-between;
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
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__filter {
      padding: 12px;
      border-radius:8px;
      background: var(--background-color);
      cursor:pointer;
      font-weight:600;
      line-height:1;
      display: inline-flex;
      align-items: center;
      text-align: left;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__filter.is-active {
      background: var(--accent-color);
      color: white;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__filter-other .pwe-logotypes__filter {
      font-size: 12px;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__filters span {
      font-size: 14px;
      background: white;
      padding: 4px;
      border-radius: 4px;
      margin-left: 6px;
      color: black;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__items {
      width:100%;
      margin-top:18px;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__item {
      background:#fff;
      border:1px solid #c8c8c8;
      border-radius:10px;
      display:flex;
      flex-direction:column;
      justify-content:center;
      align-items:center;
      aspect-ratio:3/2;
      text-decoration:none;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__item img {
      aspect-ratio: 3 / 1.6;
      flex: 1;
      object-fit: contain;
      padding:10px;
      max-width:100%;
      height:auto;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__item p {
      text-transform:uppercase;
      font-size:12px;
      font-weight:700;
      color:#000;
      white-space:break-spaces;
      text-align:center;
      line-height:1.1;
      margin:5px;
      flex-shrink: 0;
    }
    @media(max-width:960px) {
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
        grid-template-columns: 1fr 1fr 1fr;
      }
    }
    @media(max-width:450px) {
      #pweLogotypes'. $slug_id .' .pwe-logotypes__filters {
        gap: 8px;
      }
      #pweLogotypes'. $slug_id .' .pwe-logotypes__filter-other .slick-list::after {
        width: 100px;
      }
    }

  </style>';

  $output .= '
  <div id="pweLogotypes'. $slug_id .'" class="pwe-logotypes '. $logotypes_slug .'">
      <div class="pwe-logotypes__wrapper">

          <div class="pwe-logotypes__title">
            <h4 class="pwe-main-title">'. esc_html($title) .'</h4>
            <div class="swiper-buttons-arrows">
                <div class="swiper-button-prev">⏴</div>
                <div class="swiper-button-next">⏵</div>
            </div>
          </div>

          <div class="pwe-logotypes__buttons">
              <div class="pwe-logotypes__filters" role="tablist" aria-label="Filtry logotypów">

                  <div class="pwe-logotypes__filter-all">
                    <button class="pwe-logotypes__filter is-active" data-filter="all">
                        Wszyscy <span>'. intval($total) .'</span>
                    </button>
                    <div class="swiper-buttons-arrows">
                      <div class="swiper-button-prev">⏴</div>
                      <div class="swiper-button-next">⏵</div>
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
                      $label = trim($logo['desc_pl'] ?? $logo['desc_en'] ?? 'Inne');
                      $cat   = pwe_slugify($label);

                      $name  = $logo['name'] ?? '';
                      $alt   = $logo['alt']  ?? $name;

                      if (empty($logo['link'])) {
                          $output .= '
                          <div class="swiper-slide" data-cat="'. esc_attr($cat) .'">
                          <div class="pwe-logotypes__item">
                              <img src="'. esc_url($logo['url']) .'" alt="'. esc_attr($alt) .'">';
                              $output .= ($name ? '<p>'. esc_html($name) .'</p>' : '');
                              $output .= '
                          </div>
                          </div>';
                      } else {
                          $output .= '
                          <div class="swiper-slide" data-cat="'. esc_attr($cat) .'">
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

  $output .= PWE_Swiper::swiperScripts('#pweLogotypes'. $slug_id, [0=>['slidesPerView'=>2],650=>['slidesPerView'=>3],960=>['slidesPerView'=>5]], true, true, 3);

  $logos_data = array_map(function($l){
      $label = trim($l['desc_pl'] ?? $l['desc_en'] ?? 'Inne');
      $slug  = function_exists('sanitize_title') ? sanitize_title($label)
                                                : strtolower(trim(preg_replace('~[^a-z0-9]+~i','-',$label),'-'));
      return [
          'cat'  => $slug,
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
    #pweLogotypes'. $slug_id .' .pwe-logotypes__title {
      display: flex;
      justify-content: space-between;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__items {
      margin-top: 18px;
      background: var(--background-color);
      padding: 18px;
      border-radius: 18px;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__item {
      background: #fff;
      border-radius: 10px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-decoration: none;
      overflow: hidden;
      aspect-ratio: 3/2;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__item img {
      aspect-ratio: 3/1.6;
      object-fit: contain;
      padding: 10px;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__item p {
      text-transform:uppercase;
      font-size: 12px;
      font-weight: 700;
      color: #fff;
      white-space: break-spaces;
      text-align: center;
      line-height: 1.1;
      margin: 0;
      width: 100%;
      background: var(--accent-color);
      padding: 8px;
    }
    @media(max-width:960px) {
      #pweLogotypes'. $slug_id .' .pwe-logotypes__items {
        padding: 10px;
      }
    }
  </style>';

  $output .= '
  <div id="pweLogotypes'. $slug_id .'" class="pwe-logotypes '. $logotypes_slug .'">
      <div class="pwe-logotypes__wrapper">

          <div class="pwe-logotypes__title">
            <h4 class="pwe-main-title">'. esc_html($title) .'</h4>

            <div class="swiper-buttons-arrows">
              <div class="swiper-button-prev">⏴</div>
              <div class="swiper-button-next">⏵</div>
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
