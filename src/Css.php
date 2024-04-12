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

trait Css
{
    use Base;

    /**
     * Regex token for a CSS ident
     *
     * @return string
     */
    //language=RegExp
    public static function cssIdentToken(): string
    {
        return '(?:\\\\.|[a-z0-9_-]++\s++)';
    }

    /**
     * Regex token for a CSS url, optionally capturing the value in a capture group
     *
     * @param   bool  $shouldCaptureValue Whether to capture the value in a capture group
     *
     * @return string
     */
    //language=RegExp
    public static function cssUrlWithCaptureValueToken(bool $shouldCaptureValue = false): string
    {
        $cssUrl = '(?:url\(|(?<=url)\()(?:\s*+[\'"])?<<' . self::cssUrlValueToken() . '>>(?:[\'"]\s*+)?\)';

        return self::prepare($cssUrl, $shouldCaptureValue);
    }

    public static function cssUrlToken(): string
    {
        return "url\((?>[^)\\]++|\\.)*+\)";
    }

    public static function cssSelectorsListToken(): string
    {
        $bc = self::blockCommentToken();

        return "(?>[_a-zA-Z0-9:.\#\*,\s>+~\"'^$=|()\[\]-]++|{$bc}|\\.)++(?={)";
    }

    public static function cssDeclarationsListToken(): string
    {
        $bc = self::blockCommentToken();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();
        $url = self::cssUrlToken();

        return "(?>[^{}@/'\"u\\]++|{$bc}|{$dqStr}|{$sqStr}|{$url}|\\.|u)*";
    }

    public static function cssRuleToken(): string
    {
        $selectors = self::cssSelectorsListToken();
        $declarations = self::cssDeclarationsListToken();

        return "$selectors{{$declarations}+}";
    }

    public static function cssRulesListToken(): string
    {
        $cssRule = self::cssRuleToken();
        $bc = self::blockCommentToken();

        return "(?>{$cssRule}|{$bc}|\s++)+";
    }

    public static function cssRegularAtRulesToken(?string $identifier = null): string
    {
        $bc = self::blockCommentToken();
        $url = self::cssUrlToken();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();

        $name = $identifier ?? '[a-zA-Z-]++';

        return "@{$name}\s++(?>[^;/u{}@\'\"]++|{$bc}|{$url}|{$dqStr}|{$sqStr}|\\.|u)++;";
    }

    public static function cssNestedAtRulesToken(): string
    {
        $bc = self::blockCommentToken();
        $cssRulesList = self::cssRulesListToken();
        $regularAtRule = self::cssRegularAtRulesToken();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();
        $url = self::cssUrlToken();

        //language=RegExp
        return "(?P<atrule>@[a-zA-Z-]++\s++(?>[^{}/;@u\\]++|{$bc}|{$url}|{$dqStr}|{$sqStr}|u|\\.)*+"
        . "{(?>(?:\s++|{$regularAtRule}|{$cssRulesList}+|{$bc})++|(?&atrule))*+})";
    }

    public static function cssNamedNestedAtRulesToken(string $identifier): string
    {
        $bc = self::blockCommentToken();
        $cssString = self::cssStringToken();

        //language=RegExp
        return "@{$identifier}\s++(?>[^{}/;@\]++|{$bc}|\\.)++{{$cssString}+}";
    }

    public static function cssStringToken(): string
    {
        $bc = self::blockCommentToken();
        $nestedAtRule = self::cssNestedAtRulesToken();
        $regularAtRule = self::cssRegularAtRulesToken();
        $cssRulesList = self::cssRulesListToken();

        return "(?>\s++|{$cssRulesList}+|{$nestedAtRule}|{$regularAtRule}|{$bc})*";
    }

    /**
     * Regex token for a CSS url value
     *
     * @return string
     */
    //language=RegExp
    public static function cssUrlValueToken(): string
    {
        return '(?:' . self::stringValueToken() . '|' . self::cssUnquotedUrlValueToken() . ')';
    }

    /**
     * Regex token for an unquoted CSS url value
     *
     * @return string
     */
    //language=RegExp
    public static function cssUnquotedUrlValueToken(): string
    {
        return '(?<=url\()(?>\s*+(?:\\\\.)?[^\\\\()\s\'"]*+)++';
    }
}
