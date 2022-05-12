<?php

/**
 * @package   codealfa/regextokenizer
 * @author    Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2020 Samuel Marshall
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace CodeAlfa\RegexTokenizer;

trait Html
{
	use Base;

	//language=RegExp
	public static function HTML_COMMENT(): string
	{
		return '<!--(?>-?[^-]*+)*?--!?>';
		//return '(?:(?:<!--|(?<=[\s/^])-->)[^\r\n]*+)';
	}

	//language=RegExp
	public static function HTML_GENERIC_ELEMENT(): string
	{
		return '[a-z0-9]++';
	}

	//language=RegExp
	public static function HTML_ATTRIBUTE_CP( $attrName = '', $captureValue = false, $captureDelimiter = false, $matchedValue = '' )
	{
		$name      = $attrName != '' ? $attrName : '[^\s/"\'=<>]++';
		$delimiter = $captureDelimiter ? '([\'"]?)' : '[\'"]?';

		//If we don't need to match a value then the value of attribute is optional
		if ( $matchedValue == '' )
		{
			$attribute = $name . '(?:\s*+=\s*+(?>' . $delimiter . ')<<' . self::HTML_ATTRIBUTE_VALUE() . '>>[\'"]?)?';
		}
		else
		{
			$attribute = $name . '\s*+=\s*+(?>' . $delimiter . ')' . $matchedValue . '<<' . self::HTML_ATTRIBUTE_VALUE() . '>>[\'"]?';
		}

		return self::prepare( $attribute, $captureValue );
	}

	//language=RegExp
	public static function HTML_ATTRIBUTE_VALUE(): string
	{
		return '(?:' . self::STRING_VALUE() . '|' . self::HTML_ATTRIBUTE_VALUE_UNQUOTED() . ')';
	}

	//language=RegExp
	public static function HTML_ATTRIBUTE_VALUE_UNQUOTED(): string
	{
		return '(?<==)[^\s*+>]++';
	}

	//language=RegExp
	public static function HTML_ELEMENTS( array $aElement ): string
	{
		$aResult = array();

		foreach ( $aElement as $sElement )
		{
			$aResult[] = self::HTML_ELEMENT( $sElement );
		}

		return '(?:' . implode( '|', $aResult ) . ')';
	}

	//language=RegExp
	public static function HTML_ELEMENT( $element = '', $isSelfClosing = false ): string
	{
		$name = $element != '' ? $element : self::HTML_GENERIC_ELEMENT();
		$tag  = '<' . $name . '\b(?:\s++' . self::parseAttributesStatic() . ')?\s*+>';

		if ( ! $isSelfClosing )
		{
			$tag .= '(?><?[^<]*+)*?</' . $name . '\s*+>';
		}

		return $tag;
	}

	//language=RegExp
	public static function HTML_ELEMENT_SELF_CLOSING( $element = '' ): string
	{
		return self::HTML_ELEMENT( $element, true );
	}


	protected static function parseAttributesStatic(): string
	{
		return '(?>' . self::HTML_ATTRIBUTE_CP() . '\s*+)*?';
	}
}