<?php
class PriorityQueue {
	// array of array($priority, $payload)
	// left child of A[i] is A[2i + 1]
	// right child of A[i] is A[2i + 2]
	// parent of A[i] is A[floor((i-1)/2)]
	private $arr = array();
	private function bubble_up($idx) {
		while($idx > 0) {
			$parent = floor(($idx-1)/2);
			if($this->arr[$parent][0] < $this->arr[$idx][0]) {
				$tmp = $this->arr[$parent];
				$this->arr[$parent] = $this->arr[$idx];
				$this->arr[$idx] = $tmp;
				$idx = $parent;
			} else {
				break;
			}
		}
	}
	private function bubble_down($idx) {
		while(($left = 2*$idx+1) < count($this->arr)) {
			$right = 2*$idx+2;
			$maxchild = $this->arr[$left][0] > $this->arr[$right][0] ? $left : $right;
			if($this->arr[$idx][0] < $this->arr[$maxchild][0]) {
				$tmp = $this->arr[$idx];
				$this->arr[$idx] = $this->arr[$maxchild];
				$this->arr[$maxchild] = $tmp;
				$idx = $maxchild;
			} else {
				break;
			}
		}
	}
	public function __construct() { }
	// O(log n)
	public function insertUnordered($priority, $payload) {
		$this->arr[count($this->arr)] = array($priority, $payload);
		self::bubble_up(count($this->arr)-1);
	}
	// O(n)
	public function insert($priority, $payload) {
		$idx = count($this->arr);
		$this->arr[$idx] = array($priority, $payload);
		while($idx > 0 && $this->arr[$idx][0] > $this->arr[$idx-1][0]) {
			$tmp = $this->arr[$idx-1];
			$this->arr[$idx-1] = $this->arr[$idx];
			$this->arr[$idx] = $tmp;
			$idx--;
		}
	}
	public function top() {
		return count($this->arr) > 0 ? $this->arr[0][1] : false;
	}
	// O(log n)
	public function popUnordered() {
		$ret = self::top();
		$this->arr[0] = $this->arr[count($this->arr)-1];
		unset($this->arr[count($this->arr)-1]);
		self::bubble_down(0);
		return $ret;
	}
	// O(n)
	public function pop() {
		$ret = self::top();
		for($i=1; $i < count($this->arr); $i++) {
			$this->arr[$i-1] = $this->arr[$i];
		}
		unset($this->arr[count($this->arr)-1]);
		return $ret;
	}
	public function renderInternalRepresentation() {
		echo "<br>State:<br>";
		var_dump($this->arr);
		echo "<br>";
	}
	public function size() {
		return count($this->arr);
	}
}
