<?php

namespace App\Livewire\StudyCalendar;

use Livewire\Component;
use App\Models\StudyCalendar;
use App\Models\StudyDetail;
use App\Traits\HasFormValidation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Create extends Component
{
    use HasFormValidation;

    public $step = 1;

    // Step 1
    public $start_date;
    public $end_date;
    public $total_semester;

    // Step 2
    public $university_name;
    public $university_address;
    public $study_program_id;
    public $study_level;
    public $scholarship;
    public $funding_source;
    public $study_address;

    // Step 3
    public $agreed = false;
    public $isLoading = false;
    public $hasValidationErrors = false;

    public $availableStudyPrograms = [];

    protected $rules = [
        'start_date' => 'required|date_format:Y-m-d',
        'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        'total_semester' => 'required|integer|min:1|max:20',
        'university_name' => 'required|string',
        'university_address' => 'required|string',
        'study_program_id' => 'required|integer|exists:study_programs,id',
        'study_level' => 'required|string',
        'scholarship' => 'nullable|string',
        'funding_source' => 'required|string',
        'study_address' => 'required|string',
        'agreed' => 'accepted',
    ];

    protected $messages = [
        'start_date.required' => 'Tanggal mulai studi wajib diisi.',
        'start_date.date_format' => 'Format tanggal mulai studi harus YYYY-MM-DD (contoh: 2024-01-01).',
        'end_date.required' => 'Tanggal selesai studi wajib diisi.',
        'end_date.date_format' => 'Format tanggal selesai studi harus YYYY-MM-DD (contoh: 2024-12-31).',
        'end_date.after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai.',
        'total_semester.required' => 'Total semester wajib diisi.',
        'total_semester.integer' => 'Total semester harus berupa angka.',
        'total_semester.min' => 'Total semester minimal 1 semester.',
        'total_semester.max' => 'Total semester maksimal 20 semester.',
        'university_name.required' => 'Nama universitas wajib diisi.',
        'university_address.required' => 'Alamat universitas wajib diisi.',
        'study_program_id.required' => 'Program studi wajib dipilih.',
        'study_program_id.integer' => 'Program studi tidak valid.',
        'study_program_id.exists' => 'Program studi tidak ditemukan.',
        'study_level.required' => 'Tingkat studi wajib dipilih.',
        'funding_source.required' => 'Sumber pendanaan wajib dipilih.',
        'study_address.required' => 'Alamat studi wajib diisi.',
        'agreed.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
    ];

    public function getStudyLevelsProperty()
    {
        return [
            'S3' => 'Doktoral',
            'Postdoc' => 'Post-doc',
            'Specialist' => 'Spesialis',
        ];
    }

    public function getFundingSourcesProperty()
    {
        return [
            'LPDP' => 'LPDP',
            'Pribadi' => 'Pribadi',
            'Instansi' => 'Instansi',
            'Perusahaan' => 'Perusahaan',
            'Yayasan' => 'Yayasan',
        ];
    }

    public function getProgressBarData()
    {
        $errorStep = $this->getErrorStep();

        // Determine which steps are completed based on data, not just current step
        $hasStep1Data = !empty($this->start_date) && !empty($this->end_date) && !empty($this->total_semester);
        $hasStep2Data = !empty($this->university_name) && !empty($this->university_address) &&
                       !empty($this->study_program_id) && !empty($this->study_level) &&
                       !empty($this->funding_source) && !empty($this->study_address);

        return [
            'current_phase' => $this->step,
            'total_phases' => 3,
            'error_step' => $errorStep,
            'phases' => [
                [
                    'id' => 1,
                    'name' => 'Informasi Dasar',
                    'status' => $this->step > 1 || $hasStep1Data ? 'completed' : ($this->step == 1 ? 'current' : 'pending'),
                    'has_error' => $errorStep === 1
                ],
                [
                    'id' => 2,
                    'name' => 'Detail Studi',
                    'status' => $this->step > 2 || $hasStep2Data ? 'completed' : ($this->step == 2 ? 'current' : 'pending'),
                    'has_error' => $errorStep === 2
                ],
                [
                    'id' => 3,
                    'name' => 'Konfirmasi',
                    'status' => $this->step == 3 ? 'current' : 'pending',
                    'has_error' => $errorStep === 3
                ],
            ],
        ];
    }

    public function updated($propertyName)
    {
        Log::debug('Property updated', [
            'property' => $propertyName,
            'value' => $this->{$propertyName},
            'step' => $this->step
        ]);
        $this->validateOnly($propertyName, $this->stepRules());
    }

    public function stepRules()
    {
        if ($this->step == 1) {
            return [
                'start_date' => $this->rules['start_date'],
                'end_date' => $this->rules['end_date'],
                'total_semester' => $this->rules['total_semester'],
            ];
        } elseif ($this->step == 2) {
            return [
                'university_name' => $this->rules['university_name'],
                'university_address' => $this->rules['university_address'],
                'study_program_id' => $this->rules['study_program_id'],
                'study_level' => $this->rules['study_level'],
                'scholarship' => $this->rules['scholarship'],
                'funding_source' => $this->rules['funding_source'],
                'study_address' => $this->rules['study_address'],
            ];
        } elseif ($this->step == 3) {
            return [
                'agreed' => $this->rules['agreed'],
            ];
        }
        return [];
    }

    public function nextStep()
    {
        $this->validate($this->stepRules(), $this->messages);
        if ($this->step < 3) {
            $this->step++;
            $this->clearErrors();
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
            $this->clearErrors();
        }
    }

    public function mount($initialData = [])
    {
        $this->availableStudyPrograms = \App\Models\StudyProgram::orderBy('name')->pluck('name', 'id')->toArray();

        Log::debug('StudyCalendar Create: mount() called', [
            'step' => $this->step,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_semester' => $this->total_semester,
            'university_name' => $this->university_name,
            'university_address' => $this->university_address,
            'study_program_id' => $this->study_program_id,
            'study_level' => $this->study_level,
            'scholarship' => $this->scholarship,
            'funding_source' => $this->funding_source,
            'study_address' => $this->study_address,
            'agreed' => $this->agreed,
        ]);
        // If you have any initialization logic, keep it here
    }

    public function submit()
    {
        Log::debug('StudyCalendar Create: submit() called', [
            'step' => $this->step,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_semester' => $this->total_semester,
            'university_name' => $this->university_name,
            'university_address' => $this->university_address,
            'study_program_id' => $this->study_program_id,
            'study_level' => $this->study_level,
            'scholarship' => $this->scholarship,
            'funding_source' => $this->funding_source,
            'study_address' => $this->study_address,
            'agreed' => $this->agreed,
        ]);
        Log::debug('Submit button pressed in StudyCalendar Create component');
        Log::debug('Before validation');

        try {
            $this->validate($this->rules, $this->messages);
            Log::debug('After validation');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::debug('Validation failed', [
                'errors' => $e->validator->errors()->toArray(),
                'current_step' => $this->step
            ]);

            // Get the first field that has an error
            $errorFields = array_keys($e->validator->errors()->toArray());
            $firstErrorField = $errorFields[0] ?? null;

            // Determine which step the error belongs to
            $errorStep = $this->getStepForField($firstErrorField);

            Log::debug('Error analysis', [
                'first_error_field' => $firstErrorField,
                'error_step' => $errorStep,
                'current_step' => $this->step
            ]);

            // Navigate to the step with the error
            if ($errorStep && $errorStep !== $this->step) {
                $this->step = $errorStep;
                session()->flash('error', 'Silakan perbaiki kesalahan pada langkah ' . $errorStep . ' sebelum melanjutkan.');
                Log::debug('Redirecting to step', ['new_step' => $this->step]);

                // Log the preserved data after redirect
                Log::debug('Data preserved after redirect', [
                    'step' => $this->step,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'total_semester' => $this->total_semester,
                    'university_name' => $this->university_name,
                    'university_address' => $this->university_address,
                    'study_program_id' => $this->study_program_id,
                    'study_level' => $this->study_level,
                    'scholarship' => $this->scholarship,
                    'funding_source' => $this->funding_source,
                    'study_address' => $this->study_address,
                    'agreed' => $this->agreed,
                ]);
            } else {
                // If error is on current step, show a more specific message
                $errorMessages = $e->validator->errors()->all();
                $errorMessage = implode(', ', $errorMessages);
                session()->flash('error', 'Silakan perbaiki kesalahan berikut: ' . $errorMessage);
                Log::debug('Staying on current step', ['step' => $this->step]);
            }

            // Dispatch event for JavaScript to handle scrolling
            $this->dispatch('validation-error');

            // Re-throw the validation exception so Livewire can handle it
            throw $e;
        }

        $this->isLoading = true;

        // Debug: Log the form data
        Log::info('StudyCalendar Create - Form data:', [
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_semester' => $this->total_semester,
            'university_name' => $this->university_name,
            'university_address' => $this->university_address,
            'study_program_id' => $this->study_program_id,
            'study_level' => $this->study_level,
            'scholarship' => $this->scholarship,
            'funding_source' => $this->funding_source,
            'study_address' => $this->study_address,
            'agreed' => $this->agreed,
        ]);

        DB::beginTransaction();
        try {
            $employee = Auth::user()->employee;

            // Debug: Check if employee exists
            if (!$employee) {
                throw new \Exception('Employee not found for user: ' . Auth::id());
            }

            $calendar = StudyCalendar::create([
                'employee_id' => $employee->id,
                'study_start' => $this->start_date,
                'estimated_study_end' => $this->end_date,
                'workflow_state' => 0,
            ]);

            // Debug: Log the created calendar
            Log::info('StudyCalendar created:', ['id' => $calendar->id]);

            StudyDetail::create([
                'study_calendar_id' => $calendar->id,
                'university_name' => $this->university_name,
                'university_address' => $this->university_address,
                'university_email' => '', // Add default value for required field
                'university_phone' => '', // Add default value for required field
                'study_program_id' => $this->study_program_id,
                'study_level' => $this->study_level,
                'total_semester' => $this->total_semester,
                'scholarship' => $this->scholarship,
                'funding_source' => $this->funding_source,
                'study_address' => $this->study_address,
            ]);

            // Debug: Log success
            Log::info('StudyDetail created successfully');

            DB::commit();
            session()->flash('success', 'Kalender studi berhasil dibuat!');
            return redirect()->route('study-calendar.manage');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('StudyCalendar Create Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Determine which step a field belongs to
     */
    private function getStepForField($fieldName)
    {
        $step1Fields = ['start_date', 'end_date', 'total_semester'];
        $step2Fields = ['university_name', 'university_address', 'study_program_id', 'study_level', 'scholarship', 'funding_source', 'study_address'];
        $step3Fields = ['agreed'];

        if (in_array($fieldName, $step1Fields)) {
            return 1;
        } elseif (in_array($fieldName, $step2Fields)) {
            return 2;
        } elseif (in_array($fieldName, $step3Fields)) {
            return 3;
        }

        return null;
    }

    /**
     * Get the step that has validation errors
     */
    public function getErrorStep()
    {
        $errorBag = $this->getErrorBag();

        if ($errorBag->any()) {
            $this->hasValidationErrors = true;
            $errorFields = array_keys($errorBag->toArray());

            foreach ($errorFields as $field) {
                $step = $this->getStepForField($field);
                if ($step) {
                    return $step;
                }
            }
        } else {
            $this->hasValidationErrors = false;
        }

        return null;
    }

    /**
     * Check if the current step is valid
     */
    public function isCurrentStepValid()
    {
        try {
            $this->validate($this->stepRules(), $this->messages);
            return true;
        } catch (\Illuminate\Validation\ValidationException $e) {
            return false;
        }
    }

    /**
     * Get validation errors for the current step
     */
    public function getCurrentStepErrors()
    {
        try {
            $this->validate($this->stepRules(), $this->messages);
            return [];
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $e->validator->errors()->toArray();
        }
    }

    /**
     * Navigate to the step that has validation errors
     */
    public function goToErrorStep()
    {
        $errorStep = $this->getErrorStep();
        if ($errorStep && $errorStep !== $this->step) {
            $this->step = $errorStep;
            session()->flash('error', 'Silakan perbaiki kesalahan pada langkah ' . $errorStep . ' sebelum melanjutkan.');
        }
    }

    /**
     * Check if there are validation errors on other steps
     */
    public function hasErrorsOnOtherSteps()
    {
        $errorStep = $this->getErrorStep();
        return $errorStep !== null && $errorStep !== $this->step;
    }

    /**
     * Reset form data
     */
    public function resetFormData()
    {
        $this->reset([
            'start_date', 'end_date', 'total_semester', 'university_name', 'university_address',
            'study_program_id', 'study_level', 'scholarship', 'funding_source', 'study_address', 'agreed'
        ]);
        $this->step = 1;
        $this->clearErrors();
    }

    public function render()
    {
        return view('livewire.study-calendar.create');
    }
}
