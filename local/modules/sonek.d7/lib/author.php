<?php
	namespace Sonek\D7;

	use \Bitrix\Main\Entity;
	use \Bitrix\Main\Type;

	class AuthorTable extends Entity\DataManager
	{
		public static function getTableName()
		{
			return 'author_d7';
		}

		public static function getMap()
		{
			return array(
				/** ID **/
				new Entity\IntegerField('ID', array(
					'primary' => true,
					'autocomplete' => true
				)),
				/** Имя Автора **/
				new Entity\StringField('NAME', array(
					'required' => true,
				)),

				/** Фамилия Автора **/
				new Entity\StringField('LAST_NAME'),
			);
		}
	}