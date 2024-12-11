<?php
	namespace Sonek\D7;
	class Division
	{
		public static function divided($parameters1 = 0, $parameters2 = 0)
		{
			if ($parameters2===0)
				throw new \Sonek\D7\DivisionError('Деление на ноль', $parameters1, $parameters2);

			$result = $parameters1/$parameters2;
			return $result;
		}
	}