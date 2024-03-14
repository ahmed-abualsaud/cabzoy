<?php

namespace App\Entities;

class Vehicle extends BaseEntity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	public function getVehicleImage($formatted = true)
	{

		if (!empty($this->attributes['vehicle_image']) && !str_contains($this->attributes['vehicle_image'], 'http://')  && !str_contains($this->attributes['vehicle_image'], 'https://') && $formatted) return site_url($this->attributes['vehicle_image']);

		return $this->attributes['vehicle_image'];
	}

	public function setVehicleImage(?string $image = null)
	{
		$this->attributes['vehicle_image'] = !str_contains($image, 'uploads/') ? "uploads/{$image}" : $image;

		return $this;
	}

	public function setVehicleNumber(?string $vehicleNumber = null)
	{
		$this->attributes['vehicle_number'] = str_safe($vehicleNumber);

		return $this;
	}

	public function setVehicleBrand(?string $vehicleBrand = null)
	{
		$this->attributes['vehicle_brand'] = str_safe($vehicleBrand);

		return $this;
	}

	public function setVehicleModal(?string $vehicleModal = null)
	{
		$this->attributes['vehicle_modal'] = str_safe($vehicleModal);

		return $this;
	}

	public function setVehicleColor(?string $vehicleColor = null)
	{
		$this->attributes['vehicle_color'] = str_safe($vehicleColor);

		return $this;
	}
}
