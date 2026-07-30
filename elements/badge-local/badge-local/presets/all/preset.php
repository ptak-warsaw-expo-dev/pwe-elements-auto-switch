<?php

$output = '
<div id="pweBadgeLocal" class="pwe-badge-local">
    <div class="pwe-badge-local__wrapper">
        <div class="pwe-badge-local__form">
            ' . do_shortcode('[gravityform id="'. $form_id .'" title="false" description="false" ajax="false"]') . '
            ';
        

            // 4. Cheking if $_GET parametr=masowy.
            if (isset($_GET['parametr']) && $_GET['parametr'] == 'masowy') {
                if (isset($_POST["gform_submit"]) && isset($_GET['qrcode']) && $_GET['qrcode'] == 'only'){                
                    Badge_Local::qrOnlyDownload($form_id);
                } else {
                    // 5. Adding input for multi use same data and new function for mass form.
                    Badge_Local::massGenerator($form_id);
                }
            
                $output .= '<script>
                    jQuery(function ($) {
                        const gfWraper = $("#gform_wrapper_' . $form_id . '");
                        const gfFields = gfWraper.find(".gform_fields");
                        const multiInput = $("<input>", {
                            placeholder: "ilość identyfikatorów",
                            type: "text",
                            id: "multi_send",
                            name: "multi_send"
                        });
                        gfFields.append(multiInput);
                    });
                </script>';
            }
        $output .= '
        </div>
    </div>
</div>';

return $output;