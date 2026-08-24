<?php

declare(strict_types=1);

/**
 * @package   codealfa/regextokenizer
 * @author    Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2020 Samuel Marshall
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace CodeAlfa\RegexTokenizer;

use CodeAlfa\RegexTokenizer\Debug\Profiler;
use Exception;

trait Base
{
    use Profiler;

    /**
     * Regex token for a string inside double quotes
     */
    //language=RegExp
    public static function doubleQuoteStringToken(): string
    {
        return '"(?>[^"\\\\]++|\\\\.)*+(?>"|$)';
    }

    /**
     * Regex token for a string enclosed by single quotes
     */
    //language=RegExp
    public static function singleQuoteStringToken(): string
    {
        return "'(?>[^'\\\\]++|\\\\.)*+(?>'|$)";
    }

    /**
     * Regex token for a string enclosed by back ticks
     */
    //language=RegExp
    public static function backTickStringToken(): string
    {
        return '`(?>[^`\\\\]++|\\\\.)*+(?>`|$)';
    }

    /**
     * Regex token for block or line comments
     */
    //language=RegExp
    public static function commentToken(): string
    {
        return '(?>' . self::blockCommentToken() . '|' . self::lineCommentToken() . ')';
    }

    /**
     * Regex token for block comment
     */
    //language=RegExp
    public static function blockCommentToken(): string
    {
        return '/\*(?>[^*]++|\*)*?\*/';
    }

    /**
     * Regex token for line comment
     */
    public static function lineCommentToken(): string
    {
        return '//[^\r\n]*+';
    }

    /**
     * Will throw an exception when a PHP preg error is encountered.
     *
     * @throws Exception
     */
    protected static function throwExceptionOnPregError(): void
    {
        $error = preg_last_error();

        if ($error === PREG_NO_ERROR) {
            return;
        }

        $pcreConstants = get_defined_constants(true)['pcre'] ?? [];

        $errorMap = array_flip(
            array_filter(
                $pcreConstants,
                static function (string $name): bool {
                    return str_ends_with($name, '_ERROR');
                },
                ARRAY_FILTER_USE_KEY
            )
        );

        $name = $errorMap[$error] ?? ('PREG_UNKNOWN_ERROR_' . $error);

        throw new Exception($name);
    }
}
