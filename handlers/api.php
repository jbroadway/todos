<?php

$page->template = false;
header ('Content-Type: application/json');

// Avoid PHP's limited PUT and DELETE support
if (isset ($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
	$_SERVER['REQUEST_METHOD'] = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
}

$error = false;

switch ($_SERVER['REQUEST_METHOD']) {
	case 'POST':
		// create new item
		$obj = json_decode ($GLOBALS['HTTP_RAW_POST_DATA']);
		$obj->done = ($obj->done) ? 1 : 0;
		$todo = new Todo ($obj);
		$todo->put ();
		if ($todo->error) {
			$error = $todo->error;
		} else {
			$out = $todo->orig ();
			$out->done = ($out->done == 1) ? true : false;
		}
		break;
	case 'GET':
		if (isset ($this->params[0])) {
			// get one item
			$todo = new Todo ($this->params[0]);
			if ($todo->error) {
				$error = $todo->error;
			} else {
				$out = $todo->orig ();
				$out->done = ($out->done == 1) ? true : false;
			}
		} else {
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
			} else {
				$out = array ();
			}
		}
		break;
	case 'PUT':
		// update the specified item
		$todo = new Todo ($this->params[0]);
		$data = json_decode ($this->get_put_data ());
		$todo->text = $data->text;
		$todo->done = ($data->done) ? 1 : 0;
		$todo->order = $data->order;
		$todo->put ();
		if ($todo->error) {
			$error = $todo->error;
		} else {
			$out = $todo->orig ();
			$out->done = ($out->done == 1) ? true : false;
		}
		break;
	case 'DELETE':
		// delete the specified item
		$todo = new Todo ($this->params[0]);
		$todo->remove ();
		if ($todo->error) {
			$error = $todo->error;
		} else {
			$out = $this->params[0];
		}
		break;
}

// output
if ($error) {
	error_log (sprintf ('%s %s: %s', $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $error));
	$out = false;
} else {
	$res = $out;
}

echo json_encode ($out);

?>