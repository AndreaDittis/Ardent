<?php

namespace Ardent;


final
class OuterEnumerable implements Enumerable {

	private $inner;

	public
	function __construct(\Iterator $inner) {
		$this->inner = new CountedIteratorAggregate($inner);
	}

	public
	function count(): int {
		return $this->inner->count();
	}

	public
	function getIterator(): \Iterator {
		return $this->inner->getIterator();
	}

	/**
	 * @param callable $f
	 * @return OuterEnumerable
	 */
	public
	function filter(callable $f) {
		return new self(Algorithm\filter($f, $this->inner));
	}

	/**
	 * @param callable $f
	 * @return OuterEnumerable
	 */
	public
	function map(callable $f) {
		return new self(Algorithm\map($f, $this->inner));
	}

	public
	function reduce(callable $f): Optional {
		return Algorithm\reduce($f, $this->inner);
	}

	public
	function fold(callable $f, $initial): Optional {
		return Algorithm\fold($f, $initial, $this->inner);
	}

	public
	function skip(int $n) {
		return new self(Algorithm\skip($n, $this->inner));
	}

	public
	function limit(int $n) {
		return new self(Algorithm\limit($n, $this->inner));
	}

	public
	function to(Collection\Builder $builder) {
		foreach ($this->inner as $key => $value) {
			$builder->add($key, $value);
		}
		return $builder->result();
	}

	public
	function toSequentialArray(): array {
		return \iterator_to_array($this->inner, $preserve_keys = true);
	}

	public
	function choose(callable $f) {
		return new self(Algorithm\choose($f, $this->inner));
	}

	public
	function isEmpty(): bool {
		return $this->inner->count() === 0;
	}

	public
	function flatten() {
		return new self(Algorithm\flatten($this->inner));
	}

}

