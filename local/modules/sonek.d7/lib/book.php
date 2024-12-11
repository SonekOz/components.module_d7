<?php
	namespace Sonek\D7;

	use \Bitrix\Main\Entity;
	use \Bitrix\Main\Type;

	class BookTable extends Entity\DataManager
	{
		public static function getTableName()
		{
			return 'book_d7';
		}

		public static function getUfId()
		{
			return 'BOOK_D7';
		}

		public static function getConnectionName()
		{
			return 'default';
		}

		public static function getMap()
		{
			return array(
				//ID
				new Entity\IntegerField('ID', array(
					'primary' => true,
					'autocomplete' => true
				)),
				//Название
				new Entity\StringField('NAME', array(
					'required' => true,
				)),
				//Год выхода
				new Entity\IntegerField('RELEASED', array(
					'required' => true,
				)),
				//ISBN
				new Entity\StringField('ISBN', array(
					'required' => true,
					'column_name' => 'ISBNCODE',
					'validation' => function () {
						return [
							new Entity\Validator\Unique(),
							function ($value, $primary, $row, $field) {
								/**
								 * value - значение поля
								 * primary - массив с первичным ключом, в данном случае [ID => 1]
								 * row - весь массив данных, переданный в ::add или ::update
								 * field - объект валидируемого поля - Entity\StringField('ISBN', ...)
								**/

								$clean = str_replace(array('-',' '), '', $value);

								if (preg_match('/^\d{1,13}$/', $clean))
								{
									return true;
								}
								else
								{
									return 'Код ISBN должен содержать не более 13 цифр, разделенных дефисом или пробелами';
								}
							}
						];
					}
				)),

				//ФИО Автора
				new Entity\StringField('AUTHOR'),
				//Дата и время поступления книги в магазин
				new Entity\DatetimeField('TIME_ARRIVAL', [
					'required' => true,
					'default_value' => new Type\DateTime()
				]),
				//Описание книги
				new Entity\TextField('DESCRIPTION'),
				//Сколько лет книге
				new Entity\ExpressionField('AGE_YEAR',
					'YEAR(CURDATE())-%s', array('RELEASED')
				),

				/** Количество редактирований **/
				new Entity\IntegerField('WRITE_COUNT'),
			);
		}
	}