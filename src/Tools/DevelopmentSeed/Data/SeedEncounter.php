<?php

/**
 * @package   OpenEMR
 * @link      https://www.open-emr.org
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

declare(strict_types=1);

namespace OpenEMR\Tools\DevelopmentSeed\Data;

final readonly class SeedEncounter
{
    public function __construct(
        public string $patientPubpid,
        public string $facilityName,
        public string $providerUsername,
        public string $date,
        public string $reason,
        public string $classCode,
        public string $pcCatidConstant,
    ) {
    }
}
