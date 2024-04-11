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

use CodeAlfa\RegexTokenizer\Css;
use PHPUnit\Framework\TestCase;

use function preg_match;

class CssTest extends TestCase
{
    use Css;

    public function cssSelectorListData(): array
    {
        return [
            [
                'cssRule' => /** @lang CSS */ '.intro{display:block;}',
                'selector' => '.intro',
                'message' => '.class'
            ],
                        [
                'cssRule' => /** @lang CSS */ '.name1.name2 /*comment*/ {display:block;}',
                'selector' => '.name1.name2 /*comment*/ ',
                'message' => '.class1.class2'
            ],
            [
                'cssRule' => /** @lang CSS */ '.name1 .name2 {display:block;}',
                'selector' => '.name1 .name2 ',
                'message' => '.class1 .class2'
            ],
            [
                'cssRule' => /** @lang CSS */ '#firstname{display:block;}',
                'selector' => '#firstname',
                'message' => '#id'
            ],
            [
                'cssRule' => /** @lang CSS */ '* {display:block;}',
                'selector' => '* ',
                'message' => '*'
            ],
            [
                'cssRule' => /** @lang CSS */ 'p{display:block;}',
                'selector' => 'p',
                'message' => 'element'
            ],
            [
                'cssRule' => /** @lang CSS */ 'p.intro{display:block;}',
                'selector' => 'p.intro',
                'message' => 'element.class'
            ],
            [
                'cssRule' => /** @lang CSS */ 'div, /* comment */ p{display:block;}',
                'selector' => 'div, /* comment */ p',
                'message' => 'element,element'
            ],
            [
                'cssRule' => /** @lang CSS */ 'div p{display:block;}',
                'selector' => 'div p',
                'message' => 'element element'
            ],
            [
                'cssRule' => /** @lang CSS */ 'div > p{display:block;}',
                'selector' => 'div > p',
                'message' => 'element>element'
            ],
            [
                'cssRule' => /** @lang CSS */ 'div + p {display:block;}',
                'selector' => 'div + p ',
                'message' => 'element+element'
            ],
            [
                'cssRule' => /** @lang CSS */ 'p ~ ul{display:block;}',
                'selector' => 'p ~ ul',
                'message' => 'element1~element2'
            ],
            [
                'cssRule' => /** @lang CSS */ '[target]{display:block;}',
                'selector' => '[target]',
                'message' => '[attribute]'
            ],
            [
                'cssRule' => /** @lang CSS */ '[target="_blank"]{display:block;}',
                'selector' => '[target="_blank"]',
                'message' => 'attribute=value'
            ],
            [
                'cssRule' => /** @lang CSS */ '[title~=\'flower\']{display:block;}',
                'selector' => '[title~=\'flower\']',
                'message' => 'element~=value'
            ],
            [
                'cssRule' => /** @lang CSS */ '[lang|="en"]{display:block;}',
                'selector' => '[lang|="en"]',
                'message' => 'attribute|=value'
            ],
            [
                'cssRule' => /** @lang CSS */ 'a[href^=https]{display:block;}',
                'selector' => 'a[href^=https]',
                'message' => 'attribute^=value'
            ],
            [
                'cssRule' => /** @lang CSS */ 'a[href$=".pdf"]{display:block;}',
                'selector' => 'a[href$=".pdf"]',
                'message' => 'attribute$=value'
            ],
            [
                'cssRule' => /** @lang CSS */ 'a[href*="w3schools"]{display:block;}',
                'selector' => 'a[href*="w3schools"]',
                'message' => 'attribute*=value'
            ],
            [
                'cssRule' => /** @lang CSS */ 'a:active{display:block;}',
                'selector' => 'a:active',
                'message' => ':active'
            ],
            [
                'cssRule' => /** @lang CSS */ 'p::after{display:block;}',
                'selector' => 'p::after',
                'message' => '::after'
            ],
            [
                'cssRule' => /** @lang CSS */ 'p::before{display:block;}',
                'selector' => 'p::before',
                'message' => '::before'
            ],
            [
                'cssRule' => /** @lang CSS */ 'input:checked{display:block;}',
                'selector' => 'input:checked',
                'message' => ':checked'
            ],
            [
                'cssRule' => /** @lang CSS */ 'p:first-child{display:block;}',
                'selector' => 'p:first-child',
                'message' => ':first-child'
            ],
            [
                'cssRule' => /** @lang CSS */ 'p::first-line{display:block;}',
                'selector' => 'p::first-line',
                'message' => '::first-line'
            ],
            [
                'cssRule' => /** @lang CSS */ 'p:lang(it){display:block;}',
                'selector' => 'p:lang(it)',
                'message' => ':lang(language)'
            ],
            [
                'cssRule' => /** @lang CSS */ '::marker{display:block;}',
                'selector' => '::marker',
                'message' => '::marker'
            ],
            [
                'cssRule' => /** @lang CSS */ ':not(p){display:block;}',
                'selector' => ':not(p)',
                'message' => ':not(selector)'
            ],
            [
                'cssRule' => /** @lang CSS */ 'p:nth-child(2){display:block;}',
                'selector' => 'p:nth-child(2)',
                'message' => 'nth-child(2)'
            ],
            [
                'cssRule' => /** @lang CSS */ '#news:target{display:block;}',
                'selector' => '#news:target',
                'message' => ':target'
            ],
        ];
    }

    /**
     * @dataProvider cssSelectorListData
     */
    public function testCssSelectorsListAToken($cssRule, $selector, $message): void
    {
        $selectorsList = self::cssSelectorsListToken();

        preg_match("#{$selectorsList}#ix", $cssRule, $matches);
        $this->assertEquals($selector, $matches[0], $message);
    }

