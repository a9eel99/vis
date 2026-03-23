<?php

namespace App\Http\Controllers\Inspection;

use App\Application\Services\InspectionService;
use App\Application\Services\MediaService;
use App\Application\Services\TemplateService;
use App\Application\Services\UserService;
use App\Application\Services\VehicleService;
use App\Domain\DTOs\InspectionDTO;
use App\Domain\Models\Customer;
use App\Domain\Models\Vehicle;
use App\Http\Controllers\Controller;
use App\Http\Requests\InspectionRequest;
use App\Http\Requests\SubmitInspectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InspectionController extends Controller
{
    public function __construct(
        private InspectionService $inspectionService,
        private VehicleService $vehicleService,
        private TemplateService $templateService,
        private UserService $userService,
        private MediaService $mediaService,
    ) {}

    public function index(Request $request)
    {
        $inspections = $this->inspectionService->list(
            search: $request->get('search'),
            status: $request->get('status'),
            grade: $request->get('grade'),
            perPage: 15
        );

        $vehicles = Vehicle::orderBy('make')->get();
        $templates = $this->templateService->getActive();

        return view('inspections.index', compact('inspections', 'vehicles', 'templates'));
    }

    public function create()
    {
        $vehicles = Vehicle::orderBy('make')->get();
        $templates = $this->templateService->getActive();
        $inspectors = $this->userService->getInspectors();
        $customers = Customer::orderBy('name')->get();

        $vehiclesJson = $vehicles->map(fn($v) => [
            'id' => $v->id,
            'fuel_type' => $v->fuel_type,
            'label' => "{$v->year} {$v->make} {$v->model}" . ($v->license_plate ? " ({$v->license_plate})" : ''),
        ]);

        $templatesJson = $templates->map(fn($t) => [
            'id' => $t->id,
            'fuel_type' => $t->fuel_type,
            'name' => $t->name,
            'questions_count' => $t->questions()->count(),
        ]);

        return view('inspections.create', compact(
            'vehicles', 'templates', 'inspectors', 'customers', 'vehiclesJson', 'templatesJson'
        ));
    }

    public function store(InspectionRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();

                if ($request->input('vehicle_mode') === 'new') {
                    $customer = null;
                    $ownerName = $request->input('owner_name');
                    $ownerPhone = $request->input('owner_phone');

                    if ($ownerName || $ownerPhone) {
                        $customer = Customer::create([
                            'name' => $ownerName ?? '',
                            'phone' => $ownerPhone,
                            'email' => $request->input('owner_email'),
                            'created_by' => auth()->id(),
                        ]);
                    }

                    $vehicle = Vehicle::create([
                        'make' => $request->input('make'),
                        'model' => $request->input('model'),
                        'year' => $request->input('year'),
                        'color' => $request->input('color'),
                        'vin' => $request->input('vin'),
                        'license_plate' => $request->input('license_plate'),
                        'mileage' => $request->input('mileage'),
                        'fuel_type' => $request->input('fuel_type'),
                        'customer_id' => $customer?->id,
                        'owner_name' => $ownerName,
                        'owner_phone' => $ownerPhone,
                        'owner_email' => $request->input('owner_email'),
                    ]);

                    $data['vehicle_id'] = $vehicle->id;
                }

                $dto = InspectionDTO::fromArray($data);
                $inspection = $this->inspectionService->create($dto);

                Cache::forget('dashboard_stats');
                Cache::forget('dashboard_monthly');

                return redirect()->route('inspections.conduct', $inspection)
                    ->with('success', app()->getLocale() === 'ar'
                        ? 'تم إنشاء الفحص. ابدأ الفحص الآن.'
                        : 'Inspection created. Begin now.');
            });
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء إنشاء الفحص.' : 'Error creating inspection.');
        }
    }

    public function show(string $id)
    {
        $inspection = $this->inspectionService->find($id);
        return view('inspections.show', compact('inspection'));
    }

    public function conduct(string $id)
    {
        $inspection = $this->inspectionService->find($id);

        if ($inspection->status->value === 'draft') {
            $this->inspectionService->startInspection($id);
            $inspection = $this->inspectionService->find($id);
        }

        $existingAnswers = $inspection->results->keyBy('question_id');

        return view('inspections.conduct', compact('inspection', 'existingAnswers'));
    }

    public function submit(SubmitInspectionRequest $request, string $id)
    {
        try {
            $answers = $request->input('answers', []);
            $files = [];

            $mediaFiles = $request->file('media');
            if ($mediaFiles && is_array($mediaFiles)) {
                foreach ($mediaFiles as $questionId => $questionFiles) {
                    $files[$questionId] = is_array($questionFiles) ? $questionFiles : [$questionFiles];
                }
            }

            $inspection = $this->inspectionService->submitResults($id, $answers, $files);

            Cache::forget('dashboard_stats');
            Cache::forget('dashboard_monthly');

            return redirect()->route('inspections.show', $id)
                ->with('success', app()->getLocale() === 'ar'
                    ? 'تم إكمال الفحص بنجاح. التقييم: ' . $inspection->grade_label
                    : 'Inspection completed. Grade: ' . $inspection->grade_label);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء حفظ الفحص.' : 'Error submitting inspection.');
        }
    }

    public function cancel(string $id)
    {
        try {
            $this->inspectionService->cancel($id);
            Cache::forget('dashboard_stats');

            return redirect()->route('inspections.index')
                ->with('success', app()->getLocale() === 'ar' ? 'تم إلغاء الفحص.' : 'Inspection cancelled.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء إلغاء الفحص.' : 'Error cancelling inspection.');
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->inspectionService->delete($id);
            Cache::forget('dashboard_stats');
            Cache::forget('dashboard_monthly');

            return redirect()->route('inspections.index')
                ->with('success', app()->getLocale() === 'ar' ? 'تم حذف الفحص بنجاح.' : 'Inspection deleted.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء حذف الفحص.' : 'Error deleting inspection.');
        }
    }

    public function uploadMedia(Request $request, string $id)
    {
        $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => 'file|mimes:jpg,jpeg,png,webp|max:10240',
            'question_id' => 'nullable|uuid',
        ]);

        try {
            $inspection = $this->inspectionService->find($id);
            $this->mediaService->uploadForInspection(
                $inspection,
                $request->file('files'),
                $request->input('question_id')
            );

            return back()->with('success', app()->getLocale() === 'ar' ? 'تم رفع الملفات.' : 'Files uploaded.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء رفع الملفات.' : 'Error uploading files.');
        }
    }

    public function deleteMedia(string $mediaId)
    {
        try {
            $this->mediaService->delete($mediaId);
            return back()->with('success', app()->getLocale() === 'ar' ? 'تم حذف الملف.' : 'File deleted.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء حذف الملف.' : 'Error deleting file.');
        }
    }

    public function toggleHidden(Request $request, string $id)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403);
        }

        try {
            $inspection = \App\Domain\Models\Inspection::withHidden()->findOrFail($id);
            $lang = app()->getLocale();

            if ($inspection->is_hidden) {
                $inspection->showInspection();
                $msg = $lang === 'ar' ? 'تم إظهار الفحص — أصبح مرئياً للجميع.' : 'Inspection is now visible.';
            } else {
                $reason = $request->input('hidden_reason', '');
                $inspection->hideInspection($reason);
                $msg = $lang === 'ar' ? 'تم إخفاء الفحص — لن يظهر إلا لك.' : 'Inspection hidden.';
            }

            Cache::forget('dashboard_stats');

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ.' : 'An error occurred.');
        }
    }
}