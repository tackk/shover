<?php
namespace Tackk\Pushit;

interface ConnectionInterface {
	public function dispatch(Request $request);
}
