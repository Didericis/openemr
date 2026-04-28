<?php

/**
 * @package   OpenEMR
 * @link      https://www.open-emr.org
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

declare(strict_types=1);

namespace OpenEMR\Tools\DevelopmentSeed\Data;

final readonly class SeedUser
{
    /**
     * @param list<string> $aclGroupValues Default OpenEMR ACL group `value`s
     *                                     (admin, clin, doc, front, back, breakglass).
     */
    public function __construct(
        public string $username,
        public string $password,
        public string $title,
        public string $fname,
        public ?string $mname,
        public string $lname,
        public string $email,
        public ?string $npi,
        public string $taxonomy,
        public bool $authorized,
        public bool $calendar,
        public array $aclGroupValues,
    ) {
    }
}
