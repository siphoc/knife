<?php

/**
 * In this file we store all generic functions that we will be using in the subname module
 *
 * @package		backend
 * @subpackage	subname
 *
 * @author		authorname
 * @since		versionname
 */
class BackendclassnameModel
{
	/**
	 * Deletes an item
	 *
	 * @return	void
	 * @param	int $id		The id of the item to delete.
	 */
	public static function delete($id)
	{
		BackendModel::getDB(true)->delete('subname', 'id = ?', (int) $id);
	}


	/**
	 * Checks if an item exists
	 *
	 * @return	bool
	 * @param	int $id		The item id.
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(i.id)
														FROM subname AS i
														WHERE i.id = ?',
														(int) $id);
	}


	/**
	 * Fetches an item
	 *
	 * @return	array
	 * @param	int $id		The id of the item to fetch.
	 */
	public static function get($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*
															FROM subname AS i
															WHERE i.id = ?',
															(int) $id);
	}


	/**
	 * Retrieve the unique URL for an item
	 *
	 * @return	string
	 * @param	string $URL			The URL to base on.
	 * @param	int[optional] $id	The id of the item to ignore.
	 */
	public static function getURL($URL, $id = null)
	{
		// redefine URL
		$URL = SpoonFilter::urlise((string) $URL);

		// get db
		$db = BackendModel::getDB();

		// new item
		if($id === null)
		{
			// get number of categories with this URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
											FROM subname AS i
											INNER JOIN meta AS m ON i.meta_id = m.id
											WHERE i.language = ? AND m.url = ?',
											array(BL::getWorkingLanguage(), $URL));

			// already exists
			if($number != 0)
			{
				// add number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURL($URL);
			}
		}

		// current category should be excluded
		else
		{
			// get number of items with this URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
											FROM subname AS i
											INNER JOIN meta AS m ON i.meta_id = m.id
											WHERE i.language = ? AND m.url = ? AND i.id != ?',
											array(BL::getWorkingLanguage(), $URL, $id));

			// already exists
			if($number != 0)
			{
				// add number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURL($URL, $id);
			}
		}

		// return the unique URL!
		return $URL;
	}


	/**
	 * This inserts an item in the database
	 *
	 * @return	int
	 * @param	array $data		The data to insert.
	 */
	public static function insert(array $data)
	{
		return (int) BackendModel::getDB(true)->insert('subname', $data);
	}


	/**
	 * This updates an item in the database
	 *
	 * @return	void
	 * @param	array $data		The data to update.
	 */
	public static function update(array $data)
	{
		// the item id
		$itemId = $data['id'];
		unset($data['id']);

		// update
		BackendModel::getDB(true)->insert('subname', $data, 'id = ?', (int) $itemId);
	}
}

?>