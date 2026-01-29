<?php

$output = '
<div id="pweCombinedEvents" class="pwe-combined-events">
    <div class="pwe-combined-events__wrapper">

    <div class="pwe-combined-events__logos">
        <div class="pwe-combined-events__logo main">
            <img src="https://'. $main_event . (PWE_Functions::lang_pl() ? '/doc/logo-color.webp' : '/doc/logo-color-en.webp') .'" alt="Partner Logo">
        </div>
        <div class="pwe-combined-events__logo current">
            <img src="https://'. $current_event . (PWE_Functions::lang_pl() ? '/doc/logo-color.webp' : '/doc/logo-color-en.webp') .'" alt="Trade Fair Logo">
        </div>
    </div>

    <div class="pwe-combined-events__headings">
      <h2>'. (PWE_Functions::lang_pl() ? do_shortcode('[pwe_name_pl domain="'. $main_event .'"]') .' wspiera targi '. do_shortcode('[pwe_name_pl domain="'. $current_event .'"]') : do_shortcode('[pwe_name_pl domain="'. $main_event .'"]') .' supports the '.do_shortcode('[pwe_name_pl domain="'. $current_event .'"]')) .'</h2>
      <h3>'. (PWE_Functions::lang_pl() ? 'Zapraszamy na wspólne wydarzenie' : 'We invite you to a joint event') .'</h3>
    </div>

    </div>
</div>';    

return $output;