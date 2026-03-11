<?php



$rawDomains = PWECommonFunctions::get_database_week_data();

if (is_string($rawDomains)) {
    $rawDomains = json_decode($rawDomains, true);
}
if (!is_array($rawDomains)) {
    $rawDomains = [];
}

$domains = [];
foreach ($rawDomains as $domain) {
    $domain = trim($domain, " \t\n\r\0\x0B[]\"");
    if ($domain !== '') {
        $domains[] = $domain;
    }
}

$domainMap = array_flip($domains);

$fair_data = PWECommonFunctions::get_database_fairs_data();
if (!is_array($fair_data)) {
    $fair_data = [];
}

$filtered_fairs = array_values(array_filter($fair_data, function ($item) use ($domainMap) {
    return isset($item->fair_domain)
        && isset($domainMap[$item->fair_domain]);
}));

$fairsByDomain = [];

foreach ($filtered_fairs as $fair) {
    if (!empty($fair->fair_domain)) {
        $fairsByDomain[$fair->fair_domain] = $fair;
    }
}

$priorityDomains = [
    'warsawhome.eu'
];

$domains = array_values(array_unique($domains));

$priority = [];
$rest = [];

foreach ($domains as $domain) {
    if (in_array($domain, $priorityDomains, true)) {
        $priority[] = $domain;
    } else {
        $rest[] = $domain;
    }
}

$domains = array_merge($priority, $rest);


$output .= '<section class="carousel">

  <div class="head">
    <h2>Boost your professional workflow and productivity</h2>
    <p>'. $trade_fair_date .'</p>
    <div class="controls">
      <button class="nav-btn prev">‹</button>
      <button id="next" class="nav-btn next">›</button>
    </div>
  </div>

  <div class="carousel-viewport">';
  $output .= '<div class="carousel-track">';

  foreach ($domains as $index => $domain) {

      $bg   = 'https://' . $domain . '/doc/background.webp';
      $logo = 'https://' . $domain . '/doc/logo-x-pl.webp';

      $desc = '';
      if (isset($fairsByDomain[$domain])) {
          $desc = $fairsByDomain[$domain]->fair_name_pl;
      }

      // tylko pierwszy slide
      $weekLabel = '';
      if ($index === 0) {
          $weekLabel = '<div class="carousel-week">Tydzień targowy</div>';
      }

      $output .= '
          <article class="carousel-slide" style="background-image:url(' . esc_url($bg) . ')">
              ' . $weekLabel . '
              <div class="carousel-desc">
                  ' . esc_html($desc) . '
              </div>
              <img src="' . esc_url($logo) . '" alt="logo">
          </article>
      ';
  }


  $output .= '</div>

  </div>

</section>



<script>
(() => {
  const slides = document.querySelectorAll(".carousel-slide");
  const prev = document.querySelector(".prev");
  const next = document.querySelector(".next");

  let index = 0;
  const total = slides.length;

  function render() {
    slides.forEach(slide =>
      slide.classList.remove("active", "next", "next2")
    );

    slides[index].classList.add("active");
    slides[(index + 1) % total].classList.add("next");
    slides[(index + 2) % total].classList.add("next2");
  }

  next.addEventListener("click", () => {
    index = (index + 1) % total;
    render();
  });

  prev.addEventListener("click", () => {
    index = (index - 1 + total) % total;
    render();
  });

  render();
})();
const slides = document.querySelectorAll(".carousel-slide");
const nextBtn = document.getElementById("next");

slides.forEach(slide => {
  slide.addEventListener("click", (e) => {
    // zapobiega konfliktom (np. play button, linki)
    if (e.target.closest("button, a")) return;

    nextBtn.click();
  });
});
</script>


';


// $output .= PWE_Swiper::swiperScripts(
//     '#hero-slider',
//     [
//         0 => ['slidesPerView' => 1],
//     ],
//     false,
//     true,
//     1,
//     false,
//     18
// );

return $output;
