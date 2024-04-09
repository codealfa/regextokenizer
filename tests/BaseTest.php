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

use CodeAlfa\RegexTokenizer\Base;
use PHPUnit\Framework\TestCase;

use function preg_match;

class BaseTest extends TestCase
{
    use Base;

    public function doubleStringData(): array
    {
        return [
            'normal string' => [
                'string' => '"It\'s a string"',
                'message' => 'normal string'
            ],
            'escaped double quote' => [
                'string' => '"This is a \"string\""',
                'message' => 'escaped double string'
            ],
            'string with line end' => [
                'string' => '"This is a 
                 string"',
                'message' => 'string with line end'
            ],
            'string with backslash' => [
                'string' => '"This may be a \\\\ string"',
                'message' => 'string with backlash'
            ],
            'open string' => [
                'string' => '"This is a string',
                'message' => 'open string'
            ],
            'empty string' => [
                'string' => '""',
                'message' => 'empty string'
            ]
        ];
    }

    /**
     * @dataProvider doubleStringData
     */
    public function testDoubleQuoteString(string $string, string $message): void
    {
        $ds = self::doubleQuoteStringToken();
        $regex = "#{$ds}#ix";

        preg_match($regex, $string, $matches);
        $this->assertEquals($string, $matches[0], $message);
    }

    public function singleQuoteStringData(): array
    {
        return [
            'normal string' => [
                'string' => "'This is a string.'",
                'message' => 'normal string'
            ],
            'escaped single quote' => [
                'string' => "'It\'s a string'",
                'message' => 'escaped single quote'
            ],
            'string with line end' => [
                'string' => "'This is 
                a string",
                'message' => 'string with line end'
            ],
            'string with backslash' => [
                'string' => "'This may be a \\\\ string'",
                'message' => 'string with backslash'
            ],
            'open string' => [
                'string' => "'This is a string",
                'message' => 'open string'
            ],
            'empty string' => [
                'string' => "''",
                'message' => 'empty string'
            ]
        ];
    }

    /**
     * @dataProvider singleQuoteStringData
     */
    public function testSingleQuoteString(string $string, string $message): void
    {
        $ss = self::singleQuoteStringToken();
        $regex = "#{$ss}#ix";

        preg_match($regex, $string, $matches);
        $this->assertEquals($string, $matches[0], $message);
    }

    public function backTickStringData(): array
    {
        return [
            'normal string' => [
                'string' => '`It\'s a string`',
                'message' => 'normal string'
            ],
            'escaped double quote' => [
                'string' => '`This is a \`string\``',
                'message' => 'escaped double string'
            ],
            'string with line end' => [
                'string' => '`This is a 
                 string`',
                'message' => 'string with line end'
            ],
            'string with backslash' => [
                'string' => '`This may be a \\\\ string`',
                'message' => 'string with backlash'
            ],
            'open string' => [
                'string' => '`This is a string',
                'message' => 'open string'
            ],
            'empty string' => [
                'string' => '``',
                'message' => 'empty string'
            ]
        ];
    }

    /**
     * @dataProvider backTickStringData
     */
    public function testBackTickQuoteString(string $string, string $message): void
    {
        $bs = self::backTickStringToken();
        $regex = "#{$bs}#ix";

        preg_match($regex, $string, $matches);
        $this->assertEquals($string, $matches[0], $message);
    }

    public function blockCommentData(): array
    {
        return [
            'normal comment' => [
                'comment' => '/* comment */',
                'message' => 'normal comment'
            ],
            'comment with asterisk' => [
                'comment' => '/* comment ** comment */',
                'message' => 'comment with asterisk'
            ],
            'multiline comment' => [
                'comment' => '/*
                comment
                comment
                */',
                'message' => 'multiline comment'
            ],
            'empty comment' => [
                'comment' => '/**/',
                'message' => 'empty comment'
            ]
        ];
    }

    /**
     * @dataProvider blockCommentData
     */
    public function testBlockComment(string $comment, $message): void
    {
        $bc = self::blockCommentToken();
        $regex = "#{$bc}#ix";

        preg_match($regex, $comment, $matches);
        $this->assertEquals($comment, $matches[0], $message);
    }

    public function testLineComment(): void
    {
        $lc = self::lineCommentToken();
        $regex = "#{$lc}#ix";

        $string = '// line comment
        2nd comment
        ';
        preg_match($regex, $string, $matches);
        $this->assertEquals('// line comment', $matches[0], 'line comment');
    }
}
