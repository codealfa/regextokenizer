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
	public static function CSS_URL_CP($bCaptureValue=false)
	{
		return 'url\([\'"]?' . self::captureValue(self::CSS_URL_VALUE(), $bCaptureValue) . '[\'"]?\)';
	}

}

