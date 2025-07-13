<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\AcademicDocument;
use App\Models\StudyCalendar;
use App\Models\StudyDetail;
use App\Models\StudyPromotor;
use App\Models\SupervisorAssignment;
use App\Models\CourseResponsibility;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LecturerWithStudyRequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get study requirement document types
        $studyRequirementTypes = DocumentType::whereIn('name', DocumentTypeConstants::getStudyRequirementNames())->get();

        if ($studyRequirementTypes->isEmpty()) {
            $this->command->warn('No study requirement document types found. Run DocumentTypeSeeder first.');
            return;
        }

        // Get the first lecturer (must be executed before LecturerWithCompleteDocumentsSeeder)
        $lecturer = Employee::where('role', 'lecturer')->first();

        if (!$lecturer) {
            $this->command->warn('No eligible lecturer found.');
            return;
        }

        $this->command->info("Creating complete lecturer data for: {$lecturer->user->name}");

        $studyRequirementCount = 0;

        // Create verified study requirement documents
        foreach ($studyRequirementTypes as $documentType) {
            // Check if document already exists for this lecturer and type
            $existingDocument = AcademicDocument::where('employee_id', $lecturer->id)
                ->where('document_type_id', $documentType->id)
                ->first();

            if ($existingDocument) {
                $this->command->info("Study requirement document {$documentType->display_name} already exists for {$lecturer->user->name}, skipping...");
                continue;
            }

            // Create verified study requirement document
            $document = AcademicDocument::create([
                'employee_id' => $lecturer->id,
                'document_type_id' => $documentType->id,
                'file_name' => $this->generateStudyRequirementFileName($documentType->name),
                'file_path' => $this->generateStudyRequirementFilePath($lecturer->id, $documentType->name),
                'workflow_state' => 3, // VERIFIED state
                'upload_date' => Carbon::now()->subDays(rand(1, 30)), // Random upload date within last 30 days
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);

            $studyRequirementCount++;
            $this->command->info("Created verified study requirement document: {$documentType->display_name}");
        }

        $this->command->info("✅ Successfully created {$studyRequirementCount} verified study requirement documents for {$lecturer->user->name}");

        // Create complete study calendar with all related data
        $this->createCompleteStudyCalendar($lecturer);

        $this->command->info("🎓 Lecturer {$lecturer->user->name} now has complete document set and detailed study calendar!");
    }

    /**
     * Create complete study calendar with all related data
     */
    private function createCompleteStudyCalendar(Employee $lecturer): void
    {
        // Check if study calendar already exists
        $existingCalendar = StudyCalendar::where('employee_id', $lecturer->id)->first();
        
        if ($existingCalendar) {
            $this->command->info("Study calendar already exists for {$lecturer->user->name}, skipping...");
            return;
        }

        // Remove any existing related data for this lecturer
        $lecturer->studyCalendars()->delete();
        $lecturer->supervisorAssignments()->delete();
        $lecturer->courseResponsibilities()->delete();

        // Generate study calendar data
        $studyCalendarData = $this->generateStudyCalendarDataForState($lecturer, 1); // 1 = DRAFT

        // Create the study calendar
        $studyCalendar = StudyCalendar::create(array_merge([
            'employee_id' => $lecturer->id,
        ], $studyCalendarData));

        // Create study detail
        $studyDetail = $this->createStudyDetail($studyCalendar);

        // Create promotors
        $this->createPromotors($studyDetail);

        // Create supervisor assignments
        $this->createSupervisorAssignments($lecturer);

        // Create course responsibilities
        $this->createCourseResponsibilities($lecturer);

        $this->command->info("📅 Created complete study calendar with all related data for {$lecturer->user->name}");
    }

    /**
     * Generate study calendar data for a specific workflow_state
     */
    private function generateStudyCalendarDataForState(Employee $lecturer, int $workflowState): array
    {
        $lecturerName = strtolower($lecturer->user->name);
        $position = strtolower($lecturer->position);

        // Determine study level and duration based on current title
        $studyLevel = $this->determineStudyLevel($lecturerName, $position);
        $studyDurationYears = $this->getStudyDuration($studyLevel);

        // Generate realistic dates
        $studyStart = $this->generateStudyStartDate();
        $estimatedEnd = $studyStart->copy()->addYears($studyDurationYears);

        $data = [
            'study_start' => $studyStart,
            'estimated_study_end' => $estimatedEnd,
            'workflow_state' => $workflowState,
        ];

        // Add graduation date if finished
        if ($workflowState === 7) { // FINISHED state
            $data['graduation_date'] = $estimatedEnd->copy()->subMonths(rand(0, 6));
        }

        return $data;
    }

    /**
     * Create study detail for the study calendar
     */
    private function createStudyDetail(StudyCalendar $studyCalendar): StudyDetail
    {
        $studyProgram = \App\Models\StudyProgram::inRandomOrder()->first();
        
        return StudyDetail::create([
            'study_calendar_id' => $studyCalendar->id,
            'university_name' => $this->getRandomUniversity(),
            'university_address' => $this->getUniversityAddress(),
            'university_email' => $this->getUniversityEmail(),
            'university_phone' => $this->getUniversityPhone(),
            'study_program_id' => $studyProgram->id,
            'study_address' => $this->getRandomStudyAddress(),
            'study_level' => $this->getRandomStudyLevel(),
            'total_semester' => $this->getRandomTotalSemester(),
            'scholarship' => $this->getRandomScholarship(),
            'funding_source' => $this->getRandomFundingSource(),
            'study_regulation_notes' => $this->getRandomRegulationNotes(),
        ]);
    }

    /**
     * Create promotors for the study detail
     */
    private function createPromotors(StudyDetail $studyDetail): void
    {
        // Create primary promotor
        StudyPromotor::create([
            'study_detail_id' => $studyDetail->id,
            'name' => $this->getRandomPromotorName(),
            'email' => $this->generatePromotorEmail(),
            'is_primary' => true,
        ]);

        // Create secondary promotor (70% chance)
        if (rand(1, 100) <= 70) {
            StudyPromotor::create([
                'study_detail_id' => $studyDetail->id,
                'name' => $this->getRandomPromotorName(),
                'email' => $this->generatePromotorEmail(),
                'is_primary' => false,
            ]);
        }
    }

    /**
     * Create supervisor assignments for the lecturer
     */
    private function createSupervisorAssignments(Employee $lecturer): void
    {
        // Get potential supervisors (other lecturers, heads, etc.)
        $supervisors = Employee::where('id', '!=', $lecturer->id)
            ->whereIn('role', ['lecturer', 'head_of_study_program', 'head_of_research_group', 'fri_vice_dean'])
            ->get();

        if ($supervisors->isEmpty()) {
            return;
        }

        // Create active supervisor assignment
        $activeSupervisor = $supervisors->random();
        SupervisorAssignment::create([
            'employee_id' => $lecturer->id,
            'supervisor_id' => $activeSupervisor->id,
            'start_date' => Carbon::now()->subMonths(rand(3, 12)),
            'end_date' => null, // Active assignment
        ]);

        // Create completed supervisor assignment (30% chance)
        if (rand(1, 100) <= 30) {
            $completedSupervisor = $supervisors->where('id', '!=', $activeSupervisor->id)->first();
            if ($completedSupervisor) {
                $startDate = Carbon::now()->subMonths(rand(18, 36));
                SupervisorAssignment::create([
                    'employee_id' => $lecturer->id,
                    'supervisor_id' => $completedSupervisor->id,
                    'start_date' => $startDate,
                    'end_date' => $startDate->copy()->addMonths(rand(6, 18)),
                ]);
            }
        }
    }

    /**
     * Create course responsibilities for the lecturer
     */
    private function createCourseResponsibilities(Employee $lecturer): void
    {
        $courses = $this->getCourseList();
        $academicYears = ['2023/2024', '2024/2025', '2025/2026'];
        
        // Create 2-4 course responsibilities
        $numberOfCourses = rand(2, 4);
        $selectedCourses = collect($courses)->random($numberOfCourses);

        foreach ($selectedCourses as $course) {
            CourseResponsibility::create([
                'employee_id' => $lecturer->id,
                'course_name' => $course,
                'semester' => rand(1, 8),
                'academic_year' => $academicYears[array_rand($academicYears)],
            ]);
        }
    }

    /**
     * Determine study level based on current academic title
     */
    private function determineStudyLevel(string $name, string $position): string
    {
        // Those with Prof. are likely pursuing post-doctoral or sabbatical
        if (str_contains($name, 'prof.') || str_contains($position, 'prof')) {
            return 'postdoc'; // Post-doctoral research
        }

        // Those with Dr. might be pursuing additional specialization or higher degree
        if (str_contains($name, 'dr.') || str_contains($position, 'doktor')) {
            return rand(0, 1) ? 'postdoc' : 'specialist'; // Post-doc or specialization
        }

        // Others are likely pursuing doctoral studies
        return 'doctoral';
    }

    /**
     * Get study duration in years based on level
     */
    private function getStudyDuration(string $level): int
    {
        return match($level) {
            'doctoral' => rand(3, 5), // 3-5 years for doctoral
            'postdoc' => rand(1, 2),  // 1-2 years for post-doc
            'specialist' => rand(1, 3), // 1-3 years for specialization
            default => 4
        };
    }

    /**
     * Generate realistic study start date
     */
    private function generateStudyStartDate(): Carbon
    {
        // Generate dates between 1-4 years ago for realistic timeline
        $yearsAgo = rand(1, 4);
        $monthsAgo = rand(0, 11);

        // Prefer academic calendar starts (September, January)
        $preferredMonths = [1, 9]; // January, September
        $month = $preferredMonths[array_rand($preferredMonths)];

        return Carbon::create(
            year: date('Y') - $yearsAgo,
            month: $month,
            day: 1
        );
    }

    /**
     * Generate a realistic file name for study requirement documents
     */
    private function generateStudyRequirementFileName(string $documentTypeName): string
    {
        $fileNameMap = [
            'letter_of_acceptance' => 'Surat_Penerimaan_Resmi.pdf',
            'scholarship_cover_letter' => 'Surat_Pengantar_Beasiswa.pdf',
            'rector_permit_letter' => 'Surat_Izin_Rektor.pdf',
            'permanent_lecturer_decree' => 'SK_Dosen_Tetap_Yayasan.pdf',
            'diploma_certificate' => 'Ijazah_Terakhir.pdf',
            's1_transcript' => 'Transkrip_Nilai_S1.pdf',
            's2_transcript' => 'Transkrip_Nilai_S2.pdf',
            'inpassing_decree' => 'SK_Inpassing.pdf',
            'jad_decree' => 'SK_JAD.pdf',
            'graduation_report_statement' => 'Surat_Pernyataan_Laporan_Kelulusan.pdf',
            'lldikti_assignment_statement' => 'Pernyataan_Penugasan_LLDIKTI.pdf',
            'willing_to_be_relieved_letter' => 'Surat_Bersedia_Dibebastugaskan.pdf',
            'management_permit_letter' => 'Surat_Izin_Pimpinan.pdf',
            'work_period_certificate' => 'Surat_Keterangan_Masa_Kerja.pdf',
            'academic_recommendation_letter' => 'Surat_Rekomendasi_Akademisi.pdf',
            'management_recommendation_letter' => 'Surat_Rekomendasi_Pimpinan.pdf',
        ];

        return $fileNameMap[$documentTypeName] ?? 'Dokumen_Persyaratan_Studi.pdf';
    }

    /**
     * Generate a realistic file path for study requirement documents
     */
    private function generateStudyRequirementFilePath(int $employeeId, string $documentTypeName): string
    {
        $timestamp = Carbon::now()->format('Y/m/d');
        return "documents/{$employeeId}/study_requirements/{$timestamp}/{$this->generateStudyRequirementFileName($documentTypeName)}";
    }

    /**
     * Get random university name
     */
    private function getRandomUniversity(): string
    {
        $universities = [
            'Universitas Indonesia',
            'Institut Teknologi Bandung',
            'Universitas Gadjah Mada',
            'Institut Teknologi Sepuluh Nopember',
            'Universitas Airlangga',
            'Universitas Padjadjaran',
            'Universitas Brawijaya',
            'Universitas Diponegoro',
            'Institut Pertanian Bogor',
            'Universitas Hasanuddin'
        ];

        return $universities[array_rand($universities)];
    }

    /**
     * Get university address
     */
    private function getUniversityAddress(): string
    {
        $addresses = [
            'Jl. Prof. Dr. Ir. H. Soedarto, SH, Tembalang, Semarang, Jawa Tengah 50275',
            'Jl. Ganesha No.10, Lb. Siliwangi, Kecamatan Coblong, Kota Bandung, Jawa Barat 40132',
            'Jl. Prof. Dr. Ir. H. Soedarto, SH, Tembalang, Semarang, Jawa Tengah 50275',
            'Jl. Raya ITS, Sukolilo, Surabaya, Jawa Timur 60111',
            'Jl. Dharmawangsa Dalam, Surabaya, Jawa Timur 60286',
            'Jl. Raya Bandung-Sumedang KM.21, Jatinangor, Sumedang, Jawa Barat 45363',
            'Jl. Veteran, Malang, Jawa Timur 65145',
            'Jl. Prof. H. Soedarto, SH, Tembalang, Semarang, Jawa Tengah 50275',
            'Jl. Raya Dramaga, Bogor, Jawa Barat 16680',
            'Jl. Perintis Kemerdekaan KM.10, Makassar, Sulawesi Selatan 90245'
        ];

        return $addresses[array_rand($addresses)];
    }

    /**
     * Get university email
     */
    private function getUniversityEmail(): string
    {
        $domains = [
            'ui.ac.id',
            'itb.ac.id',
            'ugm.ac.id',
            'its.ac.id',
            'unair.ac.id',
            'unpad.ac.id',
            'ub.ac.id',
            'undip.ac.id',
            'ipb.ac.id',
            'unhas.ac.id'
        ];

        $domain = $domains[array_rand($domains)];
        return 'info@' . $domain;
    }

    /**
     * Get university phone
     */
    private function getUniversityPhone(): string
    {
        $phones = [
            '021-7270163',
            '022-2500935',
            '0274-563974',
            '031-5994251',
            '031-5039470',
            '022-7796377',
            '0341-551611',
            '024-7460058',
            '0251-8628444',
            '0411-585869'
        ];

        return $phones[array_rand($phones)];
    }

    /**
     * Get random study address
     */
    private function getRandomStudyAddress(): string
    {
        $addresses = [
            'Jl. Sudirman No. 123, Jakarta Pusat',
            'Jl. Thamrin No. 45, Jakarta Pusat',
            'Jl. Gatot Subroto No. 67, Jakarta Selatan',
            'Jl. Rasuna Said No. 89, Jakarta Selatan',
            'Jl. Sudirman No. 12, Bandung',
            'Jl. Asia Afrika No. 34, Bandung',
            'Jl. Malioboro No. 56, Yogyakarta',
            'Jl. Solo No. 78, Yogyakarta',
            'Jl. Ahmad Yani No. 90, Surabaya',
            'Jl. Basuki Rahmat No. 23, Surabaya'
        ];

        return $addresses[array_rand($addresses)];
    }

    /**
     * Get random study level
     */
    private function getRandomStudyLevel(): string
    {
        $levels = ['S2', 'S3', 'Postdoc', 'Specialist'];
        return $levels[array_rand($levels)];
    }

    /**
     * Get random total semester
     */
    private function getRandomTotalSemester(): int
    {
        return rand(4, 12);
    }

    /**
     * Get random scholarship
     */
    private function getRandomScholarship(): ?string
    {
        $scholarships = [
            'LPDP',
            'Beasiswa Unggulan',
            'Beasiswa DIKTI',
            'Beasiswa Pertamina',
            'Beasiswa Djarum',
            'Mandiri'
        ];

        return rand(1, 100) <= 60 ? $scholarships[array_rand($scholarships)] : null;
    }

    /**
     * Get random funding source
     */
    private function getRandomFundingSource(): ?string
    {
        $sources = [
            'LPDP',
            'Pribadi',
            'Instansi',
            'Perusahaan',
            'Yayasan'
        ];

        return $sources[array_rand($sources)];
    }

    /**
     * Get random regulation notes
     */
    private function getRandomRegulationNotes(): ?string
    {
        $notes = [
            'Mengikuti peraturan akademik universitas dengan ketat',
            'Wajib mengikuti seminar dan publikasi minimal 2 kali per semester',
            'Harus menyelesaikan disertasi dalam waktu maksimal 6 semester',
            'Wajib mengikuti ujian kualifikasi dalam 2 semester pertama',
            'Harus mempertahankan IPK minimal 3.5',
            'Wajib mengikuti program bahasa Inggris intensif'
        ];

        return rand(1, 100) <= 50 ? $notes[array_rand($notes)] : null;
    }

    /**
     * Get random promotor name
     */
    private function getRandomPromotorName(): string
    {
        $promotorNames = [
            'Prof. Dr. Ahmad Sutrisno, M.Eng',
            'Prof. Dr. Maya Arlini Puspasari, M.T',
            'Prof. Dr. Andi Cakravastia, M.T',
            'Dr. Ir. Budi Hartono, M.T',
            'Dr. Dewi Hardiningtyas, S.T., M.T',
            'Dr. Eng. Ir. Erwin Widodo, M.Eng',
            'Dr. Ir. Herry Christian, M.T',
            'Dr. Nurhadi Siswanto, S.T., M.T',
            'Dr. Ir. Putu Dana Karningsih, M.T',
            'Dr. Ir. Stefanus Eko Wiratno, M.T'
        ];

        return $promotorNames[array_rand($promotorNames)];
    }

    /**
     * Generate promotor email
     */
    private function generatePromotorEmail(): string
    {
        $domains = [
            'ui.ac.id',
            'itb.ac.id',
            'ugm.ac.id',
            'its.ac.id',
            'unair.ac.id'
        ];

        $domain = $domains[array_rand($domains)];
        $name = strtolower(str_replace([' ', ',', '.'], '', $this->getRandomPromotorName()));
        return $name . '@' . $domain;
    }

    /**
     * Get course list
     */
    private function getCourseList(): array
    {
        return [
            'Kalkulus I',
            'Kalkulus II',
            'Aljabar Linear',
            'Analisis Real',
            'Analisis Kompleks',
            'Fisika Dasar I',
            'Fisika Dasar II',
            'Mekanika Kuantum',
            'Kimia Dasar',
            'Kimia Organik',
            'Biologi Umum',
            'Biokimia',
            'Statistika',
            'Geometri Analitik',
            'Persamaan Diferensial',
            'Metode Numerik',
            'Pemrograman Komputer',
            'Struktur Data',
            'Algoritma dan Pemrograman',
            'Basis Data'
        ];
    }
} 