<?php

namespace App\Models;

use App\Entities\{Category, User};
use Faker\Generator;

class CategoryModel extends BaseModel
{
	protected $categoryStatus = ['approved', 'pending', 'rejected'];
	protected $categoryType   = ['vehicle', 'complaint', 'faq', 'ticket', 'cancellation', 'review'];

	protected $useSoftDeletes = true;
	protected $with           = 'users';
	protected $table          = 'categories';
	protected $returnType     = Category::class;
	protected $allowedFields  = ['category_name', 'category_description', 'category_type', 'category_status', 'category_image', 'category_icon', 'created_by'];

	protected $validationRules = [
		'id'              => 'permit_empty|is_natural_no_zero',
		'category_type'   => 'in_list[vehicle, complaint, faq, ticket, cancellation, review]',
		'category_status' => 'in_list[approved, pending, rejected]',
	];

	public function typeOf(?string $type = null)
	{
		$this->builder()->where('category_type', $type);

		return $this;
	}

	public function fake(Generator $faker): Category
	{
		$user = new User([
			// 'password_hash', 'reset_hash', 'reset_at', 'reset_expires', 'activate_hash', 'status', 'status_message', 'active', 'force_pass_reset', 'permissions', 'deleted_at', 'firstname', 'lastname', 'phone', 'profile_pic', 'lat', 'long', 'speed', 'heading', 'is_online', 'app_token',

			'app_token' => str_replace('-', '', $faker->uuid()),
			'user_id' => null,
			'lastname'          => $faker->lastName(),
			'firstname'         => $faker->firstName(),
			'email'             => $faker->unique()->email(),
			'password'          => bin2hex(random_bytes(16)),
			'profile_pic'       => $faker->imageUrl(200, 200),
			'active'            => $faker->numberBetween(0, 1),
			'force_pass_reset'  => $faker->numberBetween(0, 1),
			'is_phone_verified' => $faker->numberBetween(0, 1),
			'username'          => $faker->unique()->userName(),
			'is_online'         => array_rand(['online', 'offline']),
			'id'                => $faker->unique()->randomDigitNotNull(),
			'phone'             => str_replace('+', '', $faker->unique()->e164PhoneNumber()),
		]);

		return new Category([
			'user'                 => $user,
			'created_by'           => $user->id,
			'category_name'        => $faker->word(),
			'category_description' => $faker->sentence(),
			'created_at'           => $faker->dateTime(),
			'updated_at'           => $faker->dateTime(),
			'category_icon'        => $faker->imageUrl(48, 48),
			'category_image'       => $faker->imageUrl(640, 480),
			'category_type'        => $faker->randomElement($this->categoryType),
			'category_status'      => $faker->randomElement($this->categoryStatus),
			'deleted_at'           => $faker->randomElement([null, $faker->dateTime()]),
		]);
	}
}
