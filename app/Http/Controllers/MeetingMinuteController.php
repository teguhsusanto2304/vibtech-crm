<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Services\CommonService;
use App\Services\MeetingMinuteService;
use Barryvdh\DomPDF\Facade\Pdf; // Import the PDF Facade
use ZipArchive; // Import ZipArchive for creating ZIP files
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\MeetingMinute;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class MeetingMinuteController extends Controller
{
    protected $notificationService;
    protected $commonService;
    protected $meetingMinuteService;

    public function __construct(
        NotificationService $notificationService,
        CommonService $commonService,
        MeetingMinuteService $meetingMinuteService)
    {
        $this->middleware('auth');

        $this->notificationService = $notificationService;
        $this->commonService = $commonService;
        $this->meetingMinuteService = $meetingMinuteService;
    }
    
    public function index()
    {
        return view('meeting_minutes.index')->with('title', 'Meeting Minutes')->with('breadcrumb', ['Home', 'Meeting Minutes']);
    }

    public function list()
    {
        return view('meeting_minutes.list')->with('title', 'Your Meeting Minutes')->with('breadcrumb', ['Home', 'Your Meeting Minutes']);
    }

    public function all()
    {
        return view('meeting_minutes.all')->with('title', 'Your Meeting Minutes')->with('breadcrumb', ['Home', 'Your Meeting Minutes']);
    }

    public function create()
    {
        return $this->meetingMinuteService->create();
    }

    public function store(Request $request)
    {
        return $this->meetingMinuteService->store($request);
    }

    public function getMeetingMinutesData(Request $request)
    {
        return $this->meetingMinuteService->getMeetingMinutesData($request);
    }

    public function show($id)
    {
        return $this->meetingMinuteService->show($id);
    }

    public function bulkExportPdfx(Request $request)
    {
        return $this->meetingMinuteService->bulkExportPdf($request);
    }

    /**
     * Generate and force download a ZIP file containing PDFs of filtered meeting minutes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function bulkExportPdf(Request $request)
    {
        // Fetch meeting minutes based on filters
        $query = MeetingMinute::query()
            ->with(['attendees.user', 'savedBy'])
            ->orderBy('meeting_date', 'asc') // Order by date for consistent PDF naming in ZIP
            ->orderBy('start_time', 'asc');

        if ($request->filled('month')) {
            $query->whereMonth('meeting_date', (int) $request->input('month'));
        }
        if ($request->filled('year')) {
            $query->whereYear('meeting_date', (int) $request->input('year'));
        }

        $meetingMinutes = $query->get();

        if ($meetingMinutes->isEmpty()) {
            return redirect()->back()->with('error', 'No meeting minutes found for the selected filters to export.');
        }

        // Create a temporary directory for PDFs
        $tempDir = 'temp_pdfs_' . uniqid();
        Storage::disk('local')->makeDirectory($tempDir);
        $tempDirPath = storage_path('app/' . $tempDir);

        $pdfFiles = [];

        foreach ($meetingMinutes as $minute) {
            // Generate PDF for each meeting minute
            $pdf = Pdf::loadView('pdfs.meeting_minute_detail', compact('minute')); // Use a dedicated Blade view for PDF content
            
            // Define a clean filename for the PDF
            $filename = 'Meeting_Minutes_' . $minute->meeting_date->format('Y-m-d') . '_' . str_replace([' ', '/', '\\'], '_', $minute->topic) . '.pdf';
            $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '', $filename); // Sanitize filename
            $filePath = $tempDirPath . '/' . $filename;
            
            $pdf->save($filePath); // Save PDF to temporary directory
            $pdfFiles[] = $filePath;
        }

        // Create a ZIP archive
        $zipFileName = 'Meeting_Minutes_Export_' . Carbon::now()->format('Ymd_His') . '.zip';
        $zipFilePath = storage_path('app/' . $zipFileName);
        $zip = new ZipArchive;

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($pdfFiles as $file) {
                $zip->addFile($file, basename($file)); // Add each PDF to the ZIP
            }
            $zip->close();
        } else {
            // Clean up temporary PDFs if ZIP creation fails
            Storage::disk('local')->deleteDirectory($tempDir);
            return redirect()->back()->with('error', 'Failed to create ZIP archive.');
        }

        // Clean up temporary PDF files and directory after ZIP is created
        foreach ($pdfFiles as $file) {
            unlink($file); // Delete individual PDF files
        }
        //Storage::disk('local')->deleteDirectory($tempDir); // Delete the temporary directory

        // Return the ZIP file as a download

        
        
       return response()->download($zipFilePath, $zipFileName)->deleteFileAfterSend(true);

    }

    public function pdfPreview($id)
    {
        return $this->meetingMinuteService->pdfPreview($id);
    }

    public function downloadPdf( $id)
    {
        return $this->meetingMinuteService->downloadPdf($id);
    }

}
