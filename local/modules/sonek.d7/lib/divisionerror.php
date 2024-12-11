<?php
	namespace Sonek\D7;
	class DivisionError extends \Bitrix\Main\SystemException
	{
		protected $parameters1;
		protected $parameters2;

		public function __construct($type = 'division by zero', $parameters1 = 0, $parameters2 = 0, \Exception $previous = null)
		{
			$message = 'An error has occured:'.$type;

			$this->parameters1 = $parameters1;
			$this->parameters2 = $parameters2;

			parent::__construct($message, false, false, false, $previous);
		}

		public function getParameters1()
		{
			return $this->parameters1;
		}

		public function getParameters2()
		{
			return $this->parameters2;
		}
	}