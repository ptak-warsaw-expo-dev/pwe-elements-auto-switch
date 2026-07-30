<?php

$report['status'] = false;

$new_url = str_replace('private_html','public_html',$_SERVER["DOCUMENT_ROOT"]) .'/wp-load.php';
if (file_exists($new_url)) {
    require_once($new_url);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['secret'] == SECURE_AUTH_KEY) {
        if (class_exists('GFAPI')) {
            $all_entries = array();
            $target_email = null;
            $entry_object = null;
            $target_lang = null;
            $target_phone = null;

            $post_data = str_replace("\\", "", $_POST['data']);

            $data_array = json_decode($post_data, true);
            $report['val'] = $data_array;

            foreach ($data_array['all_names'] as $index => $name){

                $entry = array();

                foreach ($data_array as $id => $val){
                    if ($target_email === null && filter_var($val, FILTER_VALIDATE_EMAIL)){
                        $target_email = $val;
                    }
                    if($target_lang === null && $id == 'channel'){
                        $target_lang = strpos(strtolower($val), 'eng') !== false ? 'en' : 'pl';
                    }

                    if($id == 'form_id'){
                        $entry[$id] = $val;
                        continue;
                    }

                    if($id == 'name_id'){
                        $field_id = str_replace('input_', '', $val);
                        $entry[$field_id] = $name;
                        continue;
                    }

                    if(strpos($id, 'input') !== false){
                        $field_id = str_replace('input_', '', $id);
                        $field_id = explode('.', $field_id)[0];

                        $entry[$field_id] = $val;
                        continue;
                    }

                }

                if(empty($data_array['channel'])){
                    $field_id = str_replace('input_', '', $data_array['kanal_id']);
                    $field_id = explode('.', $field_id)[0];

                    $entry[$field_id] = $data_array['getkanal'];
                }

                $phone_id = str_replace('input_', '', $data_array['phone_id']);
                $entry[$phone_id] = $data_array['phone'];
                $report['entry'] = $entry;

                $entry_id = GFAPI::add_entry($entry);
                if (!empty($entry_id)) {
                    $all_entries[] = $entry_id;
                }
            }

            foreach ($all_entries as $entry_id) {
                $form = GFAPI::get_form($data_array['form_id']);
                $entry_object = GFAPI::get_entry($entry_id);

                $notification_to_send = null;
                $channel_value = $data_array['channel'] ?? '';

                if ($channel_value === 'Platyna') {
                    $notification_to_send = 'Registration Platyna - PL';
                } elseif ($channel_value === 'Platyna EN') {
                    $notification_to_send = 'Registration Platyna - EN';
                } elseif (strpos($channel_value, ' EN') !== false) {
                    $notification_to_send = 'Registration CC - EN';
                } else {
                    $notification_to_send = 'Registration CC - PL';
                }

                $notifications_to_send = [];
                foreach ($form['notifications'] as $id => $notification) {
                    if (trim($notification['name']) === $notification_to_send) {
                        $notifications_to_send[] = $id;
                        break;
                    }
                }

                if (!empty($notifications_to_send)) {
                    $id_to_send = $notifications_to_send[0];
                    $notification = $form['notifications'][$id_to_send];

                    $original_active_state = $notification['isActive'];
                    $notification['isActive'] = true;

                    GFCommon::send_notification($notification, $form, $entry_object);

                    $notification['isActive'] = $original_active_state;
                } else {
                    $fallback_notification = null;

                    if ($channel_value === 'Platyna') {
                        $fallback_notification = 'Registration CC - PL';
                    } elseif ($channel_value === 'Platyna EN') {
                        $fallback_notification = 'Registration CC - EN';
                    }

                    if ($fallback_notification) {
                        foreach ($form['notifications'] as $id => $notification) {
                            if (trim($notification['name']) === $fallback_notification) {
                                $fallback_id = $id;

                                $original_active_state = $notification['isActive'];
                                $notification['isActive'] = true;

                                GFCommon::send_notification($notification, $form, $entry_object);

                                $notification['isActive'] = $original_active_state;

                                break;
                            }
                        }
                    }

                    $subject = "Brak powiadomienia: {$notification_to_send}";
                    $message = "Nie znaleziono powiadomienia '{$notification_to_send}' w formularzu ID {$data_array['form_id']}.\n" .
                            "Kanał: {$channel_value}\n" .
                            "Entry ID: {$entry_id}\n" .
                            "Zostało wysłane powiadomienie zastępcze: " . ($fallback_notification ?? 'brak') . "\n\n" .
                            "Proszę sprawdzić ustawienia w Gravity Forms.";

                    wp_mail(
                        'jakub.chola@warsawexpo.eu',
                        $subject,
                        $message,
                        ['Content-Type: text/plain; charset=UTF-8']
                    );
                }


                if (!empty($entry_id)){
                    wp_remote_post(home_url('wp-content/plugins/custom-element/action_handler.php'), [
                        'body' => [
                            'element' => 'gform_after_submission',
                            'entry_id' => $entry_id,
                            'url' => null
                        ],
                        'timeout' => 0.01,
                        'blocking' => false,
                    ]);
                }
            }

            $report['entries'] = $all_entries;
            $report['status'] = true;
            echo json_encode($report, true);
        }
    }
}
