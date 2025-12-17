<?php
namespace App\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SubmitClaim;
use App\Models\SubmitClaimApproval;
use App\Models\SubmitClaimItem;
use App\Models\ClaimType;
use App\Models\ExchangeRate;
use App\Models\User;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Helpers\IdObfuscator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response; // Import Response facade
use Illuminate\Support\Facades\Log; // For logging errors
use Auth;
use Illuminate\Validation\Rule;
use App\Notifications\UserNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rules\Can;

class SubmitClaimService {

    /**
     * Update the description of a specific claim.
     */
    public function updateDescription(Request $request, $id)
    {
        $claim = SubmitClaim::find($id);
        // 1. Validation
        $validated = $request->validate([
            'description' => 'required|string|max:500', 
            // The claim_id hidden field is often redundant if using route model binding
        ]);

        // 2. Update the model instance retrieved via Route Model Binding
        // $claim already contains the Claim model instance for the ID in the URL
        $claim->description = $validated['description'];
        $claim->save();

        // 3. Return a response
        
        // If NOT using AJAX (the simple form submit):
        //return redirect()->back()->with('success', 'Claim title updated successfully.');

        // If using AJAX (like the commented out JS):
        
        return response()->json([
            'success' => true, 
            'new_description' => $claim->description,
            'message' => 'Updated successfully'
        ]);
        
    }
    /**
     * Store a newly created claim in storage.
     */
    public function store(Request $request)
    {
        $submitClaimId = null;
        if ($request->input('submit_claim_id')) {
            $submitClaimId = IdObfuscator::decode($request->input('submit_claim_id'));
        }
        // 1. Validate the incoming request data
        $request->validate([
            'claim_serial_number' => 'nullable|string|max:255|unique:submit_claims,serial_number',
           /*  'claim_type_id' => [
                'required',
                'exists:claim_types,id',
                // Unique validation: claim_type_id must be unique for this submit_claim_id
                Rule::unique('submit_claim_items')->where(function ($query) use ($submitClaimId, $request) {
                    return $query->where('submit_claim_id', $submitClaimId)
                    ->where('start_at',$request->start_at)
                    ->where('end_at',$request->end_at)
                    ->where('data_status','!=',0);
                }),
            ],**/
            'claim_type_id' => [
                'required',
                'exists:claim_types,id',
            ],
            'start_at' => 'required|date|before_or_equal:today',
            'end_at' => 'required|date|before_or_equal:today|after_or_equal:start_at',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required',
            'project_files' => 'required|array|max:5', // Max 5 new files
            'project_files.*' => 'file|mimes:png,jpg,pdf,doc,docx|max:10240',
            'description' => 'required',
            'claim_purpose'=> 'required'
        ],
        [
            // Custom messages for the 'project_files' array itself
            'project_files.required' => 'Please upload at least one documentation file.',
            'project_files.array' => 'Documentation files must be provided as a list.', // Less common to trigger this
            'project_files.max' => 'You can upload a maximum of :max documentation files.', // :max will be replaced by 5

            // Custom messages for each individual file within the 'project_files' array
            'project_files.*.file' => 'Each uploaded item must be a valid file.',
            'project_files.*.mimes' => 'Only PNG, JPG, PDF, DOC, and DOCX files are allowed for documentation.',
            'project_files.*.max' => 'Each documentation file must not exceed 10MB in size.', // Laravel passes KB, so mention MB here for clarity
        ]);

        // Use a database transaction to ensure atomicity
        // If any part fails, all changes are rolled back.
        try {
            DB::beginTransaction();
            $totalAmount = 0;
            if($request->input('submit_claim_id')){

                 $decodedId = IdObfuscator::decode($request->input('submit_claim_id'));
                $claim = SubmitClaim::find($decodedId);
                $totalAmount = $claim->total_amount;                

            } else {

                // 2. Create the main Claim record
                $claim = SubmitClaim::create([
                    'serial_number' => $this->generateSerialNumber(),
                    'claim_date' => date('Y-m-d'),
                    'staff_id' => auth()->user()->id,
                    'data_status' => 1, // Default status
                    'total_amount' => 0, // Will be updated after saving items
                    'currency' => $request->currency,
                ]);

            }            

            $submitClaimItem = SubmitClaimItem::create([
                    'submit_claim_id' => $claim->id,
                    'description' => $request->claim_purpose,
                    'amount' => $request->amount,
                    'currency' => $request->currency,
                    'claim_type_id' => $request->claim_type_id,
                    'start_at' => $request->start_at,
                    'end_at' => $request->end_at,
                    'data_status' => 1,
                ]);

                 $totalAmount = $totalAmount+$request->amount;
                
            if ($request->hasFile('project_files')) {
                foreach ($request->file('project_files') as $index => $file) {
                    $originalFileName = $file->getClientOriginalName();
                    $fileMimeType = $file->getClientMimeType();
                    $fileSize = $file->getSize(); // Size in bytes

                    // Store file in storage/app/public/projects/{project_id}/files
                    $path = $file->store('submit_claim/' . $submitClaimItem->id . '/files', 'public');

                    // Save file details to project_files table
                    $submitClaimItem->files()->create([
                        'file_name' => $originalFileName,
                        'file_path' => $path, // The path returned by store()
                        'mime_type' => $fileMimeType,
                        'file_size' => $fileSize,
                        'description' => $request->input('project_file_descriptions')[$index] ?? null,
                        'uploaded_by_user_id' => Auth::id(),
                    ]);
                }
            }

            // 4. Update the total_amount for the main claim
            $claim->update(['total_amount' => $totalAmount,'description'=>$request->description]);

            DB::commit();
            Cache::forget('submit_claim_items_' . $submitClaimItem->id);

            // 5. Return a success response
            return redirect()->route('v1.submit-claim.detail',['id'=>$claim->obfuscated_id])->with('success', 'Claim has been stored successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            \Log::error('Claim submission failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withErrors(['errors' => 'Failed to submit claim. Please try again.'.$e->getMessage()])->withInput();

        }
    }

    /**
     * Update the specified submit claim in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $obfuscatedId  The obfuscated ID of the SubmitClaim.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Find the SubmitClaim using the obfuscated ID
            //$decodedId = IdObfuscator::decode($request->input('submit_claim_id'));
            //$submitClaim = SubmitClaim::where('id', $decodedId)->firstOrFail();

            $itemDecodedId = IdObfuscator::decode($id);
            $submitClaimItem = SubmitClaimItem::find($itemDecodedId);

            $validatedData = $request->validate([
                'claim_serial_number' => 'nullable|string|max:255|unique:submit_claims,serial_number',
            'claim_type_id' => [
                    'required',
                    'exists:claim_types,id',
                ],
                'start_at' => 'required|date|before_or_equal:today',
                'end_at' => 'required|date|before_or_equal:today|after_or_equal:start_at',
                'amount' => 'required|numeric|min:0.01',
                'currency' => 'required',
                'project_files' => 'nullable|array|max:5', // Max 5 new files
                'project_files.*' => 'file|mimes:png,jpg,pdf,doc,docx|max:10240',
                'description' => 'required',
                'claim_purpose'=> 'required'
            ],
            [
                // Custom messages for the 'project_files' array itself
                'project_files.array' => 'Documentation files must be provided as a list.', // Less common to trigger this
                'project_files.max' => 'You can upload a maximum of :max documentation files.', // :max will be replaced by 5

                // Custom messages for each individual file within the 'project_files' array
                'project_files.*.file' => 'Each uploaded item must be a valid file.',
                'project_files.*.mimes' => 'Only PNG, JPG, PDF, DOC, and DOCX files are allowed for documentation.',
                'project_files.*.max' => 'Each documentation file must not exceed 10MB in size.', // Laravel passes KB, so mention MB here for clarity
            ]);

            // Update main SubmitClaim details
             $submitClaimItem->claim_type_id = $validatedData['claim_type_id'];
            $submitClaimItem->start_at = $validatedData['start_at'];
            $submitClaimItem->end_at = $validatedData['end_at'];
            $submitClaimItem->amount = $validatedData['amount'];
            $submitClaimItem->currency = $validatedData['currency'];
            $submitClaimItem->description = $validatedData['claim_purpose']; // Assuming this maps to the claim_purpose field

            $submitClaimItem->save(); 
            // If serial_number was editable, you'd update it here:
            // $submitClaim->serial_number = $validatedData['claim_serial_number'] ?? $submitClaim->serial_number;


            // Handle new file uploads
            if ($request->hasFile('project_files')) {
                foreach ($request->file('project_files') as $index => $file) {
                    $originalFileName = $file->getClientOriginalName();
                    $fileMimeType = $file->getClientMimeType();
                    $fileSize = $file->getSize(); // Size in bytes

                    // Store file in storage/app/public/projects/{project_id}/files
                    $path = $file->store('submit_claim/' . $submitClaimItem->id . '/files', 'public');

                    // Save file details to project_files table
                    $submitClaimItem->files()->create([
                        'file_name' => $originalFileName,
                        'file_path' => $path, // The path returned by store()
                        'mime_type' => $fileMimeType,
                        'file_size' => $fileSize,
                        'description' => $request->input('project_file_descriptions')[$index] ?? null,
                        'uploaded_by_user_id' => Auth::id(),
                    ]);
                }
            }

            DB::commit();
            Cache::forget('submit_claim_items_' . $submitClaimItem->id);

            return redirect()->route('v1.submit-claim.detail', ['id' => $submitClaimItem->submitClaim->obfuscated_id])
                             ->with('success', 'Claim updated successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();
            // This will automatically redirect back with errors for web requests
            // For AJAX, it will return a 422 JSON response
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error updating submit claim: {$e->getMessage()}", ['exception' => $e, 'obfuscated_id' => $id]);
            return redirect()->back()->with('error', 'An unexpected error occurred while updating the claim.'.$e->getMessage())->withInput();
        }
    }

    /**
     * Optional: Generate a unique serial number (if not auto-generated client-side)
     */
    public function generateSerialNumber()
    {
        $serialNumber = 'CLM-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        // Ensure uniqueness in a real scenario (e.g., loop until unique)
        while (SubmitClaim::where('serial_number', $serialNumber)->exists()) {
            $serialNumber = 'CLM-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        }
        return $serialNumber;
    }

    public function getSubmitClaimType()
    {
        return ClaimType::where('data_status',1)->orderBy('id','ASC')->get();
    }

    public function getSubmitClaimsData(Request $request)
    {
        $submitClaimsQuery = SubmitClaim::query()
            ->where('data_status', '!=', 0)
            ->with(['staff', 'submitClaimItems'])
            ->orderBy('created_at', 'DESC'); // ✅ Keep as query builder
            if ($request->has('status_filter') && $request->input('status_filter') !== '') {
                $status = $request->input('status_filter');
                $submitClaimsQuery->whereIn('data_status', [2,3]);
            } else {
                $submitClaimsQuery->where('staff_id', auth()->user()->id);
            }

        return DataTables::eloquent($submitClaimsQuery) // ✅ Correct: passing builder, not collection
            ->addIndexColumn()

            ->addColumn('staff', function ($claim) {
                $avatarUrl = $claim->staff->avatar_url ?? 'https://placehold.co/40x40/cccccc/333333?text=N/A';
                $staffName = $claim->staff->name ?? 'Unknown Staff';

                return '<img src="'.$avatarUrl.'" alt="'.$staffName.' Avatar"
                            class="rounded-circle me-2" data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            width="40" height="40">&nbsp;'.$staffName;
            })

            ->addColumn('submit_claim_item_count', fn($claim) =>
                $claim->submitClaimItems->where('data_status','!=',0)->count().' item(s)'
            )

            ->addColumn('total_amount_currency', function ($claim) {
                $totals = $claim->total_by_currency; // Access the accessor

                if ($totals->isEmpty()) {
                    return 'N/A'; // Or an empty string, or '0.00'
                }

                // Format the totals into a string, e.g., "MYR 1,500.00, SGD 1,234.00"
                // Or you can use a list for better readability
                $output = [];
                foreach ($totals as $total) {
                    $output[] = $total['formatted_total'];
                }
                // return implode(', ', $output); // Simple comma separated

                // For better multi-line display in a table cell, use HTML list or <br>
                return '<div style="text-align: right;">' . implode('<br>', $output) . '</div>'; // Use <br> for new lines
            })
            ->addColumn('claim_date',fn($claim) => 
                $claim->created_at->format('d M Y h:i')
            )
            ->addColumn('claim_status', function ($claim) {
                return $claim->submit_claim_status;
            })

            ->addColumn('action', function ($claim)  use ($request) {
                $btn = '<div class="btn-group btn-group-vertical" role="group" aria-label="Claim Actions">';
                if($claim->data_status==1){
                    $btn .= '<a class="btn btn-primary btn-sm" href="'.route("v1.submit-claim.create").'?id='.$claim->obfuscated_id.'">Create Item</a>';
                    $btn .= '<a href="#" class="btn btn-danger btn-sm delete-claim-btn" data-id="'.$claim->obfuscated_id.'">Delete</a>';
                }
                
                if($request->input('status_filter')){
                    $btn .= '<a class="btn btn-info btn-sm" href="' . route('v1.submit-claim.detail', ['id' => $claim->obfuscated_id,'from'=>'all']) . '">View</a>';
                } else {
                    $btn .= '<a class="btn btn-info btn-sm" href="' . route('v1.submit-claim.detail', ['id' => $claim->obfuscated_id]) . '">View</a>';
                }             
                
                 $btn .= '</div>';
                return $btn;
            })

            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Remove the specified SubmitClaim from storage.
     *
     * @param  string  $id  (obfuscated ID of the SubmitClaim)
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyClaim($id) // Renamed method
    {
        try {
            $decodedId = IdObfuscator::decode($id);
            $claim = SubmitClaim::find($decodedId);

            // You might want to add a check here, e.g., only allow deletion if data_status is 1 (Draft)
            if ($claim->data_status != 1) { // Example: Only allow deletion if it's a draft
                return Response::json(['message' => 'Claim cannot be deleted unless it is in draft status.'], 403);
            }

            // Before deleting the claim, you might need to handle its associated items/files
            // For example, delete related submit_claim_items first if not handled by CASCADE DELETE in DB
            // $claim->submitClaimItems()->delete(); // If not using CASCADE DELETE

            $claim->data_status=0;
            $claim->save();

            // Invalidate any relevant caches for the main claims list
            Cache::forget('all_submit_claims_list_cache_key'); // Example: if you cache a general list
            // If the getSubmitClaimsData method has a cache, invalidate it.
            // This is important because the claim is removed from the list.
            // If getSubmitClaimsData filters by staff_id or status, you might need more specific invalidation.
            // For simplicity, if it's a general list, clear its cache.

            Log::info("SubmitClaim with ID {$decodedId} deleted successfully.");
            return Response::json(['message' => 'Claim deleted successfully.'], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("SubmitClaim with ID {$id} not found for deletion. Error: " . $e->getMessage());
            return Response::json(['message' => 'Claim not found.'], 404);
        } catch (\Exception $e) {
            Log::error("Error deleting SubmitClaim with ID {$id}. Error: " . $e->getMessage());
            return Response::json(['message' => 'Failed to delete claim. An error occurred.'], 500);
        }
    }

    public function getSubmitClaimItemsData(Request $request,$submit_claim_id)
    {
        $submitClaimItems = SubmitClaimItem::query()
                ->where('data_status', '!=', SubmitClaim::STATUS_DELETED)
                ->where('submit_claim_id', $submit_claim_id)
                ->orderBy('created_at', 'DESC')
                ->get();     


        return DataTables::collection($submitClaimItems) // ✅ Correct: passing builder, not collection
            ->addIndexColumn()   
            ->addColumn('claim_type', fn($claim) =>
                $claim->claimType->name
            )
            ->addColumn('amount_currency', fn($claim) =>
                $claim->currency . ' ' . number_format($claim->amount, 2)
            )
            ->addColumn('currency', fn($claim) => $claim->currency)
            ->addColumn('start_date',fn($claim) => 
                $claim->start_at->format('d M Y')
            )
            ->addColumn('end_date',fn($claim) => 
                $claim->end_at->format('d M Y')
            )
            ->addColumn('created_date',fn($claim) => 
                $claim->created_at->format('d M Y h:i')
            )
            ->addColumn('claim_status', function ($claim) {
                $notes = null;
                $verified_by = null;
                $verified_at = null;
                foreach($claim->submitClaimItemApproval as $claimReject){
                    $notes .= $claimReject->notes;
                    $verified_by .= $claimReject->approver->name;
                    $verified_at = $claimReject->created_at->format('d M Y H:i:s');
                }
                
                if($claim->submitClaim->data_status == SubmitClaim::STATUS_DRAFT){
                    $submitClaimItemStatus = '<span class="badge bg-warning">Draft</span>';
                } else if($claim->submitClaim->data_status == SubmitClaim::STATUS_SUBMIT){
                    $submitClaimItemStatus = '<span class="badge bg-info">Submitted</span>';
                } else {
                    $submitClaimItemStatus = '<span class="badge bg-info">Submitted</span>';
                }
                return match ($claim->data_status) {
                    1 => $submitClaimItemStatus,
                    3 => '<span class="badge bg-success">Approved</span><div class="container-fluid p-0">

                        <!-- Row for Rejected By -->
                        <div class="row g-0 mb-1 align-items-baseline">
                            <div class="col-6 text-nowrap"><small><strong>Approved By</strong></small></div>
                            <div class="col-auto me-1">:<small></small></div>
                            <div class="col"><small>'.$verified_by.'</small></div>
                        </div>

                        <!-- Row for Rejected At -->
                        <div class="row g-0 align-items-baseline">
                            <div class="col-4 text-nowrap"><small><strong>Approved At</strong></small></div>
                            <div class="col-auto me-1">:<small></small></div>
                            <div class="col"><small>'.$verified_at.'</small></div>
                        </div>
                    </div>',
                    4 => '<span class="badge bg-danger">Rejected</span>
                    <div class="container-fluid p-0">
                        <!-- Row for Reason -->
                        <div class="row g-0 mb-1 align-items-baseline">
                            <div class="col-4 text-nowrap"><small><strong>Reason</strong></small></div>
                            <div class="col-auto me-1">:<small></small></div>
                            <div class="col"><small>'.$notes.'</small></div>
                        </div>

                        <!-- Row for Rejected By -->
                        <div class="row g-0 mb-1 align-items-baseline">
                            <div class="col-6 text-nowrap"><small><strong>Rejected By</strong></small></div>
                            <div class="col-auto me-1">:<small></small></div>
                            <div class="col"><small>'.$verified_by.'</small></div>
                        </div>

                        <!-- Row for Rejected At -->
                        <div class="row g-0 align-items-baseline">
                            <div class="col-4 text-nowrap"><small><strong>Rejected At</strong></small></div>
                            <div class="col-auto me-1">:<small></small></div>
                            <div class="col"><small>'.$verified_at.'</small></div>
                        </div>
                    </div>',
                    default => '<span class="badge bg-secondary">Unknown Status</span>',
                };
            })

            ->addColumn('action', function ($claim) {
                $btn = '<div class="btn-group btn-group" role="group" aria-label="Claim Actions">';
                $btn .= '<a class="btn btn-info btn-sm view-item-btn" href="#" data-id="' . $claim->obfuscated_id . '">View</a>'; // Changed href to # and added class/data-id
                
                if (auth()->user()->can('adjust-claim')) {
                    $btn .= '<a class="btn btn-warning btn-sm adjust-item-btn"
                                href="#"
                                data-id="'.$claim->obfuscated_id.'"
                                data-amount="'.$claim->amount.'">Adjust</a>';
                }
                if($claim->submitClaim->data_status==1){
                    $btn .= '<a class="btn btn-warning btn-sm" href="'.route('v1.submit-claim.edit',['id'=>$claim->obfuscated_id]).'" disabled>Edit</a>';
                    $btn .= '<a class="btn btn-danger btn-sm delete-item-btn" href="#" data-id="' . $claim->obfuscated_id . '">Delete</a>';
                } 
                $btn .= '</div>';
                return $btn;
            })

            ->escapeColumns([])
            ->make(true);
    }

    public function getData($id)
    {
        $decodedId = IdObfuscator::decode($id);
        $claim = SubmitClaim::find($decodedId);
        return $claim;
    }

    public function getSubmitClaimItemData($id)
    {
        $decodedId = IdObfuscator::decode($id);
        $claimItem = SubmitClaimItem::find($decodedId);
        return $claimItem;
    }

    public function submitClaimDestroy($id)
    {
        $decodedId = IdObfuscator::decode($id);
        $claimItem = SubmitClaimItem::find($decodedId);
        $claimItem->data_status = 0;
        $claimItem->save();
        $submitClaim = SubmitClaim::find($claimItem->submit_claim_id);
        $submitClaim->total_amount = $submitClaim->total_amount - $claimItem->amount;
        $submitClaim->save();
        $cacheKey = 'submit_claim_items_' . $claimItem->submit_claim_id;
        Cache::forget($cacheKey);
        return Response::json(['message' => 'Item deleted successfully.'], 200);
    }

    public function show($id)
    {
        $notif = request('notif');

        if ($id) {
            Auth::user()->notifications()
                ->where('id', $notif)
                ->update(['read_at' => now()]);
        }

        $decodedId = IdObfuscator::decode($id);
        $claim = SubmitClaim::with(['staff', 'submitClaimItems'])->find($decodedId);
        return view('submit_claim.detail',compact('claim'))->with('title', 'Submit Claim Detail')->with('breadcrumb', ['Home', 'Staff Task','Submit Claim Detail']);
    }

    public function adjustClaimItem(Request $request)
    {
        // Validate input
        $request->validate([
            'id'     => 'required',
            'amount' => 'required|numeric|min:0',
        ]);

        // Find claim
        $decodedId = IdObfuscator::decode($request->id);
        $claim = SubmitClaimItem::where('id', $decodedId)->first();

        if (!$claim) {
            return response()->json([
                'success' => false,
                'message' => 'Claim not found'
            ], 404);
        }

        // Update amount
        $claim->amount = $request->amount;
        $claim->save();

        return response()->json([
            'success' => true,
            'message' => 'Claim updated successfully.',
            'new_amount' => $claim->amount
        ]);
    }

    public function print($id)
    {
        $notif = request('notif');

        /**if ($id) {
            Auth::user()->notifications()
                ->where('id', $notif)
                ->update(['read_at' => now()]);
        }**/

        $decodedId = IdObfuscator::decode($id);
        $claim = SubmitClaim::with(['staff', 'submitClaimItems'])->find($decodedId);
        $submitClaimItems = SubmitClaimItem::query()
                //->where('data_status', '=', SubmitClaim::STATUS_APPROVED)
                ->where('submit_claim_id', $decodedId)
                ->orderBy('created_at', 'DESC')
                ->get(); 
        $groupedItems = $submitClaimItems->groupBy('currency');
        $pdf = Pdf::loadView('submit_claim.print', compact('claim', 'groupedItems'));

    // 4. Return the PDF
    // You can choose to stream it (show in browser) or download it.

    // Option A: Stream (show in browser)
        return $pdf->stream('expense_claim_' . $claim->serial_number . '.pdf');
    }

    public function getSubmitClaimItemDetails($id)
    {
        try {
            
            $decodedId = IdObfuscator::decode($id);
            $item = SubmitClaimItem::where('id', $decodedId) // Assuming base64_decode for obfuscated_id
                                   ->with(['claimType', 'files']) // Eager load relationships
                                   ->firstOrFail();

            // Prepare data for JSON response
            $data = [
                'id' => $item->id,
                'description' => $item->description, // Accessor handles nl2br and escaping
                'amount_currency' => $item->currency . ' ' . number_format($item->amount, 2),
                'claim_type_name' => $item->claimType->name,
                'data_status_label' => match ($item->data_status) {
                    1 => 'Ongoing',
                    2 => 'Completed',
                    default => 'Unknown Status',
                },
                'data_status' => $item->data_status,
                'start_date' => $item->start_at->format('d M Y'),
                'end_date' => $item->end_at->format('d M Y'),
                'created_at_formatted' => $item->created_at->format('d M Y h:i'),
                'files' => $item->files->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'name' => $file->file_name,
                        'description' => $file->description,
                        'url' => $file->file_path, // Uses the accessor to get public URL
                        'mime_type' => $file->mime_type,
                        'file_size' => $file->file_size
                    ];
                })->toArray(),
            ];

            return Response::json($data);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("SubmitClaimItem not found for ID: {$id}. Error: " . $e->getMessage());
            return Response::json(['message' => 'Item not found.'], 404);
        } catch (\Exception $e) {
            Log::error("Error fetching SubmitClaimItem details for ID: {$id}. Error: " . $e->getMessage());
            return Response::json(['message' => 'An error occurred.'], 500);
        }
    }

    /**
     * Update the data_status of a specific SubmitClaim.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id (obfuscated ID of the SubmitClaim)
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            // Decode the obfuscated ID to get the real ID
            $decodedId = IdObfuscator::decode($id);

            $claim = SubmitClaim::findOrFail($decodedId);

            // Validate the new status if needed (e.g., must be a specific value)
            $request->validate([
                'new_status' => 'required|integer|in:0,1,2,3,4', // Example: only allow 1, 2, or 3
            ]);

            $newStatus = $request->input('new_status');

            // Prevent updating if already in a final state, or if status is not changing
            if ($claim->data_status === $newStatus) {
                return Response::json(['message' => 'Claim status is already ' . $newStatus . '.'], 200);
            }

            // Update the status
            $claim->data_status = $newStatus;
            $claim->save();

            $submitClaimItemsCacheKey = 'submit_claim_items_' . $claim->id;
            Cache::forget($submitClaimItemsCacheKey);
            $users = User::permission('view-all-submit-claim')->get();
                foreach ($users as $user) {
                    try {
                        $user->notify(new UserNotification(
                            '<strong>'.auth()->user()->name.' </strong> has submitted a claim. Please check the claim at All Submitted Claims',
                            'submit-claim',
                            route('v1.submit-claim.detail', ['id' => $claim->obfuscated_id])
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Notification failed: '.$e->getMessage());
                    }
                }
            

            return Response::json(['message' => 'Claim status updated successfully to submitted'], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("SubmitClaim with ID {$id} not found for status update. Error: " . $e->getMessage());
            return Response::json(['message' => 'Claim not found.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return Response::json(['message' => 'Validation error: ' . $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("Error updating SubmitClaim status for ID {$id}. Error: " . $e->getMessage());
            return Response::json(['message' => 'Failed to update claim status. An error occurred.'], 500);
        }
    }

    /**
     * Handle the approval or rejection action for a SubmitClaim.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id (obfuscated ID of the SubmitClaim)
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleApprovalAction(Request $request, $id)
    {
        $decodedId = IdObfuscator::decode($id);

        try {
            DB::beginTransaction(); // Start a database transaction

            $claim = SubmitClaim::findOrFail($decodedId);

            // Validate the request based on action
            $rules = [
                'action' => ['required', Rule::in(['approve', 'reject'])],
                'notes' => ['nullable', 'string', 'max:1000'],
                'transfer_document' => [
                    Rule::requiredIf($request->input('action') === 'approve'), // Required only if approving
                    'nullable',
                    'file',         // Must be a file
                    'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', // Allowed file types
                    'max:5120',     // Max 5MB (5120 KB)
                ],
            ];

            // If claim is already approved/rejected, prevent further action (optional)
            if ($claim->data_status == SubmitClaim::STATUS_APPROVED || $claim->data_status == SubmitClaim::STATUS_REJECTED) {
                 DB::rollBack();
                 return Response::json(['message' => 'Claim has already been processed.'], 400);
            }

            // Only allow action if claim is in 'Pending Approval' status
            if ($claim->data_status != SubmitClaim::STATUS_SUBMIT) {
                DB::rollBack();
                return Response::json(['message' => 'Claim is not in submit status.'], 400);
            }


            $validatedData = $request->validate($rules);

            $action = $validatedData['action'];
            $notes = $validatedData['notes'] ?? null;
            $transferDocumentPath = null;

            if ($action === 'approve') {
                // Handle file upload for approval
                if ($request->hasFile('transfer_document')) {
                    $file = $request->file('transfer_document');
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $transferDocumentPath = $file->storeAs('public/claim_transfers', $fileName); // Store in storage/app/public/claim_transfers
                } else {
                    // This should not happen if requiredIf is working, but as a safeguard
                    DB::rollBack();
                    return Response::json(['message' => 'Transfer document is required for approval.'], 422);
                }
                $newStatus = SubmitClaim::STATUS_APPROVED;
                $statusText = 'approved';
            } else { // action is 'reject'
                if (empty($notes)) {
                    DB::rollBack();
                    return Response::json(['message' => 'Rejection reason is required.'], 422);
                }
                $newStatus = SubmitClaim::STATUS_REJECTED;
                $statusText = 'rejected';
            }

            // Update SubmitClaim status
            $claim->data_status = $newStatus;
            $claim->save();

            // Create SubmitClaimApproval history record
            SubmitClaimApproval::create([
                'submit_claim_id' => $claim->id,
                'approved_by_user_id' => auth()->id(), // Current authenticated user
                'data_status' => $newStatus,
                'notes' => $notes,
                'transfered_at' => date('Y-m-d H:i:s'),
                'transfer_document_path' => $transferDocumentPath,
            ]); 
            
            $user = User::find($claim->staff_id);
                    try {
                        $user->notify(new UserNotification(
                            '<strong>'.auth()->user()->name.' </strong> has completed your claim. Please check your completed claim at Your Submitted Claims – Completed Claims',
                            'submit-claim',
                            route('v1.submit-claim.detail', ['id' => $claim->obfuscated_id])
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Notification failed: '.$e->getMessage());
                    }

            DB::commit(); // Commit the transaction

            // Invalidate caches
            Cache::forget('all_submit_claims_list_cache_key'); // Example: general list
            Cache::forget('submit_claim_items_' . $claim->id); // Invalidate items cache for this claim

            Log::info("SubmitClaim ID: {$claim->id} {$statusText} by User ID: " . auth()->id());
            return Response::json(['message' => "Claim successfully {$statusText}."], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error("SubmitClaim with ID {$id} not found for approval action. Error: " . $e->getMessage());
            return Response::json(['message' => 'Claim not found.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning("Validation error during approval action for Claim ID {$id}: " . json_encode($e->errors()));
            return Response::json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("An error occurred during approval action for Claim ID {$id}. Error: " . $e->getMessage());
            return Response::json(['message' => 'An unexpected error occurred.'], 500);
        }
    }


    /**
     * Handle the approval or rejection action for a SubmitClaim.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id (obfuscated ID of the SubmitClaim)
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleRejectedAction(Request $request, $id)
    {

        try {
            DB::beginTransaction(); // Start a database transaction

            $claim = SubmitClaimItem::findOrFail($id);

            // Validate the request based on action
            $rules = [
                'action' => ['required', Rule::in(['approve', 'reject'])],
                'notes' => ['nullable', 'string', 'max:1000'],
                
            ];

           

            // Only allow action if claim is in 'Pending Approval' status
            if ($claim->data_status != SubmitClaim::STATUS_DRAFT) {
                DB::rollBack();
                return Response::json(['message' => 'Claim Item is not in submit status.'], 400);
            }


            $validatedData = $request->validate($rules);

            $action = $validatedData['action'];
            $notes = $validatedData['notes'] ?? null;
            $transferDocumentPath = null;

            if ($action === 'approve') {
                
                $newStatus = SubmitClaim::STATUS_APPROVED;
                $statusText = 'approved';
            } else { // action is 'reject'
                if (empty($notes)) {
                    DB::rollBack();
                    return Response::json(['message' => 'Rejection reason is required.'], 422);
                }
                $newStatus = SubmitClaim::STATUS_REJECTED;
                $statusText = 'rejected';
            }

            // Update SubmitClaim status
            $claim->data_status = $newStatus;
            $claim->save();

            // Create SubmitClaimApproval history record
            SubmitClaimApproval::create([
                'submit_claim_id' => $claim->submit_claim_id,
                'submit_claim_item_id' => $claim->id,
                'approved_by_user_id' => auth()->id(), // Current authenticated user
                'data_status' => $newStatus,
                'notes' => $notes,
                'transfered_at' => date('Y-m-d H:i:s'),
                'transfer_document_path' => $transferDocumentPath,
            ]); 

            DB::commit(); // Commit the transaction

            // Invalidate caches
            Cache::forget('all_submit_claims_list_cache_key'); // Example: general list
            Cache::forget('submit_claim_items_' . $claim->submit_claim_id); // Invalidate items cache for this claim

            Log::info("SubmitClaim ID: {$claim->id} {$statusText} by User ID: " . auth()->id());
            return Response::json(['message' => "Claim successfully {$statusText}."], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            //Log::error("SubmitClaim with ID {$id} not found for approval action. Error: " . $e->getMessage());
            return Response::json(['message' => 'Claim Item not found.'.$id], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning("Validation error during approval action for Claim ID {$id}: " . json_encode($e->errors()));
            return Response::json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("An error occurred during approval action for Claim ID {$id}. Error: " . $e->getMessage());
            return Response::json(['message' => 'An unexpected error occurred.'], 500);
        }
    }

    public function exchange()
    {
        // You might want to pass an initial date or data here,
        // but for AJAX, the JS will fetch it on load.
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        return view('submit_claim.exchange.list', compact('today'));
    }

    /**
     * Fetch exchange rates based on a specific date.
     * Returns JSON data for AJAX requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRates(Request $request)
    {
        $filterDate = $request->query('date'); // Get the date from the query parameter

        $query = ExchangeRate::query();

        if ($filterDate) {
            // Ensure the date is valid and format it for database query
            try {
                $parsedDate = \Carbon\Carbon::parse($filterDate)->format('Y-m-d');
                $query->whereDate('rate_date', $parsedDate);
            } catch (\Exception $e) {
                // Handle invalid date format
                return response()->json(['success' => false, 'message' => 'Invalid date format provided.'], 400);
            }
        } else {
            // If no date is provided, default to today's rates or latest rates
            // Option 1: Default to today's rates
            $query->whereDate('rate_date', \Carbon\Carbon::now()->format('Y-m-d'));
            // Option 2: Default to the latest rates available (e.g., max rate_date)
            // $latestDate = ExchangeRate::max('rate_date');
            // if ($latestDate) {
            //     $query->whereDate('rate_date', $latestDate);
            // }
        }

        $rates = $query->orderBy('currency')->get();

        // Format the data for the frontend
        $formattedRates = $rates->map(function ($rate) {
            return [
                'id' => $rate->id,
                'rate_date' => \Carbon\Carbon::parse($rate->rate_date)->format('d M Y'), // Format for display
                'currency' => $rate->currency,
                'rate' => number_format($rate->rate, 4), // Format rate to 4 decimal places
                'created_at' => $rate->created_at->format('d M Y H:i'),
            ];
        });

        return response()->json(['success' => true, 'rates' => $formattedRates]);
    }

}