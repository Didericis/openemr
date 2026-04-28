<?php

/**
 * @package   OpenEMR
 * @link      https://www.open-emr.org
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

declare(strict_types=1);

namespace OpenEMR\Tools\DevelopmentSeed\Data;

final readonly class SeedPatient
{
    public function __construct(
        public string $pubpid,
        public string $title,
        public string $fname,
        public ?string $mname,
        public string $lname,
        public string $ss,
        public string $street,
        public string $postalCode,
        public string $city,
        public string $state,
        public string $countryCode,
        public string $phoneHome,
        public string $phoneCell,
        public string $email,
        public string $dob,
        public string $sex,
        public string $status,
        public string $race,
        public string $ethnicity,
        public string $language,
    ) {
    }

    /** @return array<string, string> */
    public function toRow(): array
    {
        $row = [
            'pubpid' => $this->pubpid,
            'title' => $this->title,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'ss' => $this->ss,
            'street' => $this->street,
            'postal_code' => $this->postalCode,
            'city' => $this->city,
            'state' => $this->state,
            'country_code' => $this->countryCode,
            'phone_home' => $this->phoneHome,
            'phone_cell' => $this->phoneCell,
            'email' => $this->email,
            'DOB' => $this->dob,
            'sex' => $this->sex,
            'status' => $this->status,
            'race' => $this->race,
            'ethnicity' => $this->ethnicity,
            'language' => $this->language,
        ];
        if ($this->mname !== null) {
            $row['mname'] = $this->mname;
        }
        return $row;
    }
}
