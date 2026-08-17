<?php
/**
 * PWE Email Validator addon.
 *
 * Add in the main plugin file:
 * require_once plugin_dir_path( __FILE__ ) . 'addons/email-validator/email-validator.php';
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class PWE_Email_Validator_Addon {
	private const VERSION = '1.5.0';

	private static bool $initialized = false;
	private static bool $assets_loaded = false;

	public static function init(): void {
		if ( self::$initialized ) {
			return;
		}

		self::$initialized = true;

		add_filter( 'gform_field_validation', [ self::class, 'validate_email_domain' ], 10, 4 );
		self::enqueue_assets();
	}

	public static function enqueue_assets(): void {
		if ( self::$assets_loaded || is_admin() ) {
			return;
		}

		self::$assets_loaded = true;

		$base_path = plugin_dir_path( __FILE__ );
		$base_url  = plugin_dir_url( __FILE__ );

		wp_enqueue_style(
			'pwe-email-validator',
			$base_url . 'assets/css/email-validator.css',
			[],
			file_exists( $base_path . 'assets/css/email-validator.css' )
				? (string) filemtime( $base_path . 'assets/css/email-validator.css' )
				: self::VERSION
		);

		wp_enqueue_script(
			'pwe-email-validator',
			$base_url . 'assets/js/email-validator.js',
			[],
			file_exists( $base_path . 'assets/js/email-validator.js' )
				? (string) filemtime( $base_path . 'assets/js/email-validator.js' )
				: self::VERSION,
			true
		);

		wp_localize_script(
			'pwe-email-validator',
			'PWEEmailValidatorSettings',
			[
				'corrections' => self::get_domain_corrections(),
				'providers'   => self::get_provider_domains(),
				'messages'    => self::get_messages(),
			]
		);

		self::print_late_styles( [ 'pwe-email-validator' ] );
	}

	/** @param string[] $handles */
	private static function print_late_styles( array $handles ): void {
		if ( ! did_action( 'wp_head' ) || ! function_exists( 'wp_print_styles' ) ) {
			return;
		}

		$pending = array_values( array_filter( $handles, static function ( $handle ) {
			return ! wp_style_is( $handle, 'done' );
		} ) );

		if ( $pending ) {
			wp_print_styles( $pending );
		}
	}

	/**
	 * Exact corrections are deliberate. This prevents a company domain from
	 * being transformed into an unrelated public mailbox provider.
	 *
	 * @return array<string,string>
	 */
	private static function get_domain_corrections(): array {
		$corrections = [
			// Gmail.
			'gmail.'       => 'gmail.com',
			'gmail.c'      => 'gmail.com',
			'gmail.co'     => 'gmail.com',
			'gmail.con'    => 'gmail.com',
			'gmail.cm'     => 'gmail.com',
			'gmail.cim'    => 'gmail.com',
			'gmail.om'     => 'gmail.com',
			'gmail.comm'   => 'gmail.com',
			'gmail.pl'     => 'gmail.com',
			'gmail.lt'     => 'gmail.com',
			'gmai.com'     => 'gmail.com',
			'gmai.co'      => 'gmail.com',
			'gmial.com'    => 'gmail.com',
			'gimal.com'    => 'gmail.com',
			'gamil.com'    => 'gmail.com',
			'gmaill.com'   => 'gmail.com',
			'gmal.com'     => 'gmail.com',
			'gnail.com'    => 'gmail.com',
			'gmali.com'    => 'gmail.com',
			'gmeil.com'    => 'gmail.com',

			// Outlook / Hotmail.
			'outlook.'     => 'outlook.com',
			'outlook.co'   => 'outlook.com',
			'outlook.con'  => 'outlook.com',
			'outlook.cm'   => 'outlook.com',
			'outlok.com'   => 'outlook.com',
			'outloo.com'   => 'outlook.com',
			'outllok.com'  => 'outlook.com',
			'hotmail.'     => 'hotmail.com',
			'hotmail.co'   => 'hotmail.com',
			'hotmail.con'  => 'hotmail.com',
			'hotmail.cm'   => 'hotmail.com',
			'hotmal.com'   => 'hotmail.com',
			'hotmai.com'   => 'hotmail.com',
			'hotmil.com'   => 'hotmail.com',
			'hotmial.com'  => 'hotmail.com',

			// Apple.
			'icloud.'      => 'icloud.com',
			'icloud.co'    => 'icloud.com',
			'icloud.con'   => 'icloud.com',
			'iclod.com'    => 'icloud.com',
			'icoud.com'    => 'icloud.com',
			'icloid.com'   => 'icloud.com',

			// Yahoo.
			'yahoo.'       => 'yahoo.com',
			'yahoo.co'     => 'yahoo.com',
			'yahoo.con'    => 'yahoo.com',
			'yahoo.cm'     => 'yahoo.com',
			'yaho.com'     => 'yahoo.com',
			'yahooo.com'   => 'yahoo.com',
			'yaoo.com'     => 'yahoo.com',
			'yhoo.com'     => 'yahoo.com',

			// Proton.
			'proton.me.'   => 'proton.me',
			'proton.ne'    => 'proton.me',
			'protonmail.'  => 'protonmail.com',
			'protonmail.co'=> 'protonmail.com',
			'protonmail.con'=> 'protonmail.com',
			'protonmai.com'=> 'protonmail.com',

			// Popular Polish providers.
			'wp.p'         => 'wp.pl',
			'wpp.pl'       => 'wp.pl',
			'o2.p'         => 'o2.pl',
			'02.pl'        => 'o2.pl',
			'onet.p'       => 'onet.pl',
			'onte.pl'      => 'onet.pl',
			'one.pl'       => 'onet.pl',
			'interia.p'    => 'interia.pl',
			'intera.pl'    => 'interia.pl',
		];

		return apply_filters( 'pwe_email_validator_domain_corrections', $corrections );
	}

	/** @return string[] */
	private static function get_provider_domains(): array {
		return apply_filters(
			'pwe_email_validator_provider_domains',
			[
				'gmail.com', 'googlemail.com', 'outlook.com', 'outlook.pl',
				'hotmail.com', 'hotmail.co.uk', 'live.com', 'icloud.com',
				'me.com', 'mac.com', 'yahoo.com', 'yahoo.pl', 'yahoo.co.uk',
				'proton.me', 'protonmail.com', 'pm.me', 'wp.pl', 'o2.pl',
				'onet.pl', 'op.pl', 'interia.pl', 'interia.eu', 'poczta.fm',
				'gmx.de', 'gmx.net', 'web.de', 'seznam.cz', 'centrum.cz',
				'azet.sk', 'libero.it', 'orange.fr', 'inbox.lt', 'inbox.lv',
				'ukr.net', 'i.ua', 'freemail.hu',
			]
		);
	}

	/** @return array<string,array<string,string>> */
	private static function get_messages(): array {
		return [
			'pl' => [
				'suggestion' => 'Czy chodziło Ci o %s?',
				'validation' => 'Adres zawiera prawdopodobną literówkę w domenie „%1$s”. Czy chodziło Ci o „%2$s”?',
				'invalid'    => 'Wpisz prawidłowy adres e-mail.',
				'dns'        => 'Domena tego adresu e-mail nie istnieje lub nie obsługuje poczty.',
			],
			'en' => [
				'suggestion' => 'Did you mean %s?',
				'validation' => 'The address probably contains a typo in the domain “%1$s”. Did you mean “%2$s”?',
				'invalid'    => 'Enter a valid email address.',
				'dns'        => 'The email domain does not exist or does not accept email.',
			],
			'cs' => [
				'suggestion' => 'Měli jste na mysli %s?',
				'validation' => 'Adresa pravděpodobně obsahuje překlep v doméně „%1$s“. Měli jste na mysli „%2$s“?',
			],
			'de' => [
				'suggestion' => 'Meinten Sie %s?',
				'validation' => 'Die Adresse enthält wahrscheinlich einen Tippfehler in der Domain „%1$s“. Meinten Sie „%2$s“?',
			],
			'it' => [
				'suggestion' => 'Intendevi %s?',
				'validation' => 'L’indirizzo contiene probabilmente un errore nel dominio “%1$s”. Intendevi “%2$s”?',
			],
			'lt' => [
				'suggestion' => 'Ar turėjote omenyje %s?',
				'validation' => 'Adreso domene „%1$s“ tikriausiai yra klaida. Ar turėjote omenyje „%2$s“?',
			],
			'lv' => [
				'suggestion' => 'Vai domājāt %s?',
				'validation' => 'Adreses domēnā “%1$s” iespējams ir drukas kļūda. Vai domājāt “%2$s”?',
			],
			'sk' => [
				'suggestion' => 'Mali ste na mysli %s?',
				'validation' => 'Adresa pravdepodobne obsahuje preklep v doméne „%1$s“. Mali ste na mysli „%2$s“?',
			],
			'uk' => [
				'suggestion' => 'Ви мали на увазі %s?',
				'validation' => 'Адреса, ймовірно, містить помилку в домені «%1$s». Ви мали на увазі «%2$s»?',
			],
			'ro' => [
				'suggestion' => 'Ați vrut să scrieți %s?',
				'validation' => 'Adresa conține probabil o greșeală în domeniul „%1$s”. Ați vrut să scrieți „%2$s”?',
			],
			'et' => [
				'suggestion' => 'Kas mõtlesite %s?',
				'validation' => 'Aadressi domeenis „%1$s“ on tõenäoliselt kirjaviga. Kas mõtlesite „%2$s“?',
			],
			'hu' => [
				'suggestion' => 'Erre gondolt: %s?',
				'validation' => 'A cím valószínűleg elírást tartalmaz a(z) „%1$s” domainben. Erre gondolt: „%2$s”?',
			],
			'fr' => [
				'suggestion' => 'Vouliez-vous dire %s ?',
				'validation' => 'L’adresse contient probablement une faute dans le domaine « %1$s ». Vouliez-vous dire « %2$s » ?',
			],
			'es' => [
				'suggestion' => '¿Quiso decir %s?',
				'validation' => 'La dirección probablemente contiene un error en el dominio «%1$s». ¿Quiso decir «%2$s»?',
			],
		];
	}

	private static function get_language(): string {
		$allowed = [ 'pl', 'en', 'cs', 'de', 'it', 'lt', 'lv', 'sk', 'uk', 'ro', 'et', 'hu', 'fr', 'es' ];
		$locale  = function_exists( 'determine_locale' ) ? determine_locale() : get_locale();
		$language = strtolower( substr( str_replace( '_', '-', (string) $locale ), 0, 2 ) );

		return in_array( $language, $allowed, true ) ? $language : 'en';
	}

	public static function validate_email_domain( $result, $value, $form, $field ) {
		if ( ! $field || 'email' !== $field->get_input_type() || ! $result['is_valid'] ) {
			return $result;
		}

		// We only validate fields marked with the pwe-email-validate class.
		$css_classes = preg_split( '/\s+/', trim( (string) $field->cssClass ) ) ?: [];
		if ( ! in_array( 'pwe-email-validate', $css_classes, true ) ) {
			return $result;
		}

		$email = is_string( $value ) ? strtolower( trim( $value ) ) : '';
		if ( '' === $email ) {
			return $result;
		}

		$messages = self::get_messages();
		$language = self::get_language();
		$current_messages = $messages[ $language ] ?? $messages['en'];

		// 1. Check the syntax of the full email address.
		if ( false === filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			$result['is_valid'] = false;
			$result['message']  = $current_messages['invalid'] ?? $messages['en']['invalid'];

			return $result;
		}

		$at_position = strrpos( $email, '@' );
		if ( false === $at_position ) {
			$result['is_valid'] = false;
			$result['message']  = $current_messages['invalid'] ?? $messages['en']['invalid'];

			return $result;
		}

		$domain      = substr( $email, $at_position + 1 );
		$corrections = self::get_domain_corrections();

		// 2. First, we keep the current, more helpful typo suggestion.
		if ( isset( $corrections[ $domain ] ) ) {
			$template = $current_messages['validation'] ?? $messages['en']['validation'];

			$result['is_valid'] = false;
			$result['message']  = sprintf(
				$template,
				esc_html( $domain ),
				esc_html( $corrections[ $domain ] )
			);

			return $result;
		}

		// 3. Check if the domain exists and can handle email.
		// If checkdnsrr() is not available on the server, we don't block the form.
		if ( function_exists( 'checkdnsrr' ) ) {
			$fqdn   = rtrim( $domain, '.' ) . '.';
			$dns_ok = checkdnsrr( $fqdn, 'MX' ) || checkdnsrr( $fqdn, 'A' );

			if ( ! $dns_ok ) {
				$result['is_valid'] = false;
				$result['message']  = $current_messages['dns'] ?? $messages['en']['dns'];

				return $result;
			}
		}

		return $result;
	}
}


PWE_Email_Validator_Addon::init();
