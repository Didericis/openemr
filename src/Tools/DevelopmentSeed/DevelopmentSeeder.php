<?php

/**
 * Seeds a representative slice of OpenEMR data for local development:
 *   - facilities
 *   - users with assorted ACL group memberships (admin / clin / doc / front /
 *     back / breakglass; some users are in multiple groups)
 *   - patients with varied demographics
 *   - encounters tying patients to providers and facilities
 *
 * Seeded rows are tagged with `dev-seed-` (or `dev_` for usernames) so
 * `clean()` can remove them without touching anything else.
 *
 * @package   OpenEMR
 * @link      https://www.open-emr.org
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

declare(strict_types=1);

namespace OpenEMR\Tools\DevelopmentSeed;

use OpenEMR\Common\Acl\AclExtended;
use OpenEMR\Common\Database\QueryUtils;
use OpenEMR\Common\Uuid\UuidRegistry;
use OpenEMR\Tools\DevelopmentSeed\Data\SeedDataSet;
use OpenEMR\Tools\DevelopmentSeed\Data\SeedEncounter;
use OpenEMR\Tools\DevelopmentSeed\Data\SeedFacility;
use OpenEMR\Tools\DevelopmentSeed\Data\SeedPatient;
use OpenEMR\Tools\DevelopmentSeed\Data\SeedUser;
use Symfony\Component\Console\Output\OutputInterface;

final readonly class DevelopmentSeeder
{
    public const FIXTURE_PREFIX = 'dev-seed-';
    public const USERNAME_PREFIX = 'dev_';

    public function __construct(private OutputInterface $output)
    {
    }

    public function seed(): void
    {
        $this->clean();

        $this->info('Seeding facilities...');
        $facilityIdsByName = $this->seedFacilities(SeedDataSet::facilities());
        $this->info(sprintf('  installed %d facilities', count($facilityIdsByName)));

        $this->info('Seeding users + ACL group assignments...');
        $userIdsByUsername = $this->seedUsers(SeedDataSet::users(), $facilityIdsByName);
        $this->info(sprintf('  installed %d users', count($userIdsByUsername)));

        $this->info('Seeding patients...');
        $patientPidsByPubpid = $this->seedPatients(SeedDataSet::patients());
        $this->info(sprintf('  installed %d patients', count($patientPidsByPubpid)));

        $this->info('Seeding encounters...');
        $encounterCount = $this->seedEncounters(
            SeedDataSet::encounters(),
            $patientPidsByPubpid,
            $facilityIdsByName,
            $userIdsByUsername,
        );
        $this->info(sprintf('  installed %d encounters', $encounterCount));
    }

    public function clean(): void
    {
        $this->info('Removing previously-seeded development data...');

        // form_encounter: match the prefixed reason; clean the related uuids too.
        $this->deleteWithUuids(
            'form_encounter',
            'reason LIKE ?',
            [self::FIXTURE_PREFIX . '%'],
        );

        // patient_data
        $this->deleteWithUuids(
            'patient_data',
            'pubpid LIKE ?',
            [self::FIXTURE_PREFIX . '%'],
        );

        // facilities — make sure no real facility shares the prefix.
        QueryUtils::sqlStatementThrowException(
            'DELETE FROM `facility` WHERE `name` LIKE ?',
            [self::FIXTURE_PREFIX . '%'],
        );

        // ACL group memberships + ARO entries for our seeded usernames.
        $usernames = QueryUtils::fetchTableColumn(
            'SELECT username FROM `users` WHERE username LIKE ?',
            'username',
            [self::USERNAME_PREFIX . '%'],
        );
        foreach ($usernames as $username) {
            QueryUtils::sqlStatementThrowException(
                'DELETE m FROM `gacl_groups_aro_map` m
                 INNER JOIN `gacl_aro` a ON a.id = m.aro_id
                 WHERE a.section_value = ? AND a.value = ?',
                ['users', $username],
            );
            QueryUtils::sqlStatementThrowException(
                'DELETE FROM `gacl_aro` WHERE section_value = ? AND value = ?',
                ['users', $username],
            );
        }

        // Users + users_secure + uuid_registry rows for the same usernames.
        $this->deleteWithUuids(
            'users',
            'username LIKE ?',
            [self::USERNAME_PREFIX . '%'],
        );
        QueryUtils::sqlStatementThrowException(
            'DELETE FROM `users_secure` WHERE `username` LIKE ?',
            [self::USERNAME_PREFIX . '%'],
        );
    }

    /**
     * @param list<SeedFacility> $facilities
     * @return array<string, int>
     */
    private function seedFacilities(array $facilities): array
    {
        $idsByName = [];
        foreach ($facilities as $facility) {
            $row = $facility->toRow();
            $columns = array_keys($row);
            $placeholders = array_fill(0, count($columns), '?');
            $sql = sprintf(
                'INSERT INTO `facility` (%s) VALUES (%s)',
                implode(', ', array_map(static fn(string $c) => "`{$c}`", $columns)),
                implode(', ', $placeholders),
            );
            QueryUtils::sqlInsert($sql, array_values($row));

            $lookup = QueryUtils::querySingleRow(
                'SELECT id FROM `facility` WHERE name = ?',
                [$facility->name],
            );
            if ($lookup === false) {
                throw new \RuntimeException("Failed to look up just-inserted facility {$facility->name}");
            }
            $idsByName[$facility->name] = $this->intColumn($lookup, 'id');
        }
        return $idsByName;
    }

    /**
     * @param list<SeedUser> $users
     * @param array<string, int> $facilityIdsByName
     * @return array<string, int>
     */
    private function seedUsers(array $users, array $facilityIdsByName): array
    {
        $idsByUsername = [];
        $primaryFacilityName = array_key_first($facilityIdsByName);
        if ($primaryFacilityName === null) {
            throw new \RuntimeException('At least one facility must be seeded before users.');
        }
        $primaryFacilityId = $facilityIdsByName[$primaryFacilityName];

        foreach ($users as $user) {
            $hash = password_hash($user->password, PASSWORD_DEFAULT);

            $uuid = (new UuidRegistry(['table_name' => 'users']))->createUuid();

            QueryUtils::sqlInsert(
                'INSERT INTO `users` SET
                    `uuid` = ?, `username` = ?, `password` = ?, `authorized` = ?,
                    `fname` = ?, `mname` = ?, `lname` = ?, `title` = ?,
                    `email` = ?, `npi` = ?, `taxonomy` = ?, `calendar` = ?,
                    `facility` = ?, `facility_id` = ?,
                    `active` = 1, `see_auth` = 1, `cal_ui` = 1',
                [
                    $uuid,
                    $user->username,
                    'NoLongerUsed',
                    $user->authorized ? 1 : 0,
                    $user->fname,
                    $user->mname,
                    $user->lname,
                    $user->title,
                    $user->email,
                    $user->npi,
                    $user->taxonomy,
                    $user->calendar ? 1 : 0,
                    $primaryFacilityName,
                    $primaryFacilityId,
                ],
            );

            $userRow = QueryUtils::querySingleRow(
                'SELECT id FROM `users` WHERE username = ?',
                [$user->username],
            );
            if ($userRow === false) {
                throw new \RuntimeException("Failed to look up just-inserted user {$user->username}");
            }
            $userId = $this->intColumn($userRow, 'id');

            QueryUtils::sqlInsert(
                'INSERT INTO `users_secure` (`id`, `username`, `password`, `last_update_password`)
                 VALUES (?, ?, ?, NOW())',
                [$userId, $user->username, $hash],
            );

            // Translate ACL group `value`s (admin / doc / ...) to display titles
            // (Administrators / Physicians / ...) — addUserAros expects titles.
            $titles = [];
            foreach ($user->aclGroupValues as $value) {
                $groupRow = QueryUtils::querySingleRow(
                    'SELECT name FROM `gacl_aro_groups` WHERE value = ?',
                    [$value],
                );
                if ($groupRow === false) {
                    throw new \RuntimeException(sprintf(
                        'Unknown ACL group value "%s" — has the database been initialized?',
                        $value,
                    ));
                }
                $titles[] = $this->stringColumn($groupRow, 'name');
            }
            AclExtended::addUserAros($user->username, $titles);

            $idsByUsername[$user->username] = $userId;
        }
        return $idsByUsername;
    }

    /**
     * @param list<SeedPatient> $patients
     * @return array<string, int>
     */
    private function seedPatients(array $patients): array
    {
        $pidsByPubpid = [];
        foreach ($patients as $patient) {
            $maxRow = QueryUtils::querySingleRow(
                'SELECT IFNULL(MAX(pid), 0) + 1 AS next_pid FROM `patient_data`',
            );
            $nextPid = $maxRow !== false ? $this->intColumn($maxRow, 'next_pid') : 1;

            $uuid = (new UuidRegistry(['table_name' => 'patient_data']))->createUuid();

            $row = $patient->toRow();
            $columns = array_merge(array_keys($row), ['pid', 'uuid']);
            $values = array_merge(array_values($row), [$nextPid, $uuid]);
            $placeholders = array_fill(0, count($columns), '?');

            $sql = sprintf(
                'INSERT INTO `patient_data` (%s) VALUES (%s)',
                implode(', ', array_map(static fn(string $c) => "`{$c}`", $columns)),
                implode(', ', $placeholders),
            );
            QueryUtils::sqlInsert($sql, $values);
            $pidsByPubpid[$patient->pubpid] = $nextPid;
        }
        return $pidsByPubpid;
    }

    /**
     * @param list<SeedEncounter> $encounters
     * @param array<string, int> $patientPidsByPubpid
     * @param array<string, int> $facilityIdsByName
     * @param array<string, int> $userIdsByUsername
     */
    private function seedEncounters(
        array $encounters,
        array $patientPidsByPubpid,
        array $facilityIdsByName,
        array $userIdsByUsername,
    ): int {
        $count = 0;
        foreach ($encounters as $encounter) {
            $pid = $patientPidsByPubpid[$encounter->patientPubpid]
                ?? throw new \RuntimeException("Encounter references unknown patient {$encounter->patientPubpid}");
            $facilityId = $facilityIdsByName[$encounter->facilityName]
                ?? throw new \RuntimeException("Encounter references unknown facility {$encounter->facilityName}");
            $providerId = $userIdsByUsername[$encounter->providerUsername]
                ?? throw new \RuntimeException("Encounter references unknown user {$encounter->providerUsername}");

            $catRow = QueryUtils::querySingleRow(
                'SELECT pc_catid FROM `openemr_postcalendar_categories` WHERE pc_constant_id = ?',
                [$encounter->pcCatidConstant],
            );
            $pcCatId = $catRow !== false ? $this->intColumn($catRow, 'pc_catid') : 5;

            $encounterId = QueryUtils::generateId();
            $uuid = (new UuidRegistry(['table_name' => 'form_encounter']))->createUuid();

            QueryUtils::sqlInsert(
                'INSERT INTO `form_encounter` SET
                    `uuid` = ?, `encounter` = ?, `pid` = ?, `facility_id` = ?,
                    `facility` = ?, `provider_id` = ?, `pc_catid` = ?,
                    `date` = ?, `reason` = ?, `class_code` = ?,
                    `sensitivity` = ?, `last_level_billed` = 0,
                    `last_level_closed` = 0, `stmt_count` = 0,
                    `billing_facility` = ?',
                [
                    $uuid,
                    $encounterId,
                    $pid,
                    $facilityId,
                    $encounter->facilityName,
                    $providerId,
                    $pcCatId,
                    $encounter->date,
                    $encounter->reason,
                    $encounter->classCode,
                    'normal',
                    $facilityId,
                ],
            );
            $count++;
        }
        return $count;
    }

    /** @param list<mixed> $bindings */
    private function deleteWithUuids(string $table, string $where, array $bindings): void
    {
        $rows = QueryUtils::fetchRecords(
            "SELECT `uuid` FROM `{$table}` WHERE {$where}",
            $bindings,
        );
        foreach ($rows as $row) {
            $uuid = $row['uuid'] ?? null;
            if ($uuid === null || $uuid === '') {
                continue;
            }
            QueryUtils::sqlStatementThrowException(
                'DELETE FROM `uuid_registry` WHERE `table_name` = ? AND `uuid` = ?',
                [$table, $uuid],
            );
        }
        QueryUtils::sqlStatementThrowException(
            "DELETE FROM `{$table}` WHERE {$where}",
            $bindings,
        );
    }

    /** @param array<mixed> $row */
    private function intColumn(array $row, string $column): int
    {
        $value = $row[$column] ?? null;
        if (is_int($value)) {
            return $value;
        }
        if (is_string($value) && ctype_digit($value)) {
            return (int) $value;
        }
        throw new \RuntimeException("Expected integer column '{$column}' in result row.");
    }

    /** @param array<mixed> $row */
    private function stringColumn(array $row, string $column): string
    {
        $value = $row[$column] ?? null;
        if (is_string($value)) {
            return $value;
        }
        throw new \RuntimeException("Expected string column '{$column}' in result row.");
    }

    private function info(string $message): void
    {
        $this->output->writeln($message);
    }
}
