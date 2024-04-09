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

    /**
     * Regex token for a string
     *
     * @return string
     */
    //language=RegExp
    public static function htmlCommentToken(): string
    {
        return '<!--(?>[^-]++|\-++)*?--!?>';
    }

    /**
     * Regex token for an array of HTML elements
     *
     * @param string[] $elements Array of names of HTML elements
     *
     * @return string
     */
    //language=RegExp
    public static function htmlElementsToken(array $elements): string
    {
        $result = [];

        foreach ($elements as $element) {
            $result[] = self::htmlElementToken($element);
        }

        return '(?:' . implode('|', $result) . ')';
    }

    //language=RegExp
    public static function htmlElementToken(?string $name = null, ?bool $voidElement = false): string
    {
        $startTag = self::htmlStartTagToken($name);

        if ($voidElement === true) {
            return $startTag;
        }

        $textContent = self::htmlTextContentToken($name);
        $endTag = self::htmlEndTagToken($name);

        if ($voidElement === false) {
            return "{$startTag}{$textContent}?{$endTag}";
        }

        return "{$startTag}(?:{$textContent}?{$endTag})?";
    }

    //language=RegExp
    public static function htmlNestedElementToken(string $name): string
    {
        $startTag = self::htmlStartTagToken($name);
        $endTag = self::htmlEndTagToken($name);

        return "(?<{$name}>{$startTag}(?>(?:[^<]++|(?!</?{$name})<)++|(?&{$name}))*+{$endTag})";
    }

    /**
     * Regex token for any valid HTML element name
     *
     * @return string
     */
    //language=RegExp
    public static function htmlGenericElementToken(): string
    {
        return '[a-z0-9]++';
    }

    public static function htmlAttributeToken(): string
    {
        $ds = self::doubleQuoteStringToken();
        $ss = self::singleQuoteStringToken();
        $bs = self::backTickStringToken();

        return "[^\s/\"'=<>]++(?:\s*+=\s*+(?>{$ds}|{$ss}|{$bs}|(?<==)[^\s<>'\"]++))?";
    }

    public static function htmlAttributesListToken(): string
    {
        $a = self::htmlAttributeToken();

        return "(?>{$a}|\s++)*";
    }

    public static function htmlStartTagToken(?string $name = null): string
    {
        $element = $name ?? self::htmlGenericElementToken();
        $attributes = self::htmlAttributesListToken();

        return "<{$element}\b\s*+{$attributes}+\s*+/?>";
    }

    public static function htmlEndTagToken(?string $name = null): string
    {
        $element = $name ?? self::htmlGenericElementToken();

        return "</{$element}\s*+>";
    }

    public static function htmlTextContentToken(?string $name = null): string
    {
        $st = self::htmlStartTagToken($name);
        $et = self::htmlEndTagToken($name);

        return "(?>[^<]++|(?!{$st}|$et})<)*";
    }

    public static function htmlStringToken(?string $name = null): string
    {
        $c = self::htmlCommentToken();
        $el = self::htmlElementToken($name, null);
        $et = self::htmlEndTagToken($name);

        return "(?>[^<]++|{$c}|{$el}|{$et}|<)*";
    }

    public static function htmlVoidElementToken(?string $element = null): string
    {
        return self::htmlElementToken($element, true);
    }
}
