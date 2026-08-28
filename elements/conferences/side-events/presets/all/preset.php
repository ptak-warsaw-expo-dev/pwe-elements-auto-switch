<?php

$output = '
<div id="pweSideEvents" class="pwe-side-events">

    <div class="pwe-side-events__header">
        <h2 class="pwe-side-events__header-title">' . PWE_Functions::multi_translation('title') . '</h2>
    </div>


    <!-- Medals -->
    <div class="pwe-side-events__section">
        <div class="pwe-side-events__wrapper">

            <div class="pwe-side-events__visual">
                <div class="pwe-side-events__image-wrapper">
                    <img
                        class="pwe-side-events__image"
                        src="' . $medal_img . '"
                        alt="Ceremonia wręczenia medali i dyplomów"
                    >
                </div>

                <div class="pwe-side-events__badge pwe-side-events__badge--right">
                    <span class="pwe-side-events__badge-icon">✦</span>
                    <span>
                        <strong>' . PWE_Functions::multi_translation('medals_badge_title') . '</strong>
                        ' . PWE_Functions::multi_translation('medals_badge_description') . '
                    </span>
                </div>
            </div>

            <div class="pwe-side-events__content">
                <div class="pwe-side-events__content-header">

                    <div class="pwe-side-events__label">
                        <div class="pwe-side-events__label-icon-wrapper"><span class="pwe-side-events__label-icon">✦</span></div>
                        ' . PWE_Functions::multi_translation('medals_label') . '
                    </div>

                    <h3 class="pwe-side-events__title">
                        ' . PWE_Functions::multi_translation('medals_title') . '
                        <span class="pwe-side-events__highlight">
                            ' . PWE_Functions::multi_translation('medals_title_highlight') . '
                        </span>
                    </h3>

                    <div class="pwe-side-events__divider"></div>

                    <p class="pwe-side-events__text">
                        ' . PWE_Functions::multi_translation('medals_description') . '
                    </p>

                </div>

                <div class="pwe-side-events__features pwe-side-events__features--2">';

                    foreach ($categories as $index => $category) {
                        $number = str_pad($index + 1, 2, '0', STR_PAD_LEFT);

                        $output .= '
                        <div class="pwe-side-events__feature">
                            <span class="pwe-side-events__feature-number">' . $number . '</span>

                            <div>
                                <h4 class="pwe-side-events__feature-title">
                                    ' . PWE_Functions::multi_translation($category['title']) . '
                                </h4>

                                <p class="pwe-side-events__feature-text">
                                    ' . PWE_Functions::multi_translation($category['description']) . '
                                </p>
                            </div>
                        </div>';
                    }

                $output .= '
                </div>
            </div>

        </div>
    </div>


    <!-- Studio -->
    <div class="pwe-side-events__section">
        <div class="pwe-side-events__wrapper pwe-side-events__wrapper--reverse">

            <div class="pwe-side-events__visual">
                <div class="pwe-side-events__image-wrapper">
                    <img
                        class="pwe-side-events__image"
                        src="/wp-content/plugins/pwe-media/media/conferences/side-events/studio.webp"
                        alt="Studio targowe"
                    >
                </div>

                <div class="pwe-side-events__badge pwe-side-events__badge--left">
                    <span class="pwe-side-events__badge-icon">●</span>
                    <span>
                        <strong>' . PWE_Functions::multi_translation('studio_badge_title') . '</strong>
                        ' . PWE_Functions::multi_translation('studio_badge_desc') . '
                    </span>
                </div>
            </div>

            <div class="pwe-side-events__content">

                <div class="pwe-side-events__label">
                    <div class="pwe-side-events__label-icon-wrapper"><span class="pwe-side-events__label-icon">✦</span></div>
                    ' . PWE_Functions::multi_translation('studio_label') . '
                </div>

                <h3 class="pwe-side-events__title">
                    ' . PWE_Functions::multi_translation('studio_title') . '
                    <span class="pwe-side-events__highlight">
                        ' . PWE_Functions::multi_translation('studio_title_highlight') . '
                    </span>
                </h3>

                <div class="pwe-side-events__divider"></div>

                <p class="pwe-side-events__text">
                    ' . PWE_Functions::multi_translation('studio_description_1') . '
                </p>

                <p class="pwe-side-events__text">
                    ' . PWE_Functions::multi_translation('studio_description_2') . '
                </p>

                <div class="pwe-side-events__features">

                    <div class="pwe-side-events__feature">
                        <span class="pwe-side-events__feature-number">01</span>
                        <div>
                            <h4 class="pwe-side-events__feature-title">' . PWE_Functions::multi_translation('studio_1_title') . '</h4>
                            <p class="pwe-side-events__feature-text">
                                ' . PWE_Functions::multi_translation('studio_1_description') . '
                            </p>
                        </div>
                    </div>

                    <div class="pwe-side-events__feature">
                        <span class="pwe-side-events__feature-number">02</span>
                        <div>
                            <h4 class="pwe-side-events__feature-title">' . PWE_Functions::multi_translation('studio_2_title') . '</h4>
                            <p class="pwe-side-events__feature-text">
                                ' . PWE_Functions::multi_translation('studio_2_description') . '
                            </p>
                        </div>
                    </div>

                    <div class="pwe-side-events__feature">
                        <span class="pwe-side-events__feature-number">03</span>
                        <div>
                            <h4 class="pwe-side-events__feature-title">' . PWE_Functions::multi_translation('studio_3_title') . '</h4>
                            <p class="pwe-side-events__feature-text">
                                ' . PWE_Functions::multi_translation('studio_3_description') . '
                            </p>
                        </div>
                    </div>

                </div>

                <p class="pwe-side-events__lead">
                    ' . PWE_Functions::multi_translation('studio_footer_text') . '
                </p>

            </div>

        </div>
    </div>


    <!-- Networking -->
    <div class="pwe-side-events__section">
        <div class="pwe-side-events__wrapper">

            <div class="pwe-side-events__visual">
                <div class="pwe-side-events__image-wrapper">
                    <img
                        class="pwe-side-events__image"
                        src="/wp-content/plugins/pwe-media/media/conferences/side-events/networking.webp"
                        alt="Strefa networkingu B2B"
                    >
                </div>

                <div class="pwe-side-events__badge pwe-side-events__badge--right">
                    <span class="pwe-side-events__badge-icon">B2B</span>
                    <span>
                        <strong>' . PWE_Functions::multi_translation('networking_badge_title') . '</strong>
                        ' . PWE_Functions::multi_translation('networking_badge_desc') . '
                    </span>
                </div>
            </div>

            <div class="pwe-side-events__content">

                <div class="pwe-side-events__label">
                    <div class="pwe-side-events__label-icon-wrapper"><span class="pwe-side-events__label-icon">✦</span></div>
                    ' . PWE_Functions::multi_translation('networking_label') . '
                </div>

                <h3 class="pwe-side-events__title">
                    ' . PWE_Functions::multi_translation('networking_title') . '
                    <span class="pwe-side-events__highlight">
                        ' . PWE_Functions::multi_translation('networking_title_highlight') . '
                    </span>
                </h3>

                <div class="pwe-side-events__divider"></div>

                <p class="pwe-side-events__text">
                    ' . PWE_Functions::multi_translation('networking_description_1') . '
                </p>

                <p class="pwe-side-events__text">
                    ' . PWE_Functions::multi_translation('networking_description_2') . '
                </p>

                <div class="pwe-side-events__features">

                    <div class="pwe-side-events__feature">
                        <span class="pwe-side-events__feature-number">01</span>
                        <div>
                            <h4 class="pwe-side-events__feature-title">' . PWE_Functions::multi_translation('networking_1_title') . '</h4>
                            <p class="pwe-side-events__feature-text">
                                ' . PWE_Functions::multi_translation('networking_1_description') . '
                            </p>
                        </div>
                    </div>

                    <div class="pwe-side-events__feature">
                        <span class="pwe-side-events__feature-number">02</span>
                        <div>
                            <h4 class="pwe-side-events__feature-title">' . PWE_Functions::multi_translation('networking_2_title') . '</h4>
                            <p class="pwe-side-events__feature-text">
                                ' . PWE_Functions::multi_translation('networking_2_description') . '
                            </p>
                        </div>
                    </div>

                    <div class="pwe-side-events__feature">
                        <span class="pwe-side-events__feature-number">03</span>
                        <div>
                            <h4 class="pwe-side-events__feature-title">' . PWE_Functions::multi_translation('networking_3_title') . '</h4>
                            <p class="pwe-side-events__feature-text">
                                ' . PWE_Functions::multi_translation('networking_3_description') . '
                            </p>
                        </div>
                    </div>

                </div>

                <p class="pwe-side-events__lead">
                    ' . PWE_Functions::multi_translation('networking_footer_text') . '
                </p>

            </div>

        </div>
    </div>';

    if (PWE_Functions::lang_pl()) {
        $output .= '
        <!-- CTA -->
        <div class="pwe-side-events__cta">

            <div class="pwe-side-events__cta-content">

                <div class="pwe-side-events__label">
                    <div class="pwe-side-events__label-icon-wrapper"><span class="pwe-side-events__label-icon">✦</span></div>
                    ' . PWE_Functions::multi_translation('cta_label') . '
                </div>

                <h3 class="pwe-side-events__cta-title">
                    ' . PWE_Functions::multi_translation('cta_title') . '
                    <span class="pwe-side-events__highlight">
                        ' . PWE_Functions::multi_translation('cta_title_highlight') . '
                    </span>
                </h3>

                <p class="pwe-side-events__text">
                    ' . PWE_Functions::multi_translation('cta_description') . '
                </p>

            </div>

            <a class="pwe-side-events__button" href="' . PWE_Functions::multi_translation('cta_button_url') . '" target="_blank">
                <span>' . PWE_Functions::multi_translation('cta_button_text') . '</span>
                <span class="pwe-side-events__button-icon">↓</span>
            </a>

        </div>';
    }

$output .= '
</div>
';

return $output;