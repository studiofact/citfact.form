<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\Extension;

class IdentifierExtension
{
    /**
     * @var string
     */
    protected $paramsHash;
    
    /**
     * @param string $paramsHash
     */
    public function __construct($paramsHash)
    {
        $this->paramsHash = (string) $paramsHash;
    }
    
    /**
     * Generates a identifier
     *
     * @return string
     */
    public function generateIdentifier()
    {
        return $this->getIdentifier();
    }

    /**
     * Validates a identifier token
     *
     * @param string $identifier
     * @return bool
     */
    public function isIdentifierValid($identifier)
    {
        return $identifier === $this->generateIdentifier();
    }

    /**
     * Return identifier token
     *
     * @return string
     */
    protected function getIdentifier()
    {
        $caller = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS) : debug_backtrace(false);

        $traceCount = count($caller);
        $traceCurrent = $traceCount - 1;

        for ($i = 0; $i < $traceCount; $i++) {
            if (strtolower($caller[$i]['function']) == 'includecomponent' && (($c = strtolower($caller[$i]['class'])) == 'callmain' || $c == 'cmain')) {
                $traceCurrent = $i;
                break;
            }
        }
        
        return (string) abs(crc32(sprintf('%s_%s_%s', 
            $caller[$traceCurrent]['file'], 
            $caller[$traceCurrent]['line'],
            $this->paramsHash
        )));
    }
}