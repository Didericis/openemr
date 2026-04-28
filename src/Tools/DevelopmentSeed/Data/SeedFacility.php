<?php

/**
 * @package   OpenEMR
 * @link      https://www.open-emr.org
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

declare(strict_types=1);

namespace OpenEMR\Tools\DevelopmentSeed\Data;

final readonly class SeedFacility
{
    public function __construct(
        public string $name,
        public string $phone,
        public string $fax,
        public string $street,
        public string $city,
        public string $state,
        public string $postalCode,
        public string $countryCode,
        public string $email,
        public string $facilityNpi,
        public string $facilityCode,
        public int $serviceLocation,
        public int $billingLocation,
        public int $acceptsAssignment,
        public int $posCode,
        public int $primaryBusinessEntity,
        public string $color,
    ) {
    }

    /** @return array<string, string|int> */
    public function toRow(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'fax' => $this->fax,
            'street' => $this->street,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postalCode,
            'country_code' => $this->countryCode,
            'email' => $this->email,
            'facility_npi' => $this->facilityNpi,
            'facility_code' => $this->facilityCode,
            'service_location' => $this->serviceLocation,
            'billing_location' => $this->billingLocation,
            'accepts_assignment' => $this->acceptsAssignment,
            'pos_code' => $this->posCode,
            'primary_business_entity' => $this->primaryBusinessEntity,
            'color' => $this->color,
            'extra_validation' => 1,
        ];
    }
}
