<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;
use Tatter\Settings\Models\SettingModel;

class DefaultSettingSeeder extends Seeder
{
	public function run()
	{
		// Define default project setting templates
		$rows = [
			[
				'name' => 'timezone',
				'datatype' => 'string',
				'summary' => 'Timezone for the user',
				'content' => 'Asia/Kolkata',
				'protected' => 0
			],
			[
				'name' => 'perPage',
				'datatype' => 'int',
				'summary' => 'Number of items to show per page',
				'content' => '8',
				'protected' => 0
			],
			[
				'name' => 'siteVersion',
				'datatype' => 'string',
				'summary' => 'Current version of this project',
				'content' => '1.0.0',
				'protected' => 1
			],
			[
				'name' => 'siteName',
				'datatype' => 'string',
				'summary' => 'Your Site name',
				'content' => env('app.name', 'CabZoy'),
				'protected' => 1
			],
			[
				'name' => 'siteLogo',
				'datatype' => 'image',
				'summary' => 'Your Site logo',
				'content' => 'uploads/settings/1641183738_68a5854d22a767e19e29.png',
				'protected' => 1
			],
			[
				'name' => 'siteFavicon',
				'datatype' => 'image',
				'summary' => 'Favicon for Site',
				'content' => 'uploads/settings/1641183755_4f25074acfc7a41d0c38.png',
				'protected' => 1
			],
			[
				'name' => 'siteUrl',
				'datatype' => 'uri',
				'summary' => 'Your Site URL',
				'content' => 'https://example.com',
				'protected' => 1
			],
			[
				'name' => 'privacyPolicyUrl',
				'datatype' => 'uri',
				'summary' => 'Your privacy policy url',
				'content' => 'https://example.com/privacy-policy',
				'protected' => 1
			],
			[
				'name' => 'termsAndConditionsUrl',
				'datatype' => 'uri',
				'summary' => 'Your teams and conditions url',
				'content' => 'https://example.com/terms-conditions',
				'protected' => 1
			],
			[
				'name' => 'siteAddress',
				'datatype' => 'string',
				'summary' => 'Your Site address',
				'content' => 'Demo Place, Dummy City IN 101010',
				'protected' => 1
			],
			[
				'name' => 'sitePhone',
				'datatype' => 'string',
				'summary' => 'Your Site phone with country code without plus sign',
				'content' => '11234567890',
				'protected' => 1
			],
			[
				'name' => 'googleMapApi',
				'datatype' => 'string',
				'summary' => 'Google Map API Key',
				'content' => '',
				'protected' => 1
			],
			[
				'name'      => 'flutterwavePublicKey',
				'datatype'  => 'string',
				'summary'   => 'Public Key for FlutterWave',
				'content'   => '',
				'protected' => 1
			],
			[
				'name'      => 'flutterwaveSecretKey',
				'datatype'  => 'string',
				'summary'   => 'Secret Key for FlutterWave',
				'content'   => '',
				'protected' => 1
			],
			[
				'name' => 'defaultLanguage',
				'datatype' => 'string',
				'summary' => 'Your Default Language Code',
				'content' => 'en',
				'protected' => 1
			],
			[
				'name' => 'defaultLengthUnit',
				'datatype' => 'string',
				'summary' => 'Length format for Distance Calculation',
				'content' => 'KM',
				'protected' => 1
			],
			[
				'name' => 'defaultCurrencyUnit',
				'datatype' => 'string',
				'summary' => 'Currency format for number helper',
				'content' => 'USD',
				'protected' => 1
			],
			[
				'name' => 'defaultCurrencyScale',
				'datatype' => 'int',
				'summary' => 'Conversion rate to the fractional monetary unit',
				'content' => '10',
				'protected' => 1
			],
			[
				'name' => 'defaultLat',
				'datatype' => 'string',
				'summary' => 'Your Default Location\'s Latitude, You can find https://www.latlong.net/',
				'content' => '26.912434',
				'protected' => 1
			],
			[
				'name' => 'defaultLong',
				'datatype' => 'string',
				'summary' => 'Your Default Location\'s Longitude, You can find https://www.latlong.net/',
				'content' => '75.787270',
				'protected' => 1
			],
			[
				'name' => 'defaultCountryCode',
				'datatype' => 'string',
				'summary' => 'Your Default Country Code with plus sign',
				'content' => '+1',
				'protected' => 1
			],
			[
				'name' => 'defaultCountryNameCode',
				'datatype' => 'string',
				'summary' => 'Your Default Country Name Code only two digit',
				'content' => 'us',
				'protected' => 1
			],
			[
				'name' => 'defaultMinBookingAmount',
				'datatype' => 'int',
				'summary' => 'when booking amount less then describe that will be the booking amount',
				'content' => '50',
				'protected' => 1
			],
			[
				'name' => 'defaultCancellationAmount',
				'datatype' => 'int',
				'summary' => 'Booking cancellation amount',
				'content' => '50',
				'protected' => 1
			],
			[
				'name' => 'enableBooking',
				'datatype' => 'bool',
				'summary' => 'When its false user not able to book a ride',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'fareCalculationType',
				'datatype' => 'string',
				'summary' => 'Fare Calculation Type - sum | greater-sum | less-sum | greater | less',
				'content' => 'sum',
				'protected' => 1
			],
			[
				'name' => 'defaultDriverDistanceRadius',
				'datatype' => 'int',
				'summary' => 'find nearest driver radius eg. 25',
				'content' => '5',
				'protected' => 1
			],
			[
				'name' => 'enableFareCalculateRound',
				'datatype' => 'bool',
				'summary' => 'Is Fare calculate in rounded figure',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'enableFareCategoryCalculation',
				'datatype' => 'bool',
				'summary' => 'Category fare (eg. Vehicle Category wise fare) calculate on orders',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'enablePromoCode',
				'datatype' => 'bool',
				'summary' => 'Enable Promo Code',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'enableTaxCommissionCalculation',
				'datatype' => 'bool',
				'summary' => 'Enable Tax Commission Calculation',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'includeTaxCommission',
				'datatype' => 'bool',
				'summary' => 'Include or Exclude Tax and Commission in fare calculation',
				'content' => '0',
				'protected' => 1
			],
			[
				'name' => 'enableDriverWallet',
				'datatype' => 'bool',
				'summary' => 'Enable driver wallet to send and receive money',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'autoAssignNearestDriver',
				'datatype' => 'bool',
				'summary' => 'Auto assign nearest driver',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'enablePayingToDriver',
				'datatype' => 'bool',
				'summary' => 'Enable Pay to driver when user pay',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'enableDocumentVerification',
				'datatype' => 'bool',
				'summary' => 'Enable Document Verification',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'requiredDriverDocument',
				'datatype' => 'bool',
				'summary' => 'When its required driver document',
				'content' => '0',
				'protected' => 1
			],
			[
				'name' => 'driverDocumentType',
				'datatype' => 'string',
				'summary' => 'Driver Documents Type - Govt id, Passport, Driving License, Insurance',
				'content' => 'Govt id,Passport,DrivingLicense,Insurance',
				'protected' => 1
			],
			[
				'name' => 'requiredUserDocument',
				'datatype' => 'bool',
				'summary' => 'When its required user document',
				'content' => '0',
				'protected' => 1
			],
			[
				'name' => 'userDocumentType',
				'datatype' => 'string',
				'summary' => 'User Documents Type - Govt id',
				'content' => 'Govt id',
				'protected' => 1
			],
			[
				'name' => 'enableShowCategoryWhenDriverNotOnline',
				'datatype' => 'bool',
				'summary' => 'Show Category when driver not online',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'enableAutoVerifyUser',
				'datatype' => 'bool',
				'summary' => 'Enable all user auto verify when register',
				'content' => '0',
				'protected' => 1
			],
			[
				'name' => 'enableAutoVerifyUserAfterEmailVerify',
				'datatype' => 'bool',
				'summary' => 'Enable all user auto verify after email verify',
				'content' => '0',
				'protected' => 1
			],
			[
				'name' => 'enableUserRegisterFromApp',
				'datatype' => 'bool',
				'summary' => 'Enable user register from app',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'enableDriverRegisterFromApp',
				'datatype' => 'bool',
				'summary' => 'Enable driver register from app',
				'content' => '0',
				'protected' => 1
			],
			[
				'name' => 'enablePhoneLogin',
				'datatype' => 'bool',
				'summary' => 'Enable login from Mobile number',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'enableEmailLogin',
				'datatype' => 'bool',
				'summary' => 'Enable login from Email & password',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'enableCashPayment',
				'datatype' => 'bool',
				'summary' => 'Enable cash payment',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'enableReferralSystem',
				'datatype' => 'bool',
				'summary' => 'Enable refer and earn',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'defaultReferralRewardInPercentage',
				'datatype' => 'int',
				'summary' => 'Referral reward in percentage',
				'content' => '1',
				'protected' => 1
			],
			[
				'name' => 'enableDefaultVehicleAssign',
				'datatype' => 'bool',
				'summary' => 'Enable assign default vehicle to user by Admin',
				'content' => '0',
				'protected' => 1
			],
			[
				'name' => 'enableDefaultVehicleAssignUser',
				'datatype' => 'bool',
				'summary' => 'Enable default vehicle assign by user',
				'content' => '0',
				'protected' => 1
			],

			[
				'name' => 'enableSingleOrder',
				'datatype' => 'bool',
				'summary' => 'User can not book multiple orders',
				'content' => '0',
				'protected' => 1
			],
		];

		// Check for and create project setting templates
		foreach ($rows as $row) {
			if (!model(SettingModel::class)->where('name', $row['name'])->first())
				if (!model(SettingModel::class)->allowCallbacks(false)->insert($row))
					throw new RuntimeException(implode('. ', model(SettingModel::class)->errors())); // @codeCoverageIgnore
		}
	}
}
