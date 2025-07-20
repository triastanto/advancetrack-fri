<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ResearchGroup;
use App\Models\ResearchLab;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PersonnelSeeder extends Seeder
{
    use SeederHelpers;

    /**
     * Run the database seeds.
     *
     * Creates users and employees for development/testing.
     * Should NOT be run in production environment.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->warn('⚠️  PersonnelSeeder skipped in production environment');
            return;
        }

        Log::info('Starting Personnel Seeder (Development Only)');

        try {
            // Create personnel for research structure
            $this->createResearchPersonnel();

            // Create study program leadership
            $this->createStudyProgramLeadership();

            // Create administrative staff
            $this->createAdministrativeStaff();

            // Create senior leadership
            $this->createSeniorLeadership();

            Log::info('Personnel Seeder completed successfully');

        } catch (\Exception $e) {
            Log::error('Personnel Seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create personnel for research groups and labs
     */
    private function createResearchPersonnel()
    {
        foreach ($this->getPersonnelData() as $groupData) {
            // Find the research group
            $researchGroup = ResearchGroup::where('name', $groupData['group_name'])->first();
            if (!$researchGroup) {
                $this->command->warn("Research group '{$groupData['group_name']}' not found. Run ResearchStructureSeeder first.");
                continue;
            }

            // Create research group head
            if (isset($groupData['head'])) {
                $headEmployee = $this->createUserEmployee(
                    $groupData['head'],
                    'head_of_research_group',
                    'Ketua Kelompok Keilmuan'
                );

                // Assign as group head
                $researchGroup->update(['head_employee_id' => $headEmployee->id]);
            }

            // Create lab personnel
            if (isset($groupData['labs'])) {
                foreach ($groupData['labs'] as $labData) {
                    $this->createLabPersonnel($labData, $researchGroup);
                }
            }
        }
    }

    /**
     * Create personnel for a specific lab
     */
    private function createLabPersonnel($labData, $researchGroup)
    {
        // Find the research lab
        $researchLab = ResearchLab::where('alias_name', $labData['lab_alias'])
            ->where('research_group_id', $researchGroup->id)
            ->first();

        if (!$researchLab) {
            $this->command->warn("Research lab '{$labData['lab_alias']}' not found in group '{$researchGroup->name}'");
            return;
        }

        // Create lab head
        if (isset($labData['head'])) {
            $labHeadEmployee = $this->createUserEmployee(
                $labData['head'],
                'lecturer',
                'Ketua Laboratorium Riset',
                ['is_lab_head' => true, 'research_lab_id' => $researchLab->id]
            );
        }

        // Create lab members
        if (isset($labData['members'])) {
            foreach ($labData['members'] as $memberData) {
                $this->createUserEmployee(
                    $memberData,
                    'lecturer',
                    $memberData['position'],
                    ['research_lab_id' => $researchLab->id]
                );
            }
        }
    }

    /**
     * Create study program leadership
     */
    private function createStudyProgramLeadership()
    {
        $headsData = [
            [
                'name' => 'Dr. Ir. Agus Widodo, M.T.',
                'email' => 'agus.widodo.dev@fri.ac.id',
                // NIDN will be generated dynamically,
                'position' => 'Ketua Program Studi S1 Teknik Industri'
            ],
            [
                'name' => 'Dr. Rina Fitriana, S.T., M.T.',
                'email' => 'rina.fitriana.dev@fri.ac.id',
                // NIDN will be generated dynamically,
                'position' => 'Ketua Program Studi S1 Teknik Logistik'
            ],
            [
                'name' => 'Dr. Hendra Kurniawan, S.Kom., M.T.',
                'email' => 'hendra.kurniawan.dev@fri.ac.id',
                // NIDN will be generated dynamically,
                'position' => 'Ketua Program Studi S1 Sistem Informasi'
            ]
        ];

        foreach ($headsData as $headData) {
            $this->createUserEmployee($headData, 'head_of_study_program', $headData['position']);
        }
    }

    /**
     * Create administrative staff
     */
    private function createAdministrativeStaff()
    {
        $staffData = [
            [
                'name' => 'Ahmad Rifai, S.E.',
                'email' => 'ahmad.rifai.dev@fri.ac.id',
                // NIDN will be generated dynamically,
                'position' => 'Staf SDM & Keuangan'
            ],
            [
                'name' => 'Dewi Kartika, S.Sos.',
                'email' => 'dewi.kartika.dev@fri.ac.id',
                // NIDN will be generated dynamically,
                'position' => 'Staf SDM & Keuangan'
            ],
            [
                'name' => 'Budi Santoso, A.Md.',
                'email' => 'budi.santoso.dev@fri.ac.id',
                // NIDN will be generated dynamically,
                'position' => 'Staf SDM & Keuangan'
            ]
        ];

        foreach ($staffData as $staff) {
            $this->createUserEmployee($staff, 'hr_finance_staff', $staff['position']);
        }
    }

    /**
     * Create senior leadership
     */
    private function createSeniorLeadership()
    {
        $viceDeanData = [
            'name' => 'Prof. Dr. Ir. Bambang Setiawan, M.T.',
            'email' => 'wakildekan2.dev@fri.ac.id',
            // NIDN will be generated dynamically,
            'position' => 'Wakil Dekan II FRI'
        ];

        $this->createUserEmployee($viceDeanData, 'fri_vice_dean', $viceDeanData['position']);
    }

    /**
     * Generic method to create user and employee
     */
    private function createUserEmployee($userData, $role, $position, $additionalEmployeeData = [])
    {
        // Generate unique NIDN if not provided
        if (!isset($userData['nidn']) || empty($userData['nidn'])) {
            $userData['nidn'] = $this->generateUniqueNIDN();
        }

        $user = User::firstOrCreate(
            ['email' => $userData['email']],
            [
                'name' => $userData['name'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $employeeData = array_merge([
            'nidn' => $userData['nidn'],
            'position' => $position,
            'role' => $role,
            'birth_place' => $this->randomBirthPlace(),
            'birth_date' => $this->getAppropriateaBirthDate($role),
            'gender' => $this->determineGender($userData['name']),
            'functional_position' => $this->determineFunctionalPosition($userData['name'], $role),
            'origin_address' => $this->randomAddress(),
            'contact_phone' => $this->randomPhone(),
            'contact_email' => $userData['email'],
            'photo' => $this->getRandomPhotoPath($userData),
            'is_approved' => true,
            'validated_by' => 1,
            'validated_on' => now(),
        ], $additionalEmployeeData);

        return Employee::firstOrCreate(
            ['user_id' => $user->id],
            $employeeData
        );
    }

    /**
     * Get appropriate birth date based on role
     */
    private function getAppropriateaBirthDate($role)
    {
        $ranges = [
            'fri_vice_dean' => [1960, 1970],
            'head_of_research_group' => [1960, 1975],
            'head_of_study_program' => [1965, 1980],
            'lecturer' => [1970, 1990],
            'hr_finance_staff' => [1980, 1995],
        ];

        $range = $ranges[$role] ?? [1975, 1985];
        return $this->randomBirthDate($range[0], $range[1]);
    }

    /**
     * Determine functional position based on name and role
     */
    private function determineFunctionalPosition($name, $role)
    {
        if (str_contains($name, 'Prof.')) return 'Profesor';
        if (str_contains($name, 'Dr.')) return 'Lektor Kepala';

        return match($role) {
            'fri_vice_dean', 'head_of_research_group', 'head_of_study_program' => 'Lektor Kepala',
            'lecturer' => 'Lektor',
            'hr_finance_staff' => 'Tenaga Kependidikan',
            default => 'Asisten Ahli'
        };
    }

    /**
     * Get personnel data organized by research groups
     */
    private function getPersonnelData()
    {
        return [
            [
                'group_name' => 'Manufacturing dan Process Engineering',
                'head' => [
                    'name' => 'Prof. Dr. Ir. Sutrisno, M.Eng.',
                    'email' => 'sutrisno.mpe.dev@fri.ac.id',
                    // NIDN will be generated dynamically
                ],
                'labs' => [
                    [
                        'lab_alias' => 'Lab QSE',
                        'head' => [
                            'name' => 'Dr. Ir. Agung Pramono, M.T.',
                            'email' => 'agung.pramono.dev@fri.ac.id',
                            // NIDN will be generated dynamically
                        ],
                        'members' => [
                            [
                                'name' => 'Dr. Sari Indrawati, S.T., M.T.',
                                'email' => 'sari.indrawati.dev@fri.ac.id',
                                // NIDN will be generated dynamically
                                'position' => 'Dosen Teknik Industri'
                            ],
                            [
                                'name' => 'Ir. Budi Setiawan, M.Sc.',
                                'email' => 'budi.setiawan.dev@fri.ac.id',
                                // NIDN will be generated dynamically
                                'position' => 'Dosen Manajemen Kualitas'
                            ]
                        ]
                    ],
                    [
                        'lab_alias' => 'Lab PDE',
                        'head' => [
                            'name' => 'Dr. Rina Puspitasari, S.T., M.T.',
                            'email' => 'rina.puspitasari.dev@fri.ac.id',
                            // NIDN will be generated dynamically
                        ],
                        'members' => [
                            [
                                'name' => 'Dr. Fajar Nugraha, S.T., M.T.',
                                'email' => 'fajar.nugraha.dev@fri.ac.id',
                                // NIDN will be generated dynamically,
                                'position' => 'Dosen Ergonomi'
                            ]
                        ]
                    ],
                    [
                        'lab_alias' => 'Lab MS',
                        'head' => [
                            'name' => 'Dr. Ir. Hendi Kusuma, M.T.',
                            'email' => 'hendi.kusuma.dev@fri.ac.id',
                            // NIDN will be generated dynamically
                        ],
                        'members' => [
                            [
                                'name' => 'Ir. Dewi Kartini, M.T.',
                                'email' => 'dewi.kartini.dev@fri.ac.id',
                                // NIDN will be generated dynamically,
                                'position' => 'Dosen Sistem Manufaktur'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'group_name' => 'Enterprise and Industrial Management System',
                'head' => [
                    'name' => 'Dr. Ir. Maya Sari, M.M.',
                    'email' => 'maya.sari.dev@fri.ac.id',
                    // NIDN will be generated dynamically
                ],
                'labs' => [
                    [
                        'lab_alias' => 'Lab BMS',
                        'head' => [
                            'name' => 'Dr. Ahmad Fauzi, S.T., M.M.',
                            'email' => 'ahmad.fauzi.dev@fri.ac.id',
                            // NIDN will be generated dynamically
                        ],
                        'members' => [
                            [
                                'name' => 'Dr. Lestari Wulandari, S.E., M.M.',
                                'email' => 'lestari.wulandari.dev@fri.ac.id',
                                // NIDN will be generated dynamically,
                                'position' => 'Dosen Manajemen Bisnis'
                            ]
                        ]
                    ],
                    [
                        'lab_alias' => 'Lab PMDT',
                        'head' => [
                            'name' => 'Dr. Ir. Rendra Utama, M.T.',
                            'email' => 'rendra.utama.dev@fri.ac.id',
                            // NIDN will be generated dynamically
                        ],
                        'members' => [
                            [
                                'name' => 'Dr. Anisa Rahma, S.Kom., M.T.',
                                'email' => 'anisa.rahma.dev@fri.ac.id',
                                // NIDN will be generated dynamically,
                                'position' => 'Dosen Manajemen Proyek'
                            ]
                        ]
                    ],
                    [
                        'lab_alias' => 'Lab ESS',
                        'head' => [
                            'name' => 'Dr. Ir. Bayu Nugroho, M.T.',
                            'email' => 'bayu.nugroho.dev@fri.ac.id',
                            // NIDN will be generated dynamically
                        ],
                        'members' => [
                            [
                                'name' => 'Dr. Putri Maharani, S.T., M.T.',
                                'email' => 'putri.maharani.dev@fri.ac.id',
                                // NIDN will be generated dynamically,
                                'position' => 'Dosen Sistem Enterprise'
                            ]
                        ]
                    ],
                    [
                        'lab_alias' => 'Lab ELSC',
                        'head' => [
                            'name' => 'Dr. Ir. Indra Gunawan, M.T.',
                            'email' => 'indra.gunawan.dev@fri.ac.id',
                            // NIDN will be generated dynamically
                        ],
                        'members' => [
                            [
                                'name' => 'Dr. Sinta Dewi, S.T., M.T.',
                                'email' => 'sinta.dewi.dev@fri.ac.id',
                                // NIDN will be generated dynamically,
                                'position' => 'Dosen Logistik'
                            ]
                        ]
                    ],
                    [
                        'lab_alias' => 'Lab DMI',
                        'head' => [
                            'name' => 'Dr. Winda Prastiwi, S.E., M.M.',
                            'email' => 'winda.prastiwi.dev@fri.ac.id',
                            // NIDN will be generated dynamically
                        ],
                        'members' => [
                            [
                                'name' => 'Dr. Rizki Pratama, S.Kom., M.T.',
                                'email' => 'rizki.pratama.dev@fri.ac.id',
                                // NIDN will be generated dynamically,
                                'position' => 'Dosen Pemasaran Digital'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'group_name' => 'Digital Enterprise System and Technology',
                'head' => [
                    'name' => 'Dr. Andi Prasetyo, S.T., M.T.',
                    'email' => 'andi.prasetyo.dev@fri.ac.id',
                    // NIDN will be generated dynamically
                ],
                'labs' => [
                    [
                        'lab_alias' => 'Lab ERP',
                        'head' => [
                            'name' => 'Dr. Ir. Teguh Wahyudi, M.T.',
                            'email' => 'teguh.wahyudi.dev@fri.ac.id',
                            // NIDN will be generated dynamically
                        ],
                        'members' => [
                            [
                                'name' => 'Dr. Fitri Handayani, S.Kom., M.T.',
                                'email' => 'fitri.handayani.dev@fri.ac.id',
                                // NIDN will be generated dynamically,
                                'position' => 'Dosen Sistem Informasi'
                            ]
                        ]
                    ],
                    [
                        'lab_alias' => 'Lab EDM',
                        'head' => [
                            'name' => 'Dr. Ir. Arif Rachman, M.T.',
                            'email' => 'arif.rachman.dev@fri.ac.id',
                            // NIDN will be generated dynamically
                        ],
                        'members' => [
                            [
                                'name' => 'Dr. Nanda Septiani, S.Kom., M.T.',
                                'email' => 'nanda.septiani.dev@fri.ac.id',
                                // NIDN will be generated dynamically,
                                'position' => 'Dosen Data Science'
                            ]
                        ]
                    ],
                    [
                        'lab_alias' => 'Lab EIM',
                        'head' => [
                            'name' => 'Dr. Ir. Yoga Pratama, M.T.',
                            'email' => 'yoga.pratama.dev@fri.ac.id',
                            // NIDN will be generated dynamically
                        ],
                        'members' => [
                            [
                                'name' => 'Dr. Mira Sari, S.T., M.T.',
                                'email' => 'mira.sari.dev@fri.ac.id',
                                // NIDN will be generated dynamically,
                                'position' => 'Dosen Infrastruktur IT'
                            ]
                        ]
                    ],
                    [
                        'lab_alias' => 'Lab EISD',
                        'head' => [
                            'name' => 'Dr. Ir. Dimas Saputra, M.T.',
                            'email' => 'dimas.saputra.dev@fri.ac.id',
                            // NIDN will be generated dynamically
                        ],
                        'members' => [
                            [
                                'name' => 'Dr. Lia Permatasari, S.Kom., M.T.',
                                'email' => 'lia.permatasari.dev@fri.ac.id',
                                // NIDN will be generated dynamically,
                                'position' => 'Dosen Artificial Intelligence'
                            ]
                        ]
                    ],
                    [
                        'lab_alias' => 'Lab SAG',
                        'head' => [
                            'name' => 'Dr. Ir. Eko Prasetyo, M.T.',
                            'email' => 'eko.prasetyo.dev@fri.ac.id',
                            // NIDN will be generated dynamically
                        ],
                        'members' => [
                            [
                                'name' => 'Dr. Vera Anggraini, S.T., M.T.',
                                'email' => 'vera.anggraini.dev@fri.ac.id',
                                // NIDN will be generated dynamically,
                                'position' => 'Dosen Arsitektur Sistem'
                            ]
                        ]
                    ],
                    [
                        'lab_alias' => 'Lab ISD-TUKJ',
                        'head' => [
                            'name' => 'Dr. Ir. Farid Abdullah, M.T.',
                            'email' => 'farid.abdullah.dev@fri.ac.id',
                            // NIDN will be generated dynamically
                        ],
                        'members' => [
                            [
                                'name' => 'Dr. Rini Astuti, S.Kom., M.T.',
                                'email' => 'rini.astuti.dev@fri.ac.id',
                                // NIDN will be generated dynamically,
                                'position' => 'Dosen Pengembangan Sistem'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get a random or default photo path for seeded employees
     */
    private function getRandomPhotoPath($userData)
    {
        // Use randomuser.me for realistic random photos
        $genders = ['men', 'women'];
        $email = isset($userData['email']) && !empty($userData['email']) ? $userData['email'] : uniqid('user');
        $genderIndex = abs(crc32($email)) % 2;
        $gender = $genders[$genderIndex];
        $photoId = (abs(crc32($email)) % 99) + 1; // randomuser.me has 1-99
        return "https://randomuser.me/api/portraits/{$gender}/{$photoId}.jpg";
    }
}
