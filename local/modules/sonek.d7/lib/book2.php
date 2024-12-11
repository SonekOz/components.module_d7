<?php
	namespace Sonek\D7;

	use \Bitrix\Main\Entity;
	use \Bitrix\Main\Type;

	class Book2Table extends Entity\DataManager
	{
		public static function getTableName()
		{
			return 'book_d7_2';
		}

		public static function getMap()
		{
			return array(
				/** ID **/
				new Entity\IntegerField('ID', array(
					'primary' => true,
					'autocomplete' => true
				)),
				/** Название **/
				new Entity\StringField('NAME', array(
					'required' => true,
				)),
				/** Год выхода **/
				new Entity\IntegerField('RELEASED', array(
					'required' => true,
				)),
				/** ISBN **/
				new Entity\StringField('ISBN', array(
					'required' => true,
					'column_name' => 'ISBNCODE',
				)),

				/** Дата и время поступления книги в магазин **/
				new Entity\DatetimeField('TIME_ARRIVAL', [
					'required' => true,
					'default_value' => new Type\DateTime()
				]),
				/** Описание книги **/
				new Entity\TextField('DESCRIPTION'),
				/** Сколько лет книге **/
				new Entity\ExpressionField('AGE_YEAR',
					'YEAR(CURDATE())-%s', array('RELEASED')
				),

				/** ID Автора **/
				new Entity\IntegerField('AUTHOR_ID'),

				new Entity\ReferenceField(
					'AUTHOR',
					'\Sonek\D7\AuthorTable',
					/** Далее описываем массив - по каким полям связываем сущность и задаём в формате, похожем на фильтр для секции $filter в getList, где
					 * this. - поле текущей сущности
					 * ref. - поле сущности партнёра, где указываем ID автора, связанного с данной книгой
					**/
					['=this.AUTHOR_ID' => 'ref.ID']
				),
			);
		}
	}