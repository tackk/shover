<?php
namespace Tackk\Pushit;

use Exception;

class GeneralException extends Exception { }
class AuthenticationException extends GeneralException { }
class ForbiddenException extends GeneralException { }
