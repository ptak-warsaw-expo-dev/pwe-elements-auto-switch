# Email Validation

This module adds enhanced email address validation to Gravity Forms.

Validation is enabled only for email fields marked with the CSS class:

`pwe-email-validate`

This allows the module to be used only where extended email verification is required, without affecting other email fields on the website.

The module uses native PHP validation functions.

First, the email address syntax is checked with:

`FILTER_VALIDATE_EMAIL`

The module also checks common domain typing mistakes and can suggest the correct domain when a known typo is detected.

After syntax validation, the domain part of the address is verified using PHP DNS checks with:

`checkdnsrr()`

The validator checks for `MX` records and uses an `A` record as a fallback.

For example:

`contact@warsawexpo.eu`

is checked against the DNS configuration of:

`warsawexpo.eu`

while an address such as:

`contact@warsawexpo.e`

is rejected if the domain has no valid DNS records.

DNS validation confirms that the domain exists and is configured in DNS, but it does not confirm whether the specific mailbox exists.
