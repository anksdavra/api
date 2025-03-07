<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class PullQuote extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {

		$widget['text'] = $this->convertCurlyQuotes( $widget['text'] );
		$widget['text'] = str_replace('"', '', $widget['text']);

		return $widget;
	}

	private function convertCurlyQuotes($text): string
	{
		$quoteMapping = [

			// U+0084⇒U+201E double low-9 quotation mark
			"\xC2\x84"     => '"',

			// U+0093⇒U+201C left double quotation mark
			"\xC2\x93"     => '"',

			// U+0094⇒U+201D right double quotation mark
			"\xC2\x94"     => '"',

			// U+00AB left-pointing double angle quotation mark
			"\xC2\xAB"     => '"',

			// U+00BB right-pointing double angle quotation mark
			"\xC2\xBB"     => '"',

			// U+201C left double quotation mark
			"\xE2\x80\x9C" => '"',

			// U+201D right double quotation mark
			"\xE2\x80\x9D" => '"',

			// U+201E double low-9 quotation mark
			"\xE2\x80\x9E" => '"',

			// U+201F double high-reversed-9 quotation mark
			"\xE2\x80\x9F" => '"',

			// HTML left double quote
			"&ldquo;"      => '"',

			// HTML right double quote
			"&rdquo;"      => '"'
		];

		return strtr(html_entity_decode($text, ENT_QUOTES, "UTF-8"), $quoteMapping);
	}
}
