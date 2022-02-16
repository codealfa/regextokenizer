<?php

/**
 * @package   codealfa/regextokenizer
 * @author    Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2020 Samuel Marshall
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace CodeAlfa\RegexTokenizer\Debug;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * Trait Debug  - To use the Debug trait you must add a PSR-3 compliant Logger to the class using this trait
 *
 * @package CodeAlfa\RegexTokenizer\Debug
 */
trait Debug
{
	use LoggerAwareTrait;

	public $_debug = true;
	/**DO NOT ENABLE on production sites!! **/
	public $_regexNum = -1;
	public $_limit = 1.0;
	public $_printCode = true;
	protected $_ip = '';

	public function _debug( $regex, $code, $regexNum = 0 )
	{
		if ( ! $this->_debug )
		{
			return false;
		}

		if ( is_null( $this->logger ) )
		{
			$this->setLogger( new NullLogger() );
		}

		/** @var float $pstamp */
		static $pstamp = 0;

		if ( $pstamp === 0 )
		{
			$pstamp = microtime( true );

			return true;
		}

		$nstamp = microtime( true );
		$time   = ( $nstamp - $pstamp ) * 1000;

		if ( $time > $this->_limit )
		{
			$context = [ 'category' => 'Regextokenizer' ];

			$this->logger->debug( 'regexNum = ' . $regexNum, $context );
			$this->logger->debug( 'time = ' . (string)$time, $context );

			if ( $this->_printCode )
			{
				$this->logger->debug( 'regex = ' . $regex, $context );
				$this->logger->debug( 'code = ' . $code, $context );
			}
		}

		$pstamp = $nstamp;
	}
}