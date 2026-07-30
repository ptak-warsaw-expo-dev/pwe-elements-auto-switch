<?php
if (!defined('ABSPATH')) exit;

$output = '
<div class="pwe-exhibitor-worker-generator exhibitor-generator '. $form_id .'">
    <div class="exhibitor-generator__wrapper">
        <div class="exhibitor-generator__left">
            <div class="exhibitor-generator__left-wrapper">
                <h3>' . PWECommonFunctions::languageChecker('WYGENERUJ<br>IDENTYFIKATOR DLA<br>SIEBIE I OBSLUGI STOISKA', 'GENERATE</br>A VIP INVITATION</br>FOR YOUR GUESTS!') . '</h3>
            </div>
        </div>
        <div class="exhibitor-generator__right">
            <div class="exhibitor-generator__right-wrapper">
                <div class="exhibitor-generator__right-title">
                    <h3>' . PWECommonFunctions::languageChecker('WYGENERUJ<br>IDENTYFIKATOR DLA<br>SIEBIE I OBSLUGI STOISKA', 'GENERATE</br>A VIP INVITATION</br>FOR YOUR GUESTS!') . '</h3>
                </div>
                <div class="exhibitor-generator__right-form">
                    ' . $gravity_form . '
                </div>';

                $output .= '
            </div>
        </div>
    </div>
</div>';

return $output;
