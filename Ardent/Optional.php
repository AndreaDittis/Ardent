<?php

namespace Ardent;

use Countable;
use IteratorAggregate;


final
class Optional implements Countable, IteratorAggregate {

	private $has_value;
	private $value;

	private
	function __construct($has_value, $value) {
		$this->has_value = $has_value;
		$this->value = $value;
	}

	public static
	function some($thing) {
		return new Optional(true, $thing);
	}

	public static
	function none() {
		static $none = null;
		if ($none === null) {
			$none = new Optional(false, null);
		}
		return $none;
	}

	public static
	function fromMaybeNull($value) {
		return ($value !== null)
			? Optional::some($value)
			: Optional::none();
	}

	public static
	function fromMaybeFalse($value) {
		return ($value !== false)
			? Optional::some($value)
			: Optional::none();
	}

	public static
	function fromPredicate(callable $f, $value) {
		return $f($value)
			? Optional::some($value)
			: Optional::none();
	}

	public
	function count() {
		return ($this->has_value ? 1 : 0);
	}

	public
	function filter(callable $f) {
		return ($this->has_value && $f($this->value))
			? $this
			: Optional::none();
	}

	public
	function getIterator() {
		if ($this->has_value) {
			yield $this->value;
		}
	}

	public
	function map(callable $f) {
		return ($this->has_value)
			? Optional::some($f($this->value))
			: Optional::none();
	}

	public
	function match(callable $ifSome, callable $ifNone) {
		return ($this->has_value)
			? $ifSome($this->value)
			: $ifNone();
	}

	public
	function unwrap() {
		if (!$this->has_value) {
			throw new \RuntimeException();
		}

		return $this->value;
	}

}