    public function cssDeclarationsListData(): array
    {
        return [
            [
                'cssRule' =>  '#news:target{display:block;}',
                'declaration' => 'display:block;',
                'message' => 'no comment'
            ],
            [
                'cssRule' =>  '#news:target{/*comment*/display:block;}',
                'declaration' => '/*comment*/display:block;',
                'message' => 'comment before'
            ],
            [
                'cssRule' =>  '#news:target{display: /*comment*/ block;}',
                'declaration' => 'display: /*comment*/ block;',
                'message' => 'comment inside'
            ],
            [
                'cssRule' =>  '#news:target{display:block; /*comment*/}',
                'declaration' => 'display:block; /*comment*/',
                'message' => 'comment after'
            ],
        ];
    }

    /**
     * @dataProvider cssDeclarationsListData
     */
    public function testCssDeclarationListToken($cssRule, $declaration, $message): void
    {
        $declarationsList = self::cssDeclarationsListToken();

        preg_match("#(?<=\{){$declarationsList}(?=\})#ix", $cssRule, $matches);
        $this->assertEquals($declaration, $matches[0], $message);
    }

    public function cssRuleData(): array
    {
        return [
            [
                'cssRule' => /** @lang CSS */ 'p {
  color: red;
  text-align: center;
}',
                'message' => 'simple rule'
            ],
            [
                'cssRule' => /** @lang CSS */ 'input[type="search"]::-webkit-search-decoration,
input[type="search"]::-webkit-search-cancel-button {
	-webkit-appearance: none;
}',
                'message' => 'complex rule'
            ]
        ];
    }

    /**
     * @dataProvider cssRuleData
     */
    public function testCssRuleToken($cssRule, $message)
    {
        $cssRuleRegex = self::cssRuleToken();

        preg_match("#{$cssRuleRegex}#ix", $cssRule, $matches);
        $this->assertEquals($cssRule, $matches[0], $message);
    }

    public function cssRulesTokenData(): array
    {
        return [
            [
                'cssRules' => /** @lang CSS */ 'button,
html input[type="button"],
input[type="reset"],
input[type="submit"] {
	cursor: pointer;
	-webkit-appearance: button;
}

label,
select,
button,
input[type="button"],
input[type="reset"],
input[type="submit"],
input[type="radio"],
input[type="checkbox"] {
	cursor: pointer;
}

input[type="search"] {
	-webkit-box-sizing: content-box;
	-moz-box-sizing: content-box;
	box-sizing: content-box;
	-webkit-appearance: textfield;
}

input[type="search"]::-webkit-search-decoration,
input[type="search"]::-webkit-search-cancel-button {
	-webkit-appearance: none;
}

textarea {
	overflow: auto;
	vertical-align: top;
}',
                'message' => 'css rules'
            ],
                        [
                'cssRules' => /** @lang CSS */ 'button,
html input[type="button"],
input[type="reset"],
input[type="submit"] {
	cursor: pointer;
	-webkit-appearance: button;
}
/**
comment
*/

label,
select, /* comment */
button,
input[type="button"],
input[type="reset"],
input[type="submit"],
input[type="radio"],
input[type="checkbox"] {
	cursor: pointer; /* comment */
}

input[type="search"] {
	-webkit-box-sizing: content-box;
	-moz-box-sizing: content-box;
	box-sizing: content-box;
	/* comment */
	-webkit-appearance: textfield;
}
/* comment */
input[type="search"]::-webkit-search-decoration,
input[type="search"]::-webkit-search-cancel-button {
	-webkit-appearance: none;
}

textarea {
	overflow: auto;
	vertical-align: top;
}',
                'message' => 'with comments'
            ],
        ];
    }

    /**
     * @dataProvider cssRulesTokenData
     */
    public function testCssRulesToken($cssRules, $message)
    {
        $cssRulesListRegex = self::cssRulesListToken();

        preg_match("#{$cssRulesListRegex}?$#ix", $cssRules, $matches);
        $this->assertEquals($cssRules, $matches[0], $message);
    }

    public function testCssRegularAtRulesToken(): void
    {
        $atRulesRegex = self::cssRegularAtRulesToken();

        $this->assertEquals(1, preg_match("#{$atRulesRegex}#ix", '@charset "UTF-8";'), 'match charset');
        $this->assertEquals(
            1,
            preg_match(
                "#{$atRulesRegex}#ix",
                '@import url("bluish.css") print, screen;'
            ),
            'match import with url'
        );
        $this->assertEquals(
            1,
            preg_match(
                "#{$atRulesRegex}#ix",
                '@import "common.css" print, screen;'
            ),
            'match import with string'
        );
        $this->assertEquals(
            1,
            preg_match(
                "#{$atRulesRegex}#ix",
                '@namespace svg url(http://www.w3.org/2000/svg);'
            ),
            'match namespace'
        );
    }

    public function cssNestedAtRulesData(): array
    {
        return [
           [ 'css' => /** @lang CSS */ '@supports (display: flex) {
  @media screen and (min-width: 900px) {
    article {
      display: flex;
    }
  }
}',
            'message' => 'conditional at-rule'
               ]
        ];
    }

    /**
     * @dataProvider cssNestedAtRulesData
     */
    public function testCssNestedAtRulesToken($css, $message): void
    {
        $atRulesRegex = self::cssNestedAtRulesToken();

        preg_match("#{$atRulesRegex}#ix", $css, $matches);
        $this->assertEquals($css, $matches[0], $message);
    }
}
