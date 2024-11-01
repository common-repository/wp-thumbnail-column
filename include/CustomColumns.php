<?php

class CustomColumns
{

	protected static $_columns = array();

	/**
	 *
	 * @param type $post_type
	 * @param type $position
	 * @param type $column_type
	 * @param type $title
	 */
	public static function addColumn($post_type, $position, $column_type, $title = '')
	{
		if (!isset(self::$_columns[$post_type]))
		{
			self::$_columns[$post_type] = array();

			add_filter('manage_edit-' . $post_type . '_columns', array('CustomColumns', 'updateColumns_' . $post_type));
			add_action('manage_' . $post_type . '_posts_custom_column', array('CustomColumns', 'column_' . $post_type), 10, 2);

			if ($column_type == 'thumbnail')
			{
				add_action('admin_head', array('CustomColumns', 'update_header_' . $post_type));
				add_image_size($post_type . '-column-thumbnail', 100, 100, true);
			}
		}

		self::$_columns[$post_type][intval($position)] = array(
			'type' => $column_type,
			'title' => $title,
		);
	}

	/**
	 * Update column list callback
	 *
	 * @param type $columns
	 * @return type
	 */
	public static function updateColumns($post_type, $columns)
	{
		// collect columns
		$cols = array();
		foreach ($columns as $id => $title)
		{
			$cols[] = array($id => $title);
		}

		// insert our columns
		foreach (self::$_columns[$post_type] as $pos => $column)
		{
			$pos = intval($pos);

			$cols = array_merge(
				array_slice($cols, 0, 1), array(array($column['type'] => $column['title'])), array_slice($cols, 1));
		}

		//
		$columns = array();
		foreach ($cols as $col)
		{
			foreach ($col as $id => $title)
			{
				$columns[$id] = $title;
			}
		}

		return $columns;
	}

	/**
	 *
	 * @param type $post_type
	 * @param type $column
	 * @param type $post_id
	 */
	public static function column($post_type, $column, $post_id)
	{
		switch ($column)
		{
			case 'thumbnail':
				if (has_post_thumbnail($post_id))
				{
					echo get_the_post_thumbnail($post_id, $post_type . '-column-thumbnail');
				}
				break;
		}
	}

	/**
	 *
	 */
	public static function update_header($post_type)
	{
		echo '<style type="text/css">';
		foreach (self::$_columns[$post_type] as $pos => $column)
		{
			if ($column['type'] == 'thumbnail')
			{
				echo 'th.column-thumbnail {width: 110px;}';
			}
		}
		echo '</style>';
	}

	/**
	 *
	 * @param type $name
	 * @param type $arguments
	 * @return type
	 */
	public static function __callStatic($name, $arguments)
	{
		if (substr($name, 0, 14) == 'updateColumns_')
		{
			$post_type = substr($name, 14);
			return self::updateColumns($post_type, $arguments[0]);
		}

		if (substr($name, 0, 7) == 'column_')
		{
			$post_type = substr($name, 7);
			return self::column($post_type, $arguments[0], $arguments[1]);
		}

		if (substr($name, 0, 14) == 'update_header_')
		{
			$post_type = substr($name, 14);
			return self::update_header($post_type);
		}
	}

}