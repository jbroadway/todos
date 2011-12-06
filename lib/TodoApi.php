<?php

class TodoApi extends Restful {
	/**
	 * Don't wrap output in `{"success":bool,"data":"..."}`.
	 */
	var $wrap = false;

	/**
	 * Create a new item.
	 */
	public function post_item () {
		$obj = $this->get_raw_post_data (true);
		$obj->done = ($obj->done) ? 1 : 0;
		$todo = new Todo ($obj);
		if (! $todo->put ()) {
			return $this->error ($todo->error);
		}
		$out = $todo->orig ();
		$out->done = ($out->done == 1) ? true : false;
		return $out;
	}

	/**
	 * Get one or more items.
	 */
	public function get_item ($id = false) {
		if ($id) {
			// get one item
			$todo = new Todo ($id);
			if ($todo->error) {
				return $this->error ($todo->error);
			}
			$out = $todo->orig ();
			$out->done = ($out->done == 1) ? true : false;
			return $out;
		}

		// get all items
		$out = array ();
		$list = Todo::query ()
			->order ('`order` asc')
			->fetch_orig ();

		if (is_array ($list)) {
			foreach ($list as $item) {
				$item->done = ($item->done == 1) ? true : false;
				$out[] = $item;
			}
		}
		return $out;
	}

	/**
	 * Update the specified item.
	 */
	public function put_item ($id) {
		$todo = new Todo ($id);
		$data = $this->get_put_data (true);
		$todo->text = $data->text;
		$todo->done = ($data->done) ? 1 : 0;
		$todo->order = $data->order;
		if (! $todo->put ()) {
			return $this->error ($todo->error);
		}
		$out = $todo->orig ();
		$out->done = ($out->done == 1) ? true : false;
		return $out;
	}

	/**
	 * Delete the specified item.
	 */
	public function delete_item ($id) {
		$todo = new Todo ($id);
		if (! $todo->remove ()) {
			return $this->error ($todo->error);
		}
		return $id;
	}

	/**
	 * Overriding `error()` to log info.
	 */
	public function error ($msg) {
		error_log (sprintf ('%s %s: %s', $this->controller->request_method (), $_SERVER['REQUEST_URI'], $msg));
		return false;
	}
}

?>