<?php

namespace App\Constants;

class DocumentTypeConstants
{
    public const DOCUMENT_TYPES = [
        // 1. Dokumen Kelengkapan Studi Lanjut (Dosen)
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

        // 2. Dokumen Kesesuaian Studi Lanjut, Berita Acara, dan NDE (Staf FSDP)
        [
            'name' => 'study_compatibility',
            'display_name' => 'Dokumen Kesesuaian Studi Lanjut',
            'description' => 'Dokumen untuk memastikan kesesuaian program yang diambil',
        ],
        [
            'name' => 'minutes',
            'display_name' => 'Berita Acara Studi Lanjut',
            'description' => 'Notulen resmi untuk proses studi lanjut',
        ],
        [
            'name' => 'nde',
            'display_name' => 'NDE Studi Lanjut',
            'description' => 'Surat permintaan studi lanjut secara formal',
        ],

        // 3. Dokumen Perjanjian Ikatan Dinas (PID)
        [
            'name' => 'pid',
            'display_name' => 'Perjanjian Ikatan Dinas (PID)',
            'description' => 'Dokumen resmi yang telah ditandatangani oleh semua pemangku kepentingan',
        ],

        // 4. Dokumen Laporan Per Semester (LKS)
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
            'display_name' => 'Bukti Unggah Publikasi di Igracias',
            'description' => 'Bukti screenshot atau dokumen unggah publikasi ilmiah di sistem Igracias',
        ],
        [
            'name' => 'tuition_payment_proof',
            'display_name' => 'Bukti Pembayaran Biaya Pendidikan',
            'description' => 'Bukti pembayaran SPP atau biaya pendidikan lainnya',
        ],
        [
            'name' => 'study_progress_certificate',
            'display_name' => 'Surat Keterangan Progres Studi',
            'description' => 'Surat keterangan kemajuan studi dari institusi atau pembimbing',
        ],

        // 5. Dokumen Laporan Akhir dan Kelulusan
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

        // Bonus: Dokumen Tambahan (Opsional)
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
            'fsdp_documents' => ['study_compatibility', 'minutes', 'nde', 'pid'],
            'semester_documents' => self::getSemesterDocumentNames(),
            'final_documents' => self::getFinalDocumentNames(),
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
            'graduation_report_statement'
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
        return ['diploma', 'final_transcript', 'graduation_certificate', 'study_completion_statement'];
    }
}
