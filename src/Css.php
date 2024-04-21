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

        return "(?>{$esc}|[a-zA-Z0-9_-]++)++";
    }

    public static function cssEscapedString(): string
    {
        return "\\\\[0-9a-zA-Z]++\s*+|\\\\.";
    }

    public static function cssUrlToken(): string
    {
        $esc = self::cssEscapedString();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();

        return "url\((?>{$dqStr}|{$sqStr}|(?:{$esc}|[^)\\\\]++)++)*+\)";
    }

    public static function cssSelectorListToken(): string
    {
        $bc = self::blockCommentToken();
        $esc = self::cssEscapedString();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();

        return "(?<=^|[{}/\s;|])[^{}@/\\\\'\"\s;]++(?>[^{}@/\\\\'\";]++|{$esc}|{$bc}|{$sqStr}|{$dqStr})*+(?={)";
    }

    public static function cssDeclarationListToken(): string
    {
        $bc = self::blockCommentToken();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();
        $esc = self::cssEscapedString();
        $startingStyle = self::cssNestedAtRulesToken('starting-style');

        return "(?<={)(?>(?>[^{}@/\\\\'\"]++|{$bc}|{$dqStr}|{$sqStr}|{$esc}|[/\\\\]++|(?<={)(?=}))++"
        . "|{$startingStyle})++(?=})";
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

        return "(?>\s++|{$bc}|{$cssRule})++";
    }

    public static function cssRegularAtRulesToken(?string $name = null): string
    {
        $esc = self::cssEscapedString();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();
        $bc = self::blockCommentToken();

        $name = $name ?? '[a-zA-Z-]++';

        return "@{$name}\s(?>[^{}@/\\\\'\";]++|{$esc}|{$bc}|{$dqStr}|{$sqStr}|/)++;";
    }

    public static function cssNestedAtRulesToken(?string $name = null): string
    {
        $bc = self::blockCommentToken();
        $esc = self::cssEscapedString();
        $dqStr = self::doubleQuoteStringToken();
        $sqStr = self::singleQuoteStringToken();

        $name = $name ?? '[a-zA-Z-]++';

        static $cnt = 0;
        $captureGroup = 'atrule' . $cnt++;
        //language=RegExp
        return "@{$name}\s*+(?>[^{}@/\\\\'\";]++|{$esc}|{$bc}|{$dqStr}|{$sqStr}|/)*+"
        . "(?P<{$captureGroup}>{(?>(?:[^{}/\\\\'\"]++|{$bc}|{$esc}|{$dqStr}|{$sqStr})++|(?&$captureGroup))*+})";
    }

    public static function cssStringToken(): string
    {
        $bc = self::blockCommentToken();
        $nestedAtRule = self::cssNestedAtRulesToken();
        $regularAtRule = self::cssRegularAtRulesToken();
        $cssRuleList = self::cssRuleListToken();

        return "(?>\s++|{$bc}|{$cssRuleList}|{$nestedAtRule}|{$regularAtRule})*";
    }
}
