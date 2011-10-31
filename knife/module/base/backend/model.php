<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the subname module
 *
 * @author		authorname
 */
class BackendclassnameModel
{
	/**
	 * @param int $id
	 */
	public static function delete($id)
	{
		BackendModel::getDB(true)->delete('subname', 'id = ?', (int) $id);
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(i.id)
														FROM subname AS i
														WHERE i.id = ?',
														(int) $id);
	}

	/**
	 * @param int $id
	 * @return array
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
	 * @param string $URL
	 * @param int[optional] $id
	 * @return string
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
	 * @param array $data
	 * @return int
	 */
	public static function insert(array $data)
	{
		$data['created_on'] = BackendModel::getUTCDate();

		return (int) BackendModel::getDB(true)->insert('subname', $data);
	}

	/**
	 * @param	array $data		The data to update.
	 * @param	int $itemId		The item id to update.
	 */
	public static function update(array $data, $itemId)
	{
		$data['edited_on'] = BackendModel::getUTCDate();

		BackendModel::getDB(true)->update('subname', $data, 'id = ?', (int) $itemId);
	}
}
