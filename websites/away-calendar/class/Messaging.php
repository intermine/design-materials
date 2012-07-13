<?php
/**
 * System messaging through SESSION
 *
 * User: radek
 * Date: 17/06/11
 * Time: 17:07
 */

session_start();

class Messaging {

    /**#@+ session storage namespace */
	const SESSION_STORAGE = 'AwayPlanner\Message\\';
    /**#@-*/

	/**
	 * Set a message under warning status.
	 * @param string $message Message to save
	 */
	public static function warning($message) {
		self::setMessage(array('type' => 'warning', 'text' => $message));
	}

	/**
	 * Set a message under alert status.
	 * @param string $message Message to save
	 * @return void
	 */
	public static function alert($message) {
		self::setMessage(array('type' => 'alert', 'text' => $message));
	}

	/**
	 * Set a message under success status.
	 * @param string $message Message to save
	 * @return void
	 */
	public static function ok($message) {
		self::setMessage(array('type' => 'ok', 'text' => $message));
	}

	/**
	 * Will return an array of messages to be flashed to the user in View.
	 * @return array Messages with status/message fields if we have some
	 */
	public static function get() {
		$messages = array();

		// traverse the whole session looking for messages
		foreach ($_SESSION as $key => $value) {
			// our messages
			if (strstr($key, self::SESSION_STORAGE)) {
				// 'save' message to the array
				array_push($messages, $value);
				// 'delete' the message
				unset($_SESSION[$key]);
			}
		}

		// return
		if (!empty($messages)) return $messages;
	}

	/**
	 * Will set a message in the session not overwriting current contents.
	 * @param array $message Array message with status/text to save
	 * @param int $messagesPointer Used when saving more messages, leave off
	 * @return void
	 */
	private static function setMessage(array $message, $messagesPointer=0) {
		// if a message is already set at this pointer...
		if (isset($_SESSION[self::SESSION_STORAGE . '\\' . $messagesPointer])) {
			// set in the next available slot
			$messagesPointer++;
			self::setMessage($message, $messagesPointer);
		} else {
			// save message
			$_SESSION[self::SESSION_STORAGE . '\\' . $messagesPointer] = $message;
			return;
		}
	}

}
?>
<style>
    ul#messages { list-style-type:none; font-size:15px; font-family:sans-serif; }
    ul#messages li { padding:4px; margin-bottom:5px; }
    ul#messages li.ok { border:1px solid #4AE371; background:#BDF4CB; }
    ul#messages li.warning { border:1px solid #DFE32D; background:#F5F7C4; }
    ul#messages li.alert { border:1px solid #FD9696; background:#FFBBBB; }
</style>
