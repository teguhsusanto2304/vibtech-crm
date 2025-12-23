<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class LeaveApplicationController extends Controller
{
    public function index()
    {
        return view('leave_application.index')->with('title', 'Leave Application')->with('breadcrumb', ['Home', 'Staff Task', 'Leave Application']);
    }

    public function create()
    {
        return view('leave_application.form')->with('title', 'Upload Public Holiday')->with('breadcrumb', ['Home', 'Staff Task', 'Upload Public Holiday']);
    }

    public function edit($id)
    {
        $leaveApplication = LeaveApplication::findorFail($id);
        return view('leave_application.edit',compact('leaveApplication'))->with('title','Edit Public Holiday')->with('breadcrumb', ['Home', 'Staff Task', 'Edit Public Holiday']);
    }

    public function list()
    {
        
        $defaultCountry = session('defaultCountry') ?? 'SG';
        return view('leave_application.list',compact('defaultCountry'))->with('title', 'Manage Public Holiday')->with('breadcrumb', ['Home', 'Staff Task','Manage Public Holiday']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_code' => 'required|string|size:2',
            'leave_date'=> 'required|date',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:150',
        ]);
        $exists = LeaveApplication::where('country_code', $validated['country_code'])
            ->where('leave_date', $validated['leave_date'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Leave application already exists for this date and country.');
        }

        // ✅ 3. Store data
        LeaveApplication::create([
            'country_code' => $validated['country_code'],
            'leave_date'   => $validated['leave_date'],
            'title'        => $validated['title'],
            'description'  => $validated['description'],
        ]);

        // ✅ 4. Redirect with success message
        return redirect('v1/leave-application/list')
            ->with('defaultCountry',$validated['country_code'])
            ->with('success', 'Leave application has been successfully created.');
    }

    public function update(Request $request, $id)
    {
        // ✅ Validate input
        $validated = $request->validate([
            'country_code' => 'required|in:SG,MY',
            'leave_date'   => 'required|date',
            'title'        => 'required|string|max:150',
            'description'  => 'nullable|string|max:200',
        ]);
        $leave = LeaveApplication::findOrFail($id);

        // ✅ Update record
        $leave->update($validated);

        // ✅ Redirect with success message
        return redirect('v1/leave-application/list')
            ->with('defaultCountry',$validated['country_code'])
            ->with('success', 'Leave application has been successfully updated.');
    }

    public function destroy($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        $leave->delete();
        return response()->json([
            'message' => 'Public Holiday deleted successfully'
        ]);
    }

    public function getLeaveApplicationData(Request $request)
    {
        $query = LeaveApplication::query()
            ->orderBy('leave_date', 'asc');
            //->orderBy('created_at', 'desc');

        // Filter by month (leave_date)
        if ($request->filled('month')) {
            $query->whereMonth('leave_date', (int) $request->month);
        }

        // Filter by year (leave_date)
        if ($request->filled('year')) {
            $query->whereYear('leave_date', (int) $request->year);
        }

        // Filter by country
        if ($request->filled('country_code')) {
            $query->where('country_code', $request->country_code);
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()

            ->addColumn('country', function (LeaveApplication $leave) {
                return match ($leave->country_code) {
                    'SG' => 'Singapore',
                    'MY' => 'Malaysia',
                    default => $leave->country_code,
                };
            })

            ->addColumn('leave_date_formatted', function (LeaveApplication $leave) {
                return $leave->leave_date->format('d M Y');
            })

            ->addColumn('title', function (LeaveApplication $leave) {
                return e($leave->title);
            })

            ->addColumn('description', function (LeaveApplication $leave) {
                return $leave->description
                    ? e($leave->description)
                    : '<span class="text-muted">-</span>';
            })

            ->addColumn('created_at_formatted', function (LeaveApplication $leave) {
                return $leave->created_at->format('d M Y H:i');
            })

            ->addColumn('action', function (LeaveApplication $leave) {

                $btn  = '<a href="' . route('v1.leave-application.edit', ['id' => $leave->id]) . '" ';
                $btn .= 'class="btn btn-warning btn-sm me-1">Edit</a>';


                $btn .= '<button class="btn btn-danger btn-sm btn-delete" ';
                $btn .= 'data-id="'.$leave->id.'">Delete</button>';

                return $btn;
            })

            ->rawColumns(['description', 'action'])
            ->make(true);
    }

    public function downloadTemplate(Request $request): StreamedResponse
    {
        $year = $request->query('year', date('Y'));
        $country = $request->query('country','SG');
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=leave_application_template_{$year}_{$country}.csv",
        ];


        $callback = function () use ($year) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'leave_date (MM/DD/YYYY)',
                'title'
            ]);
            //$year = '2025';

            // Example row
            fputcsv($file, [
                '01/01/'. $year,
                'New Year Holiday'
            ]);
            fputcsv($file, [
                'MM/DD/YYYY',
                'Public Holiday Title'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importOld(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'country' => 'required|string|size:2',
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $year = $request->year;
        $countryCode = $request->country;
        $file = $request->file('file');

        DB::beginTransaction();

        try {
            $handle = fopen($file->getRealPath(), 'r');

            // Read header
            $header = fgetcsv($handle);
            if (!$header || $header !== ['leave_date (MM/DD/YYYY)', 'title']) {
                throw new \Exception('Invalid CSV format.');
            }

            while (($row = fgetcsv($handle)) !== false) {
                [$leaveDate, $title] = $row;

                // Validate year consistency
                if (date('Y', strtotime($leaveDate)) != $year) {
                    continue; // skip invalid year rows
                }
                LeaveApplication::where('country_code', $countryCode)
                    ->where('leave_date', $leaveDate)
                    ->delete();
                LeaveApplication::updateOrCreate(
                    [
                        'country_code' => $countryCode,
                        'leave_date' => $leaveDate,
                    ],
                    [
                        'title' => $title
                    ]
                );
            }

            fclose($handle);
            DB::commit();

            return redirect('v1/leave-application/list')->with('defaultCountry',$countryCode)->with('success', 'Public holiday imported successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'country' => 'required|string|size:2',
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $year = $request->year;
        $countryCode = $request->country;
        $file = $request->file('file');

        DB::beginTransaction();

        try {
            $handle = fopen($file->getRealPath(), 'r');

            // ✅ Read header
            $header = fgetcsv($handle);
            if (!$header || $header !== ['leave_date (MM/DD/YYYY)', 'title']) {
                throw new \Exception('Invalid CSV format. Expected: leave_date,title');
            }

            while (($row = fgetcsv($handle)) !== false) {
                [$leaveDateRaw, $title] = $row;

                $leaveDate = null;

                // ✅ Allow multiple date formats
                $allowedFormats = [
                    'm/d/Y', // 04/23/2026
                    'm/d/y', // 04/23/26
                ];

                foreach ($allowedFormats as $format) {
                    try {
                        $leaveDate = Carbon::createFromFormat($format, trim($leaveDateRaw));
                        break;
                    } catch (\Exception $e) {
                        // try next format
                    }
                }

                // ❌ Skip invalid date
                if (!$leaveDate) {
                    continue;
                }

                // ✅ Normalize to Y-m-d
                $leaveDateFormatted = $leaveDate->format('Y-m-d');

                // ✅ Validate year
                if ($leaveDate->year != $year) {
                    continue;
                }

                // ✅ Save / Update
                LeaveApplication::updateOrCreate(
                    [
                        'country_code' => $countryCode,
                        'leave_date' => $leaveDateFormatted,
                    ],
                    [
                        'title' => $title,
                    ]
                );
            }

            fclose($handle);
            DB::commit();

            return redirect('v1/leave-application/list')
                ->with('defaultCountry', $countryCode)
                ->with('success', 'Public holiday imported successfully into staff calendar.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


}
