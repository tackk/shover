<?php
namespace Tackk\Shover\Transport;

use Tackk\Shover\AuthenticationException;
use Tackk\Shover\ForbiddenException;
use Tackk\Shover\GeneralException;
use Tackk\Shover\Request;

class Blackhole extends AbstractTransport
{
    /**
     * Noop a request
     *
     * @param  Request $request The Request
     * @return array The Response array
     * @throws AuthenticationException
     * @throws ForbiddenException
     * @throws GeneralException
     */
    public function dispatch(Request $request)
    {
        $this->marshal($request);

        return [];
    }
}
