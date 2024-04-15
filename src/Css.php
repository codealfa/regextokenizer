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

    public static function cssSelectorListToken(): string
    {
        $bc = self::blockCommentToken();
        $esc = self::cssEscapedString();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();

        return "(?<=^|[{}/\s;|])(?>[^{}@/\\\\%;]++|{$esc}|{$bc}|{$sqStr}|{$dqStr})++(?=\s*+{)";
    }

    public static function cssDeclarationListToken(): string
    {
        $bc = self::blockCommentToken();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();
        $esc = self::cssEscapedString();
        $startingStyle = self::cssNamedNestedAtRulesToken('starting-style');

        return "(?<=[{\s])(?>(?>[^{}@\[\]/\\\\~^=]++|{$bc}|{$dqStr}|{$sqStr}|{$esc}|/++|(?<={)(?=}))++"
        . "|{$startingStyle})++(?=\s*+})";
    }

    public static function cssRuleToken(): string
    {
        $selectors = self::cssSelectorListToken();
        $declarations = self::cssDeclarationListToken();

        return "$selectors{{$declarations}}";
    }

    public static function cssRuleListToken(): string
    {
        $cssRule = self::cssRuleToken();
        $bc = self::blockCommentToken();

        return "(?>{$cssRule}|{$bc}|\s++)++";
    }

    public static function cssRegularAtRulesToken(?string $identifier = null): string
    {
        $esc = self::cssEscapedString();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();
        $bc = self::blockCommentToken();

        $name = $identifier ?? '[a-zA-Z-]++';

        return "@{$name}\s(?>[^{}@/\\\\;]++|{$esc}|{$bc}|{$dqStr}|{$sqStr}|/)++;";
    }

    public static function cssNestedAtRulesToken(): string
    {
        $bc = self::blockCommentToken();
        $cssRulesList = self::cssRuleListToken();
        $regularAtRule = self::cssRegularAtRulesToken();
        $esc = self::cssEscapedString();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();
        $declarations = self::cssDeclarationListToken();
        $selectors = self::cssSelectorListToken();

        static $cnt = 0;
        $name = 'nestedatrule' . $cnt++;
        //language=RegExp
        return "(?P<{$name}>@[a-zA-Z-]++\s(?>[^{}@/\\\\;]++|{$esc}|{$bc}|{$dqStr}|{$sqStr}|/)*+"
        . "{(?>(?:\s++|{$bc}|{$regularAtRule}|{$cssRulesList}|{$declarations})++|(?&$name))*+})";
    }

    public static function cssNamedNestedAtRulesToken(string $identifier): string
    {
        $bc = self::blockCommentToken();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();
        $esc = self::cssEscapedString();

        static $cnt = 0;
        $name = 'namedatrule' . $cnt++;

        //language=RegExp
        return "@{$identifier}\s(?>[^{}@/'\"\\\\]++|{$esc}|{$bc}|{$dqStr}|{$sqStr}|/)*+"
        . "(?P<{$name}>{(?>(?:[^{}/\\\\'\"]++|{$bc}|{$dqStr}|{$sqStr}|{$esc}|/)++|(?&{$name}))*+})";
    }

    public static function cssStringToken(): string
    {
        $bc = self::blockCommentToken();
        $nestedAtRule = self::cssNestedAtRulesToken();
        $regularAtRule = self::cssRegularAtRulesToken();
        $cssRulesList = self::cssRuleListToken();

        return "(?>\s++|{$cssRulesList}|{$nestedAtRule}|{$regularAtRule}|{$bc})*";
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
