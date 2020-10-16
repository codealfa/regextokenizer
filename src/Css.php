<?php

namespace CodeAlfa\RegexTokenizer;

trait Css
{
	Use Base;

	//language=RegExp
	public static function CSS_URL_VALUE_UNQUOTED()
	{
		return '(?<=url\()(?>(?:\\\\.)?[^\\\\()\s\'"]*+)++';
	}

	//language=RegExp
	public static function CSS_URL_VALUE()
	{
		return '(?:' . self::STRING_VALUE() . '|' . self::CSS_URL_VALUE_UNQUOTED() . ')';
	}

	//language=RegExp
	public static function CSS_URL_CP($bCaptureValue)
	{
		return 'url\([\'"]?' . self::captureValue(self::CSS_URL_VALUE, $bCaptureValue) . '[\'"]?\)';
	}

	//language=RegExp
	public static function CSS_AT_IMPORT_CP($bCaptureValue)
	{
		$sValue = '(?:' . self::STRING_CP($bCaptureValue) . '|' . self::CSS_URL_CP($bCaptureValue) . ')';

		return '@import\s++' . self::captureValue($sValue, $bCaptureValue, true) . '[^;]*+;';
	}
}

