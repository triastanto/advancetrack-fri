<?php

namespace App\Constants;

class DocumentTypeConstants
{
    public const DOCUMENT_TYPES = [
        // 1. Dokumen Persyaratan Studi Lanjut (Dosen)
        [
            'name' => 'letter_of_acceptance',
            'display_name' => 'Letter of Acceptance',
            'description' => 'Surat penerimaan resmi dari institusi pendidikan',
        ],
        [
            'name' => 'scholarship_cover_letter',
            'display_name' => 'Surat Pengantar Beasiswa',
            'description' => 'Surat pengantar resmi untuk pengajuan beasiswa',
        ],
        [
            'name' => 'rector_permit_letter',
            'display_name' => 'Surat Izin Rektor',
            'description' => 'Surat izin resmi dari Rektor untuk melanjutkan studi',
        ],
        [
            'name' => 'permanent_lecturer_decree',
            'display_name' => 'SK Dosen Tetap Yayasan',
            'description' => 'Surat Keputusan pengangkatan sebagai dosen tetap yayasan',
        ],
        [
            'name' => 'diploma_certificate',
            'display_name' => 'Ijazah',
            'description' => 'Ijazah terakhir yang dimiliki sebagai persyaratan studi lanjut',
        ],
        [
            'name' => 's1_transcript',
            'display_name' => 'Transkrip Nilai S1',
            'description' => 'Transkrip nilai lengkap jenjang Sarjana (S1)',
        ],
        [
            'name' => 's2_transcript',
            'display_name' => 'Transkrip Nilai S2',
            'description' => 'Transkrip nilai lengkap jenjang Magister (S2)',
        ],
        [
            'name' => 'inpassing_decree',
            'display_name' => 'SK Inpassing',
            'description' => 'Surat Keputusan Inpassing kepangkatan',
        ],
        [
            'name' => 'jad_decree',
            'display_name' => 'SK JAD',
            'description' => 'Surat Keputusan Jabatan Akademik Dosen',
        ],
        [
            'name' => 'graduation_report_statement',
            'display_name' => 'Surat Pernyataan Melaporkan Kelulusan',
            'description' => 'Surat pernyataan kesediaan melaporkan kelulusan setelah menyelesaikan studi',
        ],
        [
            'name' => 'lldikti_assignment_statement',
            'display_name' => 'Pernyataan Penugasan LLDIKTI',
            'description' => 'Surat pernyataan penugasan dari LLDIKTI',
        ],
        [
            'name' => 'willing_to_be_relieved_letter',
            'display_name' => 'Surat Bersedia Dibebastugaskan',
            'description' => 'Surat pernyataan kesediaan untuk dibebastugaskan selama studi',
        ],
        [
            'name' => 'management_permit_letter',
            'display_name' => 'Surat Izin Pimpinan',
            'description' => 'Surat izin resmi dari pimpinan institusi',
        ],
        [
            'name' => 'work_period_certificate',
            'display_name' => 'Surat Keterangan Masa Kerja',
            'description' => 'Surat keterangan masa kerja sebagai dosen',
        ],
        [
            'name' => 'academic_recommendation_letter',
            'display_name' => 'Surat Rekomendasi Akademisi',
            'description' => 'Surat rekomendasi dari akademisi atau dosen senior',
        ],
        [
            'name' => 'management_recommendation_letter',
            'display_name' => 'Surat Rekomendasi Pimpinan',
            'description' => 'Surat rekomendasi dari pimpinan institusi',
        ],

        // 2. Dokumen Persetujuan Studi Lanjut (Kepala SDM & Keuangan, Ketua Program Studi, Ketua Kelompok Keilmuan)
        [
            'name' => 'study_compatibility',
            'display_name' => 'Dokumen Kesesuaian Studi Lanjut',
            'description' => 'Dokumen untuk memastikan kesesuaian program yang diambil',
        ],
        [
            'name' => 'application_minutes',
            'display_name' => 'Berita Acara Pengajuan Studi Lanjut',
            'description' => 'Notulen resmi untuk proses pengajuan studi lanjut',
        ],
        [
            'name' => 'approval_minutes',
            'display_name' => 'Berita Acara Persetujuan Studi Lanjut',
            'description' => 'Notulen resmi untuk proses persetujuan studi lanjut',
        ],
        [
            'name' => 'nde',
            'display_name' => 'NDE Studi Lanjut',
            'description' => 'Surat permintaan studi lanjut secara formal',
        ],
        [
            'name' => 'pid',
            'display_name' => 'Perjanjian Ikatan Dinas (PID)',
            'description' => 'Dokumen resmi yang telah ditandatangani oleh semua pemangku kepentingan',
        ],

        // 3. Dokumen Laporan Per Semester (Dosen)
        [
            'name' => 'lecturer_cover_letter',
            'display_name' => 'Surat Pengantar dari Dosen',
            'description' => 'Surat pengantar resmi dari dosen pembimbing untuk laporan kemajuan studi',
        ],
        [
            'name' => 'transcript',
            'display_name' => 'Transkrip Nilai',
            'description' => 'Transkrip nilai resmi dari institusi pendidikan per semester',
        ],
        [
            'name' => 'active_student_certificate',
            'display_name' => 'Surat Keterangan Aktif',
            'description' => 'Surat keterangan status mahasiswa aktif dari institusi pendidikan',
        ],
        [
            'name' => 'igracias_publication_proof',
            'display_name' => 'Bukti Unggah Publikasi di iGracias',
            'description' => 'Bukti screenshot atau dokumen unggah publikasi ilmiah di sistem iGracias',
        ],
        [
            'name' => 'tuition_payment_proof',
            'display_name' => 'Bukti Pembayaran Biaya Pendidikan',
            'description' => 'Bukti pembayaran SPP atau biaya pendidikan lainnya',
        ],
        [
            'name' => 'study_progress_certificate',
            'display_name' => 'Laporan Kemajuan Semester',
            'description' => 'Laporan kemajuan semester',
        ],

        // 4. Dokumen Laporan Akhir dan Kelulusan (Dosen)
        [
            'name' => 'diploma',
            'display_name' => 'Ijazah',
            'description' => 'Ijazah resmi sebagai bukti kelulusan dari program studi',
        ],
        [
            'name' => 'final_transcript',
            'display_name' => 'Transkrip Nilai Akhir',
            'description' => 'Transkrip nilai akhir lengkap dari seluruh mata kuliah yang telah diselesaikan',
        ],
        [
            'name' => 'graduation_certificate',
            'display_name' => 'Surat Keterangan Lulus',
            'description' => 'Surat keterangan resmi bahwa mahasiswa telah lulus dari program studi',
        ],
        [
            'name' => 'study_completion_statement',
            'display_name' => 'Surat Pernyataan Telah Menyelesaikan Studi',
            'description' => 'Surat pernyataan resmi bahwa mahasiswa telah menyelesaikan seluruh program studi',
        ],
        // Additional Academic Document
        [
            'name' => 'additional_academic',
            'display_name' => 'Dokumen Tambahan (Akademik)',
            'description' => 'Dokumen tambahan untuk laporan akademik',
        ],
        // Additional for Study Requirements
        [
            'name' => 'additional_study_requirement',
            'display_name' => 'Dokumen Tambahan Persyaratan Studi',
            'description' => 'Dokumen tambahan untuk persyaratan studi lanjut',
        ],
        // Additional for Final Reports
        [
            'name' => 'additional_final_report',
            'display_name' => 'Dokumen Tambahan Laporan Akhir',
            'description' => 'Dokumen tambahan untuk laporan akhir dan kelulusan',
        ],
        // Additional for Approvals (already present, ensure correct)
        [
            'name' => 'additional_approval',
            'display_name' => 'Dokumen Tambahan Persetujuan',
            'description' => 'Dokumen tambahan untuk dokumen persetujuan',
        ],
        // 6. Dokumen Tambahan (Opsional, legacy)
        [
            'name' => 'additional',
            'display_name' => 'Dokumen Tambahan',
            'description' => 'Dokumen opsional lain yang diperlukan di luar kategori yang telah ditentukan',
        ],
    ];

