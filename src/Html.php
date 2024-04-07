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

        if ($voidElement) {
            return $startTag;
        }

        $textContent = self::htmlTextContent();
        $endTag = self::htmlEndTagToken($name);

        return "{$startTag}{$textContent}{$endTag}";
    }

    //language=RegExp
    public static function htmlNestedElementToken(string $name): string
    {
        $startTag = self::htmlStartTagToken($name);
        $endTag = self::htmlEndTagToken($name);

        return "(?<{$name}>{$startTag}(?>(?>(?:<(?!/?{$name}))?[^<]++)++|(?&{$name}))*+{$endTag})";
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

    /**
     * Regex for parsing an HTML attribute
     *
     * @return string
     */
    //language=RegExp
    protected static function parseAttributesStatic(): string
    {
        return '(?>' . self::htmlAttributeWithCaptureValueToken() . '\s*+)*?';
    }

    /**
     * Regex token for an HTML attribute, optionally capturing the value in a capture group
     *
     * @param string $attrName
     * @param bool $captureValue
     * @param bool $captureDelimiter
     * @param string $matchedValue
     *
     * @return string
     */
    //language=RegExp
    public static function htmlAttributeWithCaptureValueToken(
        string $attrName = '',
        bool $captureValue = false,
        bool $captureDelimiter = false,
        string $matchedValue = ''
    ): string {
        $name = $attrName != '' ? $attrName : '[^\s/"\'=<>]++';
        $delimiter = $captureDelimiter ? '([\'"]?)' : '[\'"]?';

        //If we don't need to match a value then the value of attribute is optional
        if ($matchedValue == '') {
            $attribute = $name . '(?:\s*+=\s*+(?>' . $delimiter . ')<<' . self::htmlAttributeValueToken() . '>>[\'"]?)?';
        } else {
            $attribute = $name . '\s*+=\s*+(?>' . $delimiter . ')' . $matchedValue . '<<' . self::htmlAttributeValueToken() . '>>[\'"]?';
        }

        return self::prepare($attribute, $captureValue);
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

        return "(?>{$a}|\s++)*+";
    }

    public static function htmlStartTagToken(?string $name = null): string
    {
        $element = $name ?? self::htmlGenericElementToken();
        $attributes = self::htmlAttributesListToken();

        return "<{$element}\b\s*+{$attributes}\s*+/?>";
    }

    public static function htmlEndTagToken(?string $name = null): string
    {
        $element = $name ?? self::htmlGenericElementToken();

        return "</{$element}\s*+>";
    }

    public static function htmlTextContent(): string
    {
        return "(?>[^<]++|<)*?";
    }


    /**
     * Regex token for an HTML attribute value
     *
     * @return string
     */
    //language=RegExp
    public static function htmlAttributeValueToken(): string
    {
        return '(?:' . self::stringValueToken() . '|' . self::htmlUnquotedAttributeValueToken() . ')';
    }

    /**
     * Regex token for an unquoted HTML attribute value
     *
     * @return string
     */
    //language=RegExp
    public static function htmlUnquotedAttributeValueToken(): string
    {
        return '(?<==)[^\s*+>]++';
    }

    /**
     * Regex token for a self closing HTML element
     *
     * @param string $element Name of element
     *
     * @return string
     */
    public static function htmlSelfClosingElementToken(string $element = ''): string
    {
        return self::htmlElementToken($element, true);
    }
}
