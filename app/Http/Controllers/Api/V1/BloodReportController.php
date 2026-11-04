<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BloodReportResource;
use App\Support\Journey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BloodReportController extends Controller
{
    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    public function index(Request $request)
    {
        $reports = BloodReport()->with('files')->where('user_uuid', auth()->user()->uuid)
            ->latest('report_date')
            ->paginate((int) $request->input('per_page', 10));

        return jsonResponse(true, __('api.blood_reports_fetched'), paginatedResponse($reports, BloodReportResource::class, 'reports'));
    }

    /**
     * Upload a report: report_date, files[] (pdf / images, multiple pages allowed), notes.
     * Send report_uuid to add more files to an existing report.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'report_uuid' => 'nullable|string',
            'report_date' => 'required_without:report_uuid|nullable|date|before_or_equal:' . Journey::today($user)->addDay()->toDateString(),
            'files'       => 'required|array|min:1|max:20',
            'files.*'     => 'file|mimes:' . config('tracking.report_mimes') . '|max:' . config('tracking.report_max_kb'),
            'notes'       => 'nullable|string|max:3000',
        ]);
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        if ($request->filled('report_uuid')) {
            $report = BloodReport()->where('user_uuid', $user->uuid)->where('uuid', $request->report_uuid)->first();
            if (!$report) {
                return jsonResponse(false, __('api.record_not_found'), null, 404);
            }
            $report->fill(array_filter([
                'report_date' => $request->report_date ? date('Y-m-d', strtotime($request->report_date)) : null,
                'notes'       => $request->notes,
            ]))->save();
        } else {
            $report = BloodReport()->create([
                'user_uuid'   => $user->uuid,
                'report_date' => date('Y-m-d', strtotime($request->report_date)),
                'notes'       => $request->notes,
            ]);
        }

        foreach ($request->file('files') as $file) {
            $path = uploadFile($file, 'blood-reports');
            if ($path) {
                BloodReportFile()->create([
                    'blood_report_uuid' => $report->uuid,
                    'file_path'         => $path,
                    'original_name'     => $file->getClientOriginalName(),
                    'mime_type'         => $file->getClientMimeType(),
                    'size'              => $file->getSize(),
                ]);
            }
        }

        activity_log(log: 'API Blood Report', event: 'blood_report_uploaded', subject: $report);

        return jsonResponse(true, __('api.blood_report_saved'), new BloodReportResource($report->load('files')));
    }

    public function destroy($uuid)
    {
        $report = BloodReport()->with('files')->where('user_uuid', auth()->user()->uuid)->where('uuid', $uuid)->first();
        if (!$report) {
            return jsonResponse(false, __('api.record_not_found'), null, 404);
        }

        deleteFiles($report->files->pluck('file_path')->all());
        $report->files()->delete();

        activity_log(log: 'API Blood Report', event: 'blood_report_deleted', subject: $report);
        $report->delete();

        return jsonResponse(true, __('api.blood_report_deleted'));
    }

    public function destroyFile($uuid)
    {
        $file = BloodReportFile()->where('uuid', $uuid)->first();
        $report = $file ? BloodReport()->where('uuid', $file->blood_report_uuid)->where('user_uuid', auth()->user()->uuid)->first() : null;
        if (!$report) {
            return jsonResponse(false, __('api.record_not_found'), null, 404);
        }

        deleteFiles($file->file_path);
        $file->delete();

        return jsonResponse(true, __('api.blood_report_file_deleted'), new BloodReportResource($report->load('files')));
    }
}
