<?php

namespace CodeAlfa\RegexTokenizer;

Trait Html
{
	use Base;

	//language=RegExp
	public static function HTML_COMMENT()
	{
		return '(?:(?:<!--|(?<=[\s/^])-->)[^\r\n]*+)';
	}

	//language=RegExp
	public static function HTML_ATTRIBUTE_VALUE_UNQUOTED()
	{
		return '(?<==)[^\s*+>]++';
	}

	//language=RegExp
	public static function HTML_ATTRIBUTE_VALUE()
	{
		return '(?:' . self::STRING_VALUE() . '|' . self::HTML_ATTRIBUTE_VALUE_UNQUOTED() . ')';
	}

	//language=RegExp
	public static function HTML_ATTRIBUTE_CP($bCaptureValue=false)
	{
		return '[^\s/"\'=<>]++(?:\s*+=\s*+[\'"]?' . self::captureValue(self::HTML_ATTRIBUTE_VALUE(), $bCaptureValue) . '[\'"]?)?';
	}
}