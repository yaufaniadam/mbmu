<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class LocationPicker extends Field
{
    protected string $view = 'forms.components.location-picker';

    protected ?float $defaultLatitude = -7.797068;
    protected ?float $defaultLongitude = 110.370529;
    protected int $zoom = 13;
    protected string $height = '300px';
    protected ?string $latitudeField = 'latitude';
    protected ?string $longitudeField = 'longitude';

    public function defaultLocation(float $latitude, float $longitude): static
    {
        $this->defaultLatitude = $latitude;
        $this->defaultLongitude = $longitude;
        return $this;
    }

    public function zoom(int $zoom): static
    {
        $this->zoom = $zoom;
        return $this;
    }

    public function height(string $height): static
    {
        $this->height = $height;
        return $this;
    }

    public function latitudeField(string $field): static
    {
        $this->latitudeField = $field;
        return $this;
    }

    public function longitudeField(string $field): static
    {
        $this->longitudeField = $field;
        return $this;
    }

    public function getDefaultLatitude(): ?float
    {
        return $this->defaultLatitude;
    }

    public function getDefaultLongitude(): ?float
    {
        return $this->defaultLongitude;
    }

    public function getZoom(): int
    {
        return $this->zoom;
    }

    public function getHeight(): string
    {
        return $this->height;
    }

    public function getLatitudeField(): string
    {
        return $this->latitudeField;
    }

    public function getLongitudeField(): string
    {
        return $this->longitudeField;
    }
}
