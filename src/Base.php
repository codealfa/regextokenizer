<?php

namespace CodeAlfa\RegexTokenizer;

trait Base
{

	//language=RegExp
	public static function DOUBLE_QUOTE_STRING_VALUE()
	{
		return '(?<=")(?>(?:\\\\.)?[^\\\\"]*+)++';
	}

	//language=RegExp
	public static function SINGLE_QUOTE_STRING_VALUE()
	{
		return "(?<=')(?>(?:\\\\.)?[^\\\\']*+)++";
	}

	//language=RegExp
	public static function BACK_TICK_STRING_VALUE()
	{
		return '(?<=`)(?>(?:\\\\.)?[^\\\\`]*+)++';
	}

	//language=RegExp
	public static function STRING_VALUE()
	{
		return '(?:' . self::DOUBLE_QUOTE_STRING_VALUE() . '|' . self::SINGLE_QUOTE_STRING_VALUE() . '|' . self::BACK_TICK_STRING_VALUE() . ')';
	}

	//language=RegExp
	public static function DOUBLE_QUOTE_STRING()
	{
		return '"' . self::DOUBLE_QUOTE_STRING_VALUE() . '(?:"|(?=$))';
	}

	//language=RegExp
	public static function SINGLE_QUOTE_STRING()
	{
		return "'" . self::SINGLE_QUOTE_STRING_VALUE() . "(?:'|(?=$))";
	}

	//language=RegExp
	public static function BACK_TICK_STRING()
	{
		return '`' . self::BACK_TICK_STRING_VALUE() . '(?:`|(?=$))';
	}

	//language=RegExp
	public static function STRING_CP( $bCaptureValue = false )
	{
		return '[\'"`]' . self::captureValue( self::STRING_VALUE(), $bCaptureValue ) . '[\'"`]';
	}

	//language=RegExp
	public static function BLOCK_COMMENT()
	{
		return '/\*(?>[^/*]++|//|\*(?!/)|(?<!\*)/)*+\*/';
	}

	//language=RegExp
	public static function LINE_COMMENT()
	{
		return '//[^\r\n]*+';
	}

	//language=RegExp
	public static function COMMENT()
	{
		return '(?:' . self::BLOCK_COMMENT() . '|' . self::LINE_COMMENT() . ')';
	}

	protected static function throwExceptionOnPregError()
	{
		$error = array_flip( array_filter( get_defined_constants( true )['pcre'], function ( $value ) {
			return substr( $value, - 6 ) === '_ERROR';
		}, ARRAY_FILTER_USE_KEY ) )[preg_last_error()];

		if ( preg_last_error() != PREG_NO_ERROR )
		{
			throw new \Exception( $error );
		}
	}

	private static function captureValue( $sValue, $bCaptureValue, $bResetBranch = false )
	{
		if ( $bCaptureValue )
		{
			if ( $bResetBranch )
			{
				return '(?|' . $sValue . ')';
			}

			return '(' . $sValue . ')';
		}

		return $sValue;

	}


}