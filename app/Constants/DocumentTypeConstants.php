<?php

namespace App\Constants;

class DocumentTypeConstants
{
    public const DOCUMENT_TYPES = [
        // 1. Dokumen Kelengkapan Studi Lanjut (Dosen)
        [
            'name' => 'personal_data',
            'display_name' => 'Data Pribadi',
            'description' => 'Data pribadi dan dokumen persyaratan awal untuk pengajuan studi lanjut (surat izin, ijazah sebelumnya, SK pengangkatan, dll)',
        ],
        [
            'name' => 'requirement',
            'display_name' => 'Dokumen Persyaratan',
            'description' => 'Dokumen-dokumen persyaratan untuk pengajuan studi lanjut',
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
            'name' => 'semester_report',
            'display_name' => 'Laporan Kemajuan Studi (LKS)',
            'description' => 'Laporan akademik, transkrip, atau form pemantauan studi per semester',
        ],

        // 5. Dokumen Laporan Akhir dan Kelulusan
        [
            'name' => 'final_report',
            'display_name' => 'Laporan Akhir (Disertasi/Tesis)',
            'description' => 'Dokumen disertasi atau tesis sebagai laporan akhir studi',
        ],
        [
            'name' => 'graduation_letter',
            'display_name' => 'Surat Kelulusan',
            'description' => 'Surat resmi kelulusan dari institusi pendidikan',
        ],
        [
            'name' => 'diploma',
            'display_name' => 'Ijazah',
            'description' => 'Ijazah resmi sebagai bukti kelulusan dari program studi',
        ],
        [
            'name' => 'final_transcript',
            'display_name' => 'Transkrip Akhir',
            'description' => 'Transkrip nilai akhir lengkap dari seluruh mata kuliah',
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
            'lecturer_requirements' => ['personal_data', 'requirement'],
            'fsdp_documents' => ['study_compatibility', 'minutes', 'nde'],
            'service_agreement' => ['pid'],
            'semester_reports' => ['semester_report'],
            'final_documents' => ['final_report', 'graduation_letter', 'diploma', 'final_transcript'],
            'additional' => ['additional'],
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
}
