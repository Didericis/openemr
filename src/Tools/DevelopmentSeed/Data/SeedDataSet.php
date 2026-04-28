<?php

/**
 * Static factory of dev seed data. Edit the methods below to adjust what gets
 * installed by `php bin/console openemr:seed-dev-data`.
 *
 * @package   OpenEMR
 * @link      https://www.open-emr.org
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

declare(strict_types=1);

namespace OpenEMR\Tools\DevelopmentSeed\Data;

final class SeedDataSet
{
    /** @return list<SeedFacility> */
    public static function facilities(): array
    {
        return [
            new SeedFacility(
                name: 'dev-seed-Sample Primary Clinic',
                phone: '(415) 555-0100',
                fax: '(415) 555-0199',
                street: '100 Market Street',
                city: 'San Francisco',
                state: 'CA',
                postalCode: '94105',
                countryCode: 'US',
                email: 'primary@example.test',
                facilityNpi: '1234567890',
                facilityCode: 'PRIM',
                serviceLocation: 1,
                billingLocation: 1,
                acceptsAssignment: 1,
                posCode: 11,
                primaryBusinessEntity: 1,
                color: '#A8D8EA',
            ),
            new SeedFacility(
                name: 'dev-seed-Satellite Specialty Office',
                phone: '(510) 555-0200',
                fax: '(510) 555-0299',
                street: '742 Telegraph Avenue',
                city: 'Oakland',
                state: 'CA',
                postalCode: '94612',
                countryCode: 'US',
                email: 'satellite@example.test',
                facilityNpi: '1234567891',
                facilityCode: 'SATL',
                serviceLocation: 1,
                billingLocation: 0,
                acceptsAssignment: 1,
                posCode: 11,
                primaryBusinessEntity: 0,
                color: '#FFB6B9',
            ),
        ];
    }

    /**
     * Each user is assigned one or more default OpenEMR ACL group `value`s
     * (admin, clin, doc, front, back, breakglass). The mix below covers each
     * group at least once and includes a multi-group user (`dev_office_mgr`).
     *
     * @return list<SeedUser>
     */
    public static function users(): array
    {
        return [
            new SeedUser(
                username: 'dev_admin',
                password: 'pass',
                title: 'Mr.',
                fname: 'Devon',
                mname: 'A.',
                lname: 'Admin',
                email: 'dev_admin@example.test',
                npi: null,
                taxonomy: '207Q00000X',
                authorized: true,
                calendar: false,
                aclGroupValues: ['admin'],
            ),
            new SeedUser(
                username: 'dev_doc',
                password: 'pass',
                title: 'Dr.',
                fname: 'Diana',
                mname: 'M.',
                lname: 'Physician',
                email: 'dev_doc@example.test',
                npi: '1112223330',
                taxonomy: '208D00000X',
                authorized: true,
                calendar: true,
                aclGroupValues: ['doc', 'clin'],
            ),
            new SeedUser(
                username: 'dev_clinician',
                password: 'pass',
                title: 'RN',
                fname: 'Carla',
                mname: null,
                lname: 'Clinician',
                email: 'dev_clinician@example.test',
                npi: '1112223331',
                taxonomy: '163WC0400X',
                authorized: true,
                calendar: true,
                aclGroupValues: ['clin'],
            ),
            new SeedUser(
                username: 'dev_front',
                password: 'pass',
                title: 'Mx.',
                fname: 'Frankie',
                mname: null,
                lname: 'Frontdesk',
                email: 'dev_front@example.test',
                npi: null,
                taxonomy: '207Q00000X',
                authorized: false,
                calendar: false,
                aclGroupValues: ['front'],
            ),
            new SeedUser(
                username: 'dev_billing',
                password: 'pass',
                title: 'Ms.',
                fname: 'Bobbi',
                mname: null,
                lname: 'Biller',
                email: 'dev_billing@example.test',
                npi: null,
                taxonomy: '207Q00000X',
                authorized: false,
                calendar: false,
                aclGroupValues: ['back'],
            ),
            new SeedUser(
                username: 'dev_office_mgr',
                password: 'pass',
                title: 'Ms.',
                fname: 'Olivia',
                mname: 'P.',
                lname: 'Manager',
                email: 'dev_office_mgr@example.test',
                npi: null,
                taxonomy: '207Q00000X',
                authorized: false,
                calendar: false,
                aclGroupValues: ['front', 'back'],
            ),
            new SeedUser(
                username: 'dev_emergency',
                password: 'pass',
                title: 'Mr.',
                fname: 'Ezra',
                mname: null,
                lname: 'Emergency',
                email: 'dev_emergency@example.test',
                npi: null,
                taxonomy: '207Q00000X',
                authorized: true,
                calendar: false,
                aclGroupValues: ['breakglass'],
            ),
        ];
    }

    /** @return list<SeedPatient> */
    public static function patients(): array
    {
        return [
            new SeedPatient(
                pubpid: 'dev-seed-100001',
                title: 'Mr.',
                fname: 'Marcus',
                mname: 'J',
                lname: 'Aurelio',
                ss: '111-00-1001',
                street: '23 Stoic Lane',
                postalCode: '94110',
                city: 'San Francisco',
                state: 'CA',
                countryCode: 'US',
                phoneHome: '(415) 555-1001',
                phoneCell: '(415) 555-2001',
                email: 'marcus.a@example.test',
                dob: '1972-04-26',
                sex: 'Male',
                status: 'married',
                race: 'white',
                ethnicity: 'not_hisp_or_latin',
                language: 'English',
            ),
            new SeedPatient(
                pubpid: 'dev-seed-100002',
                title: 'Ms.',
                fname: 'Hypatia',
                mname: null,
                lname: 'Rivera',
                ss: '111-00-1002',
                street: '88 Geometry Way',
                postalCode: '94612',
                city: 'Oakland',
                state: 'CA',
                countryCode: 'US',
                phoneHome: '(510) 555-1002',
                phoneCell: '(510) 555-2002',
                email: 'hypatia.r@example.test',
                dob: '1985-11-03',
                sex: 'Female',
                status: 'single',
                race: 'mexican',
                ethnicity: 'hisp_or_latin',
                language: 'Spanish',
            ),
            new SeedPatient(
                pubpid: 'dev-seed-100003',
                title: 'Mx.',
                fname: 'Juno',
                mname: 'P',
                lname: 'Okafor',
                ss: '111-00-1003',
                street: '14 Telegraph Hill',
                postalCode: '94133',
                city: 'San Francisco',
                state: 'CA',
                countryCode: 'US',
                phoneHome: '(415) 555-1003',
                phoneCell: '(415) 555-2003',
                email: 'juno.o@example.test',
                dob: '1998-07-19',
                sex: 'Nonbinary',
                status: 'single',
                race: 'black',
                ethnicity: 'not_hisp_or_latin',
                language: 'English',
            ),
            new SeedPatient(
                pubpid: 'dev-seed-100004',
                title: 'Mrs.',
                fname: 'Sadia',
                mname: 'N',
                lname: 'Khan',
                ss: '111-00-1004',
                street: '501 Crescent Road',
                postalCode: '94403',
                city: 'San Mateo',
                state: 'CA',
                countryCode: 'US',
                phoneHome: '(650) 555-1004',
                phoneCell: '(650) 555-2004',
                email: 'sadia.k@example.test',
                dob: '1962-02-14',
                sex: 'Female',
                status: 'married',
                race: 'asian',
                ethnicity: 'not_hisp_or_latin',
                language: 'Urdu',
            ),
            new SeedPatient(
                pubpid: 'dev-seed-100005',
                title: 'Mr.',
                fname: 'Kenji',
                mname: null,
                lname: 'Tanaka',
                ss: '111-00-1005',
                street: '9 Cherry Blossom Drive',
                postalCode: '94087',
                city: 'Sunnyvale',
                state: 'CA',
                countryCode: 'US',
                phoneHome: '(408) 555-1005',
                phoneCell: '(408) 555-2005',
                email: 'kenji.t@example.test',
                dob: '2008-09-30',
                sex: 'Male',
                status: 'single',
                race: 'asian',
                ethnicity: 'not_hisp_or_latin',
                language: 'Japanese',
            ),
        ];
    }

    /** @return list<SeedEncounter> */
    public static function encounters(): array
    {
        return [
            new SeedEncounter(
                patientPubpid: 'dev-seed-100001',
                facilityName: 'dev-seed-Sample Primary Clinic',
                providerUsername: 'dev_doc',
                date: '2026-04-02 09:30:00',
                reason: 'dev-seed-Annual physical, hypertension follow-up.',
                classCode: 'AMB',
                pcCatidConstant: 'office_visit',
            ),
            new SeedEncounter(
                patientPubpid: 'dev-seed-100002',
                facilityName: 'dev-seed-Sample Primary Clinic',
                providerUsername: 'dev_clinician',
                date: '2026-04-08 14:00:00',
                reason: 'dev-seed-Wellness visit, immunization review.',
                classCode: 'AMB',
                pcCatidConstant: 'office_visit',
            ),
            new SeedEncounter(
                patientPubpid: 'dev-seed-100003',
                facilityName: 'dev-seed-Satellite Specialty Office',
                providerUsername: 'dev_doc',
                date: '2026-04-12 11:15:00',
                reason: 'dev-seed-Sports injury — left knee evaluation.',
                classCode: 'AMB',
                pcCatidConstant: 'office_visit',
            ),
            new SeedEncounter(
                patientPubpid: 'dev-seed-100004',
                facilityName: 'dev-seed-Sample Primary Clinic',
                providerUsername: 'dev_doc',
                date: '2026-04-15 10:00:00',
                reason: 'dev-seed-Diabetes management, A1C review.',
                classCode: 'AMB',
                pcCatidConstant: 'office_visit',
            ),
            new SeedEncounter(
                patientPubpid: 'dev-seed-100005',
                facilityName: 'dev-seed-Satellite Specialty Office',
                providerUsername: 'dev_clinician',
                date: '2026-04-20 13:30:00',
                reason: 'dev-seed-Pediatric checkup — well child visit.',
                classCode: 'AMB',
                pcCatidConstant: 'office_visit',
            ),
            new SeedEncounter(
                patientPubpid: 'dev-seed-100001',
                facilityName: 'dev-seed-Sample Primary Clinic',
                providerUsername: 'dev_doc',
                date: '2026-04-22 15:45:00',
                reason: 'dev-seed-Lab review, lipid panel.',
                classCode: 'AMB',
                pcCatidConstant: 'office_visit',
            ),
        ];
    }
}
