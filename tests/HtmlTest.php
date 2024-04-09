<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 *  @package   jchoptimize/core
 *  @author    Samuel Marshall <samuel@jch-optimize.net>
 *  @copyright Copyright (c) 2024 Samuel Marshall / JCH Optimize
 *  @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace CodeAlfa\RegexTokenizer\Tests;

use CodeAlfa\RegexTokenizer\Html;
use PHPUnit\Framework\TestCase;

use function preg_match;

class HtmlTest extends TestCase
{
    use Html;

    public function testHtmlCommentToken(): void
    {
        $string = /** @lang HTML */
            '<!--
        -- This is a comment --
        -->';

        $hc = self::htmlCommentToken();

        preg_match("#{$hc}#ix", $string, $matches);
        $this->assertEquals($string, $matches[0], 'html comment');
    }

    public function htmlAttributeData(): array
    {
        return [
            [
                'attribute' => 'style="property:\'value\'"',
                'message' => 'double quote'
            ],
            [
                'attribute' => "onopen='<script></script>'",
                'message' => 'double quote'
            ],
            [
                'attribute' => 'id=item1',
                'message' => 'no quotes'
            ],
            [
                'attribute' => 'class = "open-container"',
                'message' => 'space around equal'
            ],
            [
                'attribute' => 'async',
                'message' => 'binary attribute'
            ]
        ];
    }

    /**
     * @dataProvider htmlAttributeData
     */
    public function testHtmlAttributeToken(string $attribute, string $message): void
    {
        $a = self::htmlAttributeToken();

        preg_match("#{$a}#ix", $attribute, $matches);
        $this->assertEquals($attribute, $matches[0], $message);
    }

    public function testHtmlAttributesListToken(): void
    {
        $attributes = 'id="item1"class=\'item2\' style="property:value" async defer="defer"';
        $al = self::htmlAttributesListToken();

        preg_match("#{$al}#ix", $attributes, $matches);
        $this->assertEquals($attributes, $matches[0], 'attributes list');
    }

    public function htmlHeadTagData(): array
    {
        return [
            [
                'tag' => '<div id="item1">',
                'message' => 'default'
            ],
            [
                'tag' => '<div  class = "container"   >',
                'message' => 'extra space'
            ],
            [
                'tag' => '<script async defer>',
                'message' => 'binary attributes'
            ],
            [
                'tag' => '<div>',
                'message' => 'no attributes'
            ],
            [
                'tag' => '<link rel="stylesheet" src="http://www.example.com/style.css" />',
                'message' => 'self closing'
            ]
        ];
    }

    /**
     * @dataProvider htmlHeadTagData
     */
    public function testHtmlStartTagToken(string $tag, string $message): void
    {
        $start = self::htmlStartTagToken();
        preg_match("#{$start}#ix", $tag, $matches);
        $this->assertEquals($tag, $matches[0], $message);
    }

    public function htmlElementTokenData(): array
    {
        return [
            [
                'tag' => '<script>const i; if (i < 0);</script>',
                'name' => 'script',
                'voidElement' => false,
                'message' => 'script'
            ],
            [
                'tag' => '<style>h1 > span{property:value;}</style>',
                'name' => 'style',
                'voidElement' => null,
                'message' => 'style'
            ],
            [
                'tag' => '<link rel="stylesheet" src="http://www.example.com/style.css" />',
                'name' => null,
                'voidElement' => true,
                'message' => 'void'
            ]
        ];
    }

    /**
     * @dataProvider htmlElementTokenData
     */
    public function testHtmlElementToken(string $tag, ?string $name, ?bool $voidElement, string $message): void
    {
        $el = self::htmlElementToken($name, $voidElement);

        preg_match("#{$el}#ix", $tag, $matches);
        $this->assertEquals($tag, $matches[0], $message);
    }

    public function testHtmlNestedElementToken(): void
    {
        $html = '<ul><li><ul><li><span></span></li></ul></li></ul>';
        $regex = self::htmlNestedElementToken('ul');

        preg_match("#{$regex}#ix", $html, $matches);
        $this->assertEquals($html, $matches[0], 'nested elements');
    }

    public function testHtmlStringToken(): void
    {
        $html = '<!DOCTYPE html><html><head><title></title></head><body class=""></body></html>';
        $regex = self::htmlStringToken();

        preg_match("#{$regex}#ix", $html, $matches);
        $this->assertEquals($html, $matches[0], 'nested elements');
    }
}
