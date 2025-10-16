<?php
if (!defined('ABSPATH')) exit;

$output .= '
<div id="exhibitorModal" class="exhibitor-modal">
  <div class="exhibitor-modal__heading"></div>
  <div class="exhibitor-modal__container">
    <div class="exhibitor-modal__content">

        <div class="exhibitor-modal__header">
            <div class="exhibitor-modal__logo">
                <img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/test.png" alt="">
            </div>
            <h3 class="exhibitor-modal__title">Oral-B</h3>
        </div>

        <div class="exhibitor-modal__description">
            <p class="exhibitor-modal__subtitle">Dlaczego warto odwiedzić nasze stoisko</p>
            <p class="exhibitor-modal__text">Opis skrócony placeholder.</p>
            <p class="exhibitor-modal__subtitle">Opis</p>
            <p class="exhibitor-modal__text">Pełny opis placeholder.</p>
        </div>

        <div class="exhibitor-modal__brands">
            <p class="exhibitor-modal__brands-title">Marki, które zaprezentujemy</p>
            <ul class="exhibitor-modal__brands-list">
                <li class="exhibitor-modal__brand">Przykładowa Marka</li>
                <li class="exhibitor-modal__brand">Inna Marka</li>
            </ul>
        </div>

        <div class="exhibitor-modal__separator"></div>

        <div class="exhibitor-modal__categories">
            <h3 class="exhibitor-modal__categories-title">Kategorie</h3>
            <div class="exhibitor-modal__category">
                <p class="exhibitor-modal__category-name">Hala</p>
                <div class="exhibitor-modal__category-list">
                    <div class="exhibitor-modal__category-value">Hala C</div>
                </div>
            </div>
        </div>

        <div class="exhibitor-modal__separator"></div>

        <div class="exhibitor-modal__products">
            <p class="exhibitor-modal__products-title">Produkty (2)</p>
            <div class="exhibitor-modal__products-list">
                <div class="exhibitor-modal__product">
                    <div class="exhibitor-modal__product-img"><img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/product.png" alt=""></div>
                    <p>Produkt 1</p>
                </div>
                <div class="exhibitor-modal__product">
                    <div class="exhibitor-modal__product-img"><img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/product2.png" alt=""></div>
                    <p>Produkt 2</p>
                </div>
            </div>
        </div>

        <div class="exhibitor-modal__separator"></div>

        <div class="exhibitor-modal__documents">
            <p class="exhibitor-modal__documents-title">Dokumenty (2)</p>
            <div class="exhibitor-modal__products-list">
                <div class="exhibitor-modal__documents-element">
                    <div class="exhibitor-modal__documents-element-container">
                        <p>Broszura</p>
                        <div class="exhibitor-catalog__documents-img">
                            <img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/document.png" alt="">
                        </div>
                    </div>
                    <p>Katalog Produktów</p>
                </div>
            </div>
        </div>
    </div>

    <div class="exhibitor-modal__sidebar">
        <div class="exhibitor-modal__stand">
            <img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/place.png" alt="">
            <span class="exhibitor-modal__stand-number">Stoisko F3.43</span>
        </div>
        <div class="exhibitor-modal__contacts">
            <p class="exhibitor-modal__contacts-title">Company contacts</p>
            <div class="exhibitor-modal__contact">
                <img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/world.png" alt="">
                <p class="exhibitor-modal__contact-text">Strona www</p>
            </div>
            <div class="exhibitor-modal__contact">
                <img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/email.png" alt="">
                <p class="exhibitor-modal__contact-text">Email</p>
            </div>
            <div class="exhibitor-modal__contact">
                <img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/phone.png" alt="">
                <p class="exhibitor-modal__contact-text">Telefon</p>
            </div>
        </div>
    </div>
  </div>
</div>
';

return $output;
