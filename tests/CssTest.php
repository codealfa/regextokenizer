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
            '.class' => [
                'cssRule' => /** @lang CSS */ '.intro{display:block;}',
                'selector' => '.intro',
                'message' => '.class'
            ],
            '.class1.class2' =>      [
                'cssRule' => /** @lang CSS */ '.name1.name2 /*comment*/ {display:block;}',
                'selector' => '.name1.name2 /*comment*/ ',
                'message' => '.class1.class2'
            ],
            '.class1 .class2' => [
                'cssRule' => /** @lang CSS */ '.name1 .name2 {display:block;}',
                'selector' => '.name1 .name2 ',
                'message' => '.class1 .class2'
            ],
            '#id' => [
                'cssRule' => /** @lang CSS */ '#firstname{display:block;}',
                'selector' => '#firstname',
                'message' => '#id'
            ],
            '*' => [
                'cssRule' => /** @lang CSS */ '* {display:block;}',
                'selector' => '* ',
                'message' => '*'
            ],
            'element' => [
                'cssRule' => /** @lang CSS */ 'p{display:block;}',
                'selector' => 'p',
                'message' => 'element'
            ],
            'element.class' => [
                'cssRule' => /** @lang CSS */ 'p.intro{display:block;}',
                'selector' => 'p.intro',
                'message' => 'element.class'
            ],
            'element,element' => [
                'cssRule' => /** @lang CSS */ 'div, /* comment */ p{display:block;}',
                'selector' => 'div, /* comment */ p',
                'message' => 'element,element'
            ],
            'element element' => [
                'cssRule' => /** @lang CSS */ 'div p{display:block;}',
                'selector' => 'div p',
                'message' => 'element element'
            ],
            'element>element' => [
                'cssRule' => /** @lang CSS */ 'div > p{display:block;}',
                'selector' => 'div > p',
                'message' => 'element>element'
            ],
            'element+element' => [
                'cssRule' => /** @lang CSS */ 'div + p {display:block;}',
                'selector' => 'div + p ',
                'message' => 'element+element'
            ],
            'element~element' => [
                'cssRule' => /** @lang CSS */ 'p ~ ul{display:block;}',
                'selector' => 'p ~ ul',
                'message' => 'element1~element2'
            ],
            '[attribute]' => [
                'cssRule' => /** @lang CSS */ '[target]{display:block;}',
                'selector' => '[target]',
                'message' => '[attribute]'
            ],
            'attribute=value' => [
                'cssRule' => /** @lang CSS */ '[target="_blank"]{display:block;}',
                'selector' => '[target="_blank"]',
                'message' => 'attribute=value'
            ],
            'element~=value' => [
                'cssRule' => /** @lang CSS */ '[title~=\'flower\']{display:block;}',
                'selector' => '[title~=\'flower\']',
                'message' => 'element~=value'
            ],
            'attribute|=value' => [
                'cssRule' => /** @lang CSS */ '[lang|="en"]{display:block;}',
                'selector' => '[lang|="en"]',
                'message' => 'attribute|=value'
            ],
            'attribute^=value' => [
                'cssRule' => /** @lang CSS */ 'a[href^=https]{display:block;}',
                'selector' => 'a[href^=https]',
                'message' => 'attribute^=value'
            ],
            'attribute$=value' => [
                'cssRule' => /** @lang CSS */ 'a[href$=".pdf"]{display:block;}',
                'selector' => 'a[href$=".pdf"]',
                'message' => 'attribute$=value'
            ],
            'attribute*=value' => [
                'cssRule' => /** @lang CSS */ 'a[href*="w3schools"]{display:block;}',
                'selector' => 'a[href*="w3schools"]',
                'message' => 'attribute*=value'
            ],
            ':active' => [
                'cssRule' => /** @lang CSS */ 'a:active{display:block;}',
                'selector' => 'a:active',
                'message' => ':active'
            ],
            '::after' => [
                'cssRule' => /** @lang CSS */ 'p::after{display:block;}',
                'selector' => 'p::after',
                'message' => '::after'
            ],
            ':first-child' => [
                'cssRule' => /** @lang CSS */ 'p:first-child{display:block;}',
                'selector' => 'p:first-child',
                'message' => ':first-child'
            ],
            ':lang(language)' => [
                'cssRule' => /** @lang CSS */ 'p:lang(it){display:block;}',
                'selector' => 'p:lang(it)',
                'message' => ':lang(language)'
            ],
            ':not(p)' => [
                'cssRule' => /** @lang CSS */ ':not(p){display:block;}',
                'selector' => ':not(p)',
                'message' => ':not(selector)'
            ],
            'nth-child(2)' => [
                'cssRule' => /** @lang CSS */ 'p:nth-child(2){display:block;}',
                'selector' => 'p:nth-child(2)',
                'message' => 'nth-child(2)'
            ],
            '#id:target' => [
                'cssRule' => /** @lang CSS */ '#news:target{display:block;}',
                'selector' => '#news:target',
                'message' => '#id:target'
            ],
            'escaped selector' => [
                'cssRule' => /** @lang CSS */ '.foo\:bar{display:block;}',
                'selector' => '.foo\:bar',
                'message' => 'escaped selector'
            ],
            'another escaped selector' => [
                'cssRule' => /** @lang CSS */ '.\31 234{display:block;}',
                'selector' => '.\31 234',
                'message' => 'another escaped selector'
            ],
            'nesting rule' => [
                'cssRule' => /** @lang CSS */ 'label {font-family: system-ui; input{border: blue 2px dashed;}}',
                'selector' => 'label ',
                'message' => 'nesting rule'
            ]
        ];
    }

    /**
     * @dataProvider cssSelectorListData
     */
    public function testCssSelectorsListToken($cssRule, $selector, $message): void
    {
        $selectorsList = self::cssSelectorListToken();

        preg_match("#{$selectorsList}#ix", $cssRule, $matches);
        $this->assertEquals($selector, $matches[0], $message);
    }

    public function cssDeclarationsListData(): array
    {
        return [
            [
                'cssRule' => /** @lang CSS */ '#news:target{display:block;}',
                'declaration' => 'display:block;',
                'message' => 'no comment'
            ],
            [
                'cssRule' => /** @lang CSS */ '#news:target{/*comment*/display:block;}',
                'declaration' => '/*comment*/display:block;',
                'message' => 'comment before'
            ],
            [
                'cssRule' => /** @lang CSS */ '#news:target{display: /*comment*/ block;}',
                'declaration' => 'display: /*comment*/ block;',
                'message' => 'comment inside'
            ],
            [
                'cssRule' => /** @lang CSS */ '#news:target{display:block; /*comment*/}',
                'declaration' => 'display:block; /*comment*/',
                'message' => 'comment after'
            ],
            [
                'cssRule' => /** @lang CSS */'p { font-family: \C7 elikfont; }',
                'declaration' => ' font-family: \C7 elikfont; ',
                'message' => 'escaped declaration'
            ],
            [
                'cssRule' => /** @lang CSS */ 'div {border-image: url("/media/diamonds.png") 30 fill / 30px / 30px space;}',
                'declaration' => 'border-image: url("/media/diamonds.png") 30 fill / 30px / 30px space;',
                'message' => 'css url'
            ],
            [
                'cssRule' => /** @lang CSS */ 'div {background: center / contain no-repeat url("../../media/examples/firefox-logo.svg"),
            #eee 35% url("../../media/examples/lizard.png");}',
                'declaration' => 'background: center / contain no-repeat url("../../media/examples/firefox-logo.svg"),
            #eee 35% url("../../media/examples/lizard.png");',
                'message' => 'background'
            ],
            [
                'cssRule' => /** @lang CSS */ 'div {shape-image-threshold: 70%;
shape-image-threshold: 0.7;}',
                'declaration' => 'shape-image-threshold: 70%;
shape-image-threshold: 0.7;',
                'message' => 'shape-image threshold'
            ],
            [
                'cssRule' => /** @lang CSS */ 'div:nth-child(4) {lch(from blue calc(l + 20) c h)}',
                'declaration' => 'lch(from blue calc(l + 20) c h)',
                'message' => 'color'
            ],
            'empty' => [
                'cssRule' => /** @lang CSS */ 'div {}',
                'declaration' => '',
                'message' => 'empty'
            ],
            'starting-style' => [
                'cssRule' => /** @lang CSS */ '#target {
  transition-property: opacity, display;
  transition-duration: 0.5s;
  display: block;
  opacity: 1;
  @starting-style {
    opacity: 0;
  }
}',
                'declaration' => '
  transition-property: opacity, display;
  transition-duration: 0.5s;
  display: block;
  opacity: 1;
  @starting-style {
    opacity: 0;
  }
',
                'message' => 'starting-style'
            ],
            'nesting rule' => [
                'cssRule' => /** @lang CSS */ 'label {font-family: system-ui; input{border: blue 2px dashed;}}',
                'declaration' => 'font-family: system-ui; input{border: blue 2px dashed;}',
                'message' => 'nesting rule'
            ]
        ];
    }

    /**
     * @dataProvider cssDeclarationsListData
     */
    public function testCssDeclarationListToken($cssRule, $declaration, $message): void
    {
        $declarationsList = self::cssDeclarationListToken();

        preg_match("#{$declarationsList}#ix", $cssRule, $matches);
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
            ],
            [
                'cssRule' => /** @lang */ 'blockquote::after {
  display: block;
  content: \' (source: \' attr(cite) \') \';
  color: hotpink;
}',
                'message' => 'attr'
            ],
            [
                'nesting Css rule' => <<<CSS
label {
    font-family: system-ui; 
    input {
        border: blue 2px dashed;
    }
}
CSS,
                'message' => 'nesting CSS rule'

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
    public function testCssRulesToken(string $cssRules, string $message): void
    {
        $cssRulesListRegex = self::cssRuleListToken();
        $cssString = self::cssStringToken();

        preg_match("#{$cssRulesListRegex}$#ix", $cssRules, $matches);
        $this->assertEquals($cssRules, $matches[0], $message);
    }

    public function cssRegularAtRulesData(): array
    {
        return [
            'import' => [
                'css' => '@import url("bluish.css") print, screen;',
                'message' => 'import'
            ],
            'namespace' => [
                'css' => '@namespace svg url(http://www.ws.org/200/svg);',
                'message' => 'namespace'
            ],
            'layer' => [
                'css' => '@layer module, state;',
                'message' => 'layer'
            ]
        ];
    }
    /**
     * @dataProvider cssRegularAtRulesData
     */
    public function testCssRegularAtRulesToken(string $css, string $message): void
    {
        $atRulesRegex = self::cssRegularAtRulesToken();
        preg_match("#$atRulesRegex#ix", $css, $matches);
        $this->assertEquals($css, $matches[0], $message . '_regular');

        $atNamedRulesRegex = self::cssRegularAtRulesToken($message);
        preg_match("#$atNamedRulesRegex#ix", $css, $matches);
        $this->assertEquals($css, $matches[0], $message . '_named');

        $this->assertEquals(
            1,
            preg_match(
                "#{$atRulesRegex}#ix",
                '@import "common.css" print, screen;'
            ),
            'import with string'
        );

        $this->assertEquals(
            0,
            preg_match(
                "#{$atRulesRegex}#ix",
                '@supports (display: flex) {
  .flex-container > * {
    text-shadow: 0 0 2px blue;
    float: none;
  }

  .flex-container {
    display: flex;
  }
}',
            ),
            'nested rule'
        );
    }

    public function cssNestedAtRulesData(): array
    {
        return [
            'media' => [
                'css' => /** @lang CSS  */ '@media (400px <= width <= 700px) {
  body {
    line-height: 1.4;
  }
}',
                'message' => 'media'
            ],
           'supports' => [
               'css' => /** @lang CSS */ '@supports (display: flex) {
  @media screen and (min-width: 900px) {
    article {
      display: flex;
    }
  }
}',
            'message' => 'supports'
               ],
            'scope' => [
                'css' => /** @lang CSS */'@scope (.article-body) to (figure) {
  img {
    border: 5px solid black;
    background-color: goldenrod;
  }
}',
                'message' => 'scope'
           ],
            'starting-style' => [
                'css' => /** @lang CSS */ '@starting-style {
  [popover]:popover-open {
    opacity: 0;
    transform: scaleX(0);
  }
}',
                'starting-style'
            ],
            'document' => [
                'css' => /** @lang CSS */ '@document url(https://www.example.com/page@2/)
{
  h1 {
    color: green;
  }
}',
                'document'
            ],
            'page' => [
                'css' => /** @lang CSS */ '@page :right {
  size: 11in;
  margin-top: 4in;
}',
                'message' => 'page'
            ],
            'font-face' => [
                'css' => /** @lang CSS */ '@font-face {
  font-family: "Trickster";
  src:
    local("Trickster"),
    url("trickster-COLRv1.otf") format("opentype") tech(color-COLRv1),
    url("trickster-outline.otf") format("opentype"),
    url("trickster-outline.woff") format("woff");
}',
               'message' => 'font-face'
            ],
            'keyframes' => [
                'css' =>  /** @lang CSS */ '@keyframes slidein {
  from {
    transform: translateX(0%);
  }

  to {
    transform: translateX(100%);
  }
}',
            'message' => 'keyframes'
            ],
            'counter-style' => [
                'css' => /** @lang CSS */ '@counter-style thumbs {
  system: cyclic;
  symbols: "\1F44D";
  suffix: " ";
}',
                'message' => 'counter-style'
            ],
            'font-feature-values' => [
                'css' => /** @lang CSS */ '@font-feature-values Font One {
  @styleset {
    nice-style: 12;
  }
}',
                'message' => 'font-feature-values'
            ],
            'property' => [
                'css' => /** @lang CSS */ '@property --property-name {
  syntax: "<color>";
  inherits: false;
  initial-value: #c0ffee;
}',
                'message' => 'property'
            ],
            'layer' => [
                'css' =>  /** @lang CSS */ '@layer module {
  .alert {
    border: medium solid violet;
    background-color: yellow;
    color: white;
  }
}',
                'message' => 'layer'
            ]
        ];
    }

    /**
     * @dataProvider cssNestedAtRulesData
     */
    public function testCssNestedAtRulesToken(string $css, string $message): void
    {
        $atRulesRegex = self::cssNestingAtRulesToken();

        preg_match("#{$atRulesRegex}#ix", $css, $matches);
        $this->assertEquals($css, $matches[0], $message . '_nested');

        $atNamesRulesRegex = self::cssNestingAtRulesToken($message);

        preg_match("#{$atNamesRulesRegex}#ix", $css, $matches);
        $this->assertEquals($css, $matches[0], $message . '_named');
    }

    public function cssStringData(): array
    {
        return [
            'bootstrap' => [
                'name' => 'bootstrap'
            ],
            'bulma' => [
                'name' => 'bulma'
            ],
            'foundation' => [
                'name' => 'foundation'
            ],
            'uikit' => [
                'name' => 'uikit'
            ],
            'pico' => [
                'name' => 'pico'
            ],
            'cirrus' => [
                'name' => 'cirrus'
            ],
            'vanilla-framework' => [
                'name' => 'vanilla-framework'
            ]
        ];
    }

    /**
     * @dataProvider cssStringData
     */
    public function testCssStringToken(string $name): void
    {
        $cssStringRegex = self::cssStringToken();

        $css = $this->getCss($name);
        preg_match("#^{$cssStringRegex}+$#ix", $css, $matches);
        $this->assertEquals($css, $matches[0], $name . '.css');

        $cssMin = $this->getCssMin($name);
        preg_match("#{$cssStringRegex}#ix", $cssMin, $matches);
        $this->assertEquals($cssMin, $matches[0], $name . '.min.css');
    }

    private function getCss(string $name): string
    {
        return file_get_contents(__DIR__ . "/_data/css/{$name}.css");
    }

    private function getCssMin(string $name): string
    {
        return file_get_contents(__DIR__ . "/_data/css/min/{$name}.min.css");
    }
}
