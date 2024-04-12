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

use function self;

trait Css
{
    use Base;

    //language=RegExp
    public static function cssIdentToken(): string
    {
        $esc = self::cssEscapedString();

        return "(?>[a-zA-Z0-9_-]++|{$esc})++";
    }

    public static function cssEscapedString(): string
    {
        return "\\\\[0-9a-zA-Z]++\s*+|\\\\.";
    }

    public static function cssBasicSelectorsString(): string
    {
        $esc = self::cssEscapedString();

        return "(?>[*.\#\[\]=:0-9a-zA-Z_-]++|{$esc})++";
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
        $esc = self::cssEscapedString();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();

        return "url\((?>{$dqStr}|{$sqStr}|(?:[^)\\\\]++|{$esc})++)*+\)";
    }

    public static function cssSelectorsListToken(): string
    {
        $bc = self::blockCommentToken();
        $esc = self::cssEscapedString();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();

        return "(?>(?:[*.\#\[\]:0-9a-zA-Z_-]++[\s+~>|=$^*()]*+|{$esc})++|{$bc}|\s++)++";
    }

    public static function cssDeclarationsListToken(): string
    {
        $bc = self::blockCommentToken();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();
        $esc = self::cssEscapedString();

        return "(?<={)(?>[^{}@/'\"]++|{$bc}|{$dqStr}|{$sqStr}|{$esc}|/++|(?<={)(?=}))++(?=})";
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
        $selectors = self::cssSelectorsListToken();
        $url = self::cssUrlToken();

        $name = $identifier ?? '[a-zA-Z-]++';

        return "@{$name}\s(?>{$selectors}|{$url})++;";
    }

    public static function cssNestedAtRulesToken(): string
    {
        $bc = self::blockCommentToken();
        $cssRulesList = self::cssRulesListToken();
        $regularAtRule = self::cssRegularAtRulesToken();
        $selectors = self::cssSelectorsListToken();
        $declarations = self::cssDeclarationsListToken();

        //language=RegExp
        return "(?P<atrule>@[a-zA-Z-]++\s{$selectors}|$url)++"
        . "{(?>(?:\s++|{$bc}|{$regularAtRule}|{$cssRulesList}+|{$declarations})++|(?&atrule))*+})";
    }

    public static function cssNamedNestedAtRulesToken(string $identifier): string
    {
        $bc = self::blockCommentToken();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();
        $selectors = self::cssSelectorsListToken();
        $esc = self::cssEscapedString();

        //language=RegExp
        return "@{$identifier}\s+{$selectors}"
        . "(?P<nestedblock>{(?>(?:[^{}/\\\\'\"]++|{$bc}|{$dqStr}|{$sqStr}|{$esc}|/)++|(?&nestedblock))*+})";
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
