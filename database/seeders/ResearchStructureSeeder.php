<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ResearchGroup;
use App\Models\ResearchLab;
use App\Models\StudyProgram;
use Illuminate\Support\Facades\Log;

class ResearchStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates research groups and research labs only.
     * Safe to run in production environment.
     */
    public function run(): void
    {
        Log::info('Starting Research Structure Seeder');

        try {
            // Create Study Programs first (if not exists)
            $this->createStudyPrograms();
            
            // Create Research Groups and Labs structure
            $this->createResearchStructure();
            
            Log::info('Research Structure Seeder completed successfully');
            
        } catch (\Exception $e) {
            Log::error('Research Structure Seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create study programs
     */
    private function createStudyPrograms()
    {
        $programs = ['S1 Teknik Industri', 'S1 Teknik Logistik', 'S1 Sistem Informasi'];
        
        foreach ($programs as $programName) {
            StudyProgram::firstOrCreate(['name' => $programName]);
        }
    }

    /**
     * Create research structure (groups and labs only)
     */
    private function createResearchStructure()
    {
        foreach ($this->getResearchGroupsData() as $groupData) {
            // Create research group (without head initially)
            $researchGroup = ResearchGroup::firstOrCreate(
                ['name' => $groupData['name']],
                [
                    'description' => $groupData['description'],
                    'head_employee_id' => null, // Will be assigned manually in production
                ]
            );

            // Create labs for this research group
            foreach ($groupData['labs'] as $labData) {
                ResearchLab::firstOrCreate(
                    ['name' => $labData['name']],
                    [
                        'alias_name' => $labData['alias_name'],
                        'description' => $labData['description'],
                        'research_group_id' => $researchGroup->id,
                    ]
                );
            }
        }
    }

    /**
     * Get research groups and labs structure data
     */
    private function getResearchGroupsData()
    {
        return [
            [
                'name' => 'Manufacturing dan Process Engineering',
                'description' => 'Kelompok keilmuan yang berfokus pada rekayasa manufaktur dan proses produksi.',
                'labs' => [
                    [
                        'name' => 'Laboratorium Riset Quality System Engineering',
                        'alias_name' => 'Lab QSE',
                        'description' => 'Laboratorium riset yang berfokus pada sistem rekayasa kualitas dan pengendalian mutu.',
                    ],
                    [
                        'name' => 'Laboratorium Riset Product Development & Ergonomics',
                        'alias_name' => 'Lab PDE',
                        'description' => 'Laboratorium riset pengembangan produk dan ergonomi.',
                    ],
                    [
                        'name' => 'Laboratorium Riset Manufacturing System',
                        'alias_name' => 'Lab MS',
                        'description' => 'Laboratorium riset sistem manufaktur dan optimasi proses produksi.',
                    ]
                ]
            ],
            [
                'name' => 'Enterprise and Industrial Management System',
                'description' => 'Kelompok keilmuan yang mengkaji sistem manajemen enterprise dan industri.',
                'labs' => [
                    [
                        'name' => 'Laboratorium Riset Business Modelling & Simulation',
                        'alias_name' => 'Lab BMS',
                        'description' => 'Laboratorium riset pemodelan bisnis dan simulasi.',
                    ],
                    [
                        'name' => 'Laboratorium Riset Project Management & Digital Talent',
                        'alias_name' => 'Lab PMDT',
                        'description' => 'Laboratorium riset manajemen proyek dan pengembangan talenta digital.',
                    ],
                    [
                        'name' => 'Laboratorium Riset Enterprise System and Solution',
                        'alias_name' => 'Lab ESS',
                        'description' => 'Laboratorium riset sistem dan solusi enterprise.',
                    ],
                    [
                        'name' => 'Laboratorium Riset E-Logistic and Supply Chain',
                        'alias_name' => 'Lab ELSC',
                        'description' => 'Laboratorium riset e-logistik dan rantai pasok.',
                    ],
                    [
                        'name' => 'Laboratorium Riset Digital Marketing and Intelligence',
                        'alias_name' => 'Lab DMI',
                        'description' => 'Laboratorium riset pemasaran digital dan intelijen bisnis.',
                    ]
                ]
            ],
            [
                'name' => 'Digital Enterprise System and Technology',
                'description' => 'Kelompok keilmuan yang meneliti sistem dan teknologi enterprise digital.',
                'labs' => [
                    [
                        'name' => 'Laboratorium Riset Enterprise Resource Planning',
                        'alias_name' => 'Lab ERP',
                        'description' => 'Laboratorium riset perencanaan sumber daya perusahaan.',
                    ],
                    [
                        'name' => 'Laboratorium Riset Enterprise Data Management',
                        'alias_name' => 'Lab EDM',
                        'description' => 'Laboratorium riset manajemen data enterprise.',
                    ],
                    [
                        'name' => 'Laboratorium Riset Enterprise Infrastructure Management',
                        'alias_name' => 'Lab EIM',
                        'description' => 'Laboratorium riset manajemen infrastruktur enterprise.',
                    ],
                    [
                        'name' => 'Laboratorium Riset Enterprise Intelligent System Development',
                        'alias_name' => 'Lab EISD',
                        'description' => 'Laboratorium riset pengembangan sistem cerdas enterprise.',
                    ],
                    [
                        'name' => 'Laboratorium System Architecture and Governance',
                        'alias_name' => 'Lab SAG',
                        'description' => 'Laboratorium arsitektur sistem dan tata kelola.',
                    ],
                    [
                        'name' => 'Laboratorium Riset Information System Development (TUKJ)',
                        'alias_name' => 'Lab ISD-TUKJ',
                        'description' => 'Laboratorium riset pengembangan sistem informasi',
                    ]
                ]
            ]
        ];
    }
}
