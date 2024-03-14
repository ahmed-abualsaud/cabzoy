<?php

namespace App\Validations;

use InvalidArgumentException;

class CustomValidation
{

	public function required_when(string $str = null, ?string $fields = null, array $data = [], string &$error = null): bool
	{
		if ($fields === null || empty($data))
			throw new InvalidArgumentException('You must supply the parameters: fields, data.');

		$field = explode(':', $fields);

		if (isset($data[$field[0]]) && $data[$field[0]] === $field[1]) return trim((string) $str) !== '';

		$error = "The $str field is required.";

		return false;
	}

	public function required_whenout(string $str = null, ?string $fields = null, array $data = [], string &$error = null): bool
	{
		if ($fields === null || empty($data))
			throw new InvalidArgumentException('You must supply the parameters: fields, data.');

		$field = explode(':', $fields);

		if (isset($data[$field[0]]) && $data[$field[0]] !== $field[1]) return trim((string) $str) !== '';

		$error = "The $str field is required.";

		return false;
	}
}
