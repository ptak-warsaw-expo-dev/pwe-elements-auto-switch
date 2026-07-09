<?php

$output = '
<div id="pweOrganizedGroups" class="pwe-organized-groups">

    <h4>'. PWE_Functions::multi_translation("title") . '</h4>

    <p>'. PWE_Functions::multi_translation("description") . '</p>

    <a class="pwe-organized-groups__btn" href="'. PWE_Functions::multi_translation("button_url") . '" target="_blank">'. PWE_Functions::multi_translation("button_text") . '</a>

</div>';

return $output;
