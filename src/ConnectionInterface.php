<?php
namespace Tackk\Shover;

interface ConnectionInterface {
	public function dispatch(Request $request);
}