    /**
     * Get document types by category
     */
    public static function getByCategory(string $category): array
    {
        $categories = [
            'study_requirements' => self::getStudyRequirementNames(),
            'approval_documents' => self::getApprovalDocumentNames(),
            'semester_documents' => self::getSemesterDocumentNames(),
            'final_documents' => self::getFinalDocumentNames(),
            'additional_documents' => ['additional'],
        ];

        if (!isset($categories[$category])) {
            return [];
        }

        return array_filter(self::DOCUMENT_TYPES, function ($type) use ($categories, $category) {
            return in_array($type['name'], $categories[$category]);
        });
    }

    /**
     * Get document type by name
     */
    public static function getByName(string $name): ?array
    {
        foreach (self::DOCUMENT_TYPES as $type) {
            if ($type['name'] === $name) {
                return $type;
            }
        }

        return null;
    }

    /**
     * Get all document type names
     */
    public static function getAllNames(): array
    {
        return array_column(self::DOCUMENT_TYPES, 'name');
    }

    /**
     * Get study requirement document type names for reusability
     */
    public static function getStudyRequirementNames(): array
    {
        return [
            'letter_of_acceptance',
            'scholarship_cover_letter', 
            'rector_permit_letter',
            'permanent_lecturer_decree',
            'diploma_certificate',
            's1_transcript',
            's2_transcript',
            'inpassing_decree',
            'jad_decree',
            'graduation_report_statement',
            'lldikti_assignment_statement',
            'willing_to_be_relieved_letter',
            'management_permit_letter',
            'work_period_certificate',
            'academic_recommendation_letter',
            'management_recommendation_letter',
            'additional_study_requirement',
        ];
    }

    /**
     * Get approval document type names for reusability
     */
    public static function getApprovalDocumentNames(): array
    {
        return [
            'study_compatibility',
            'application_minutes',
            'approval_minutes',
            'nde',
            'pid',
            'additional_approval',
        ];
    }

    /**
     * Get semester document type names for reusability
     */
    public static function getSemesterDocumentNames(): array
    {
        return ['lecturer_cover_letter', 'transcript', 'active_student_certificate', 'igracias_publication_proof', 'tuition_payment_proof', 'study_progress_certificate'];
    }

    /**
     * Get final document type names for reusability
     */
    public static function getFinalDocumentNames(): array
    {
        return ['diploma', 'final_transcript', 'graduation_certificate', 'study_completion_statement', 'additional_final_report'];
    }
}
