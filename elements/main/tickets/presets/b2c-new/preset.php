<?php
$output = '';

$output .= '
<section id="pweTickets" class="tickets-section">

    <div class="tickets-container">
        <div class="tickets-header">
            <span class="tickets-subtitle">'. PWE_Functions::multi_translation("join_us") .'</span>
            <h2 class="tickets-title">'. PWE_Functions::multi_translation("tickets") .'</h2>
        </div>

        <div class="tickets-grid">';

        foreach ($tiers as $tier) {
            $card_class = $tier['popular'] ? 'ticket-card ticket-card--popular' : 'ticket-card';

            $output .= '
            <div class="' . $card_class . '">';

                if ($tier['popular']) {
                    $output .= '<div class="ticket-badge">Bestseller</div>';
                }

                $output .= '
                <div class="ticket-header-block">
                    <h3 class="ticket-name">' . htmlspecialchars($tier['name']) . '</h3>
                    <div class="ticket-price-box">';

                        if (!empty($tier['sale_price']) && !empty($tier['price']) && $tier['sale_price'] !== $tier['price']) {
                            $output .= '<span class="ticket-price-old">' . htmlspecialchars($tier['price']) . '</span>';
                            $output .= '<span class="ticket-price ticket-price--sale">' . htmlspecialchars($tier['sale_price']) . '</span>';
                        } else {
                            $display_price = !empty($tier['sale_price']) ? $tier['sale_price'] : ($tier['price'] ?? '0');
                            $output .= '<span class="ticket-price">' . htmlspecialchars($display_price) . '</span>';
                        }

                        $output .= '
                        <span class="ticket-currency">' . htmlspecialchars($tier['currency']) . '</span>
                    </div>
                    <p class="ticket-desc">' . htmlspecialchars($tier['desc']) . '</p>
                </div>

                <div class="ticket-features">';

                if (!empty($tier['features']) && is_array($tier['features'])) {
                    foreach ($tier['features'] as $feature) {
                        $output .= '
                        <div class="ticket-feature-item">
                            <div class="ticket-icon-wrap">
                                <svg class="ticket-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <span class="ticket-feature-text">' . htmlspecialchars($feature) . '</span>
                        </div>';
                    }
                }

                $output .= '
                </div>

                <a href="' . esc_url($tier['button_url']) . '" target="_blank" class="ticket-button" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center; text-align: center;">' .
                    (!empty($tier['button_text']) ? htmlspecialchars($tier['button_text']) : PWE_Functions::multi_translation("tickets")) . '
                </a>
            </div>';
        }

        $output .= '
        </div>
    </div>
</section>';

return $output;