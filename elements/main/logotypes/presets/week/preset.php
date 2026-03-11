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
    .pwe-logotypes {
        margin-top: 48px;
    }

    /* GRID */
    .pwe-logotypes__grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 24px;
    }

    @media (max-width: 1200px) {
        .pwe-logotypes__grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 768px) {
        .pwe-logotypes__grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* ITEM */
    .pwe-logotype {
        text-align: center;
        padding: 16px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
    }

    .pwe-logotype.is-hidden {
        display: none;
    }

    .pwe-logotype img {
        max-width: 100%;
        max-height: 80px;
        object-fit: contain;
        margin-bottom: 10px;
    }

    .pwe-logotype span {
        display: block;
        font-size: 12px;
        color: #6b7280;
    }

    /* LINK */
    .pwe-logotype a {
        display: block;
        color: inherit;
        text-decoration: none;
    }

    /* BUTTON */
    .pwe-logotypes__more {
        margin: 32px auto 0;
        display: block;
        background: #1f2937;
        color: #fff;
        border: 0;
        padding: 12px 20px;
        border-radius: 8px;
        cursor: pointer;
    }

  </style>';

  $output .= '
  <div id="pweLogotypes'. $slug_id .'" class="pwe-logotypes '. $logotypes_slug .'">';

  $output .= '<div class="pwe-logotypes">';

  $output .= '
      <div class="pwe-logotypes__grid">
  ';

  foreach ($logotypes as $index => $logo) {

      $hidden = $index >= 18 ? ' is-hidden' : '';

      $img = $logo['url'];
      $alt = $logo['alt'] ?: $logo['desc_pl'];
      $desc = $logo['desc_pl'];
      $link = $logo['link'];

      $output .= '<div class="pwe-logotype'.$hidden.'">';

      if (!empty($link)) {
          $output .= '<a href="'.$link.'" target="_blank" rel="noopener">';
      }

      $output .= '
              <img src="'.$img.'" alt="'.$alt.'">

      ';

      if (!empty($link)) {
          $output .= '</a>';
      }

      $output .= '</div>';
  }

  $output .= '
      </div>

      <button class="pwe-logotypes__more">
          Pokaż więcej ↓
      </button>
  </div>';



  $output .='
  </div>';



  $output .= <<<JS
  <script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.querySelector('.pwe-logotypes__more');
        const hidden = document.querySelectorAll('.pwe-logotype.is-hidden');

        if (!btn || hidden.length === 0) return;

        btn.addEventListener('click', () => {
            hidden.forEach(el => el.classList.remove('is-hidden'));
            btn.style.display = 'none';
        });
    });
  </script>
  JS;

} else if ($logotypes_slug === 'patrons-partners-international') {
  $output = '
  <style>

  </style>';

  $output .= '
  <div id="pweLogotypes'. $slug_id .'" class="pwe-logotypes '. $logotypes_slug .'">

  </div>';

}

return $output;
