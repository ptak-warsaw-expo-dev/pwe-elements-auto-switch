# Phone Validation

This module adds enhanced phone number validation to Gravity Forms.

Validation is enabled only for phone fields marked with the dedicated CSS class, so it can be applied selectively to specific forms and fields without affecting other phone inputs on the website.

The module uses the `intl-tel-input` JavaScript library to handle international phone numbers, country selection, dialing codes and number formatting.

It verifies whether the entered phone number is valid for the selected country before allowing the form to be submitted.

If the number is invalid, the field is rejected and a validation message is displayed to the user.

The module is designed to provide more reliable phone validation than standard browser or form-level checks, especially for international numbers.