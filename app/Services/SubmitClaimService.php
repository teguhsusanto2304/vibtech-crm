<?php
namespace App\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SubmitClaim;
use App\Models\SubmitClaimItem;
use App\Models\ClaimType;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Helpers\IdObfuscator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response; // Import Response facade
use Illuminate\Support\Facades\Log; // For logging errors
use Auth;
use Illuminate\Validation\Rule;

class SubmitClaimService {
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
            'claim_type_id' => [
                'required',
                'exists:claim_types,id',
                // Unique validation: claim_type_id must be unique for this submit_claim_id
                Rule::unique('submit_claim_items')->where(function ($query) use ($submitClaimId, $request) {
                    return $query->where('submit_claim_id', $submitClaimId)
                    ->where('start_at',$request->start_at)
                    ->where('end_at',$request->end_at)
                    ->where('data_status','!=',0);
                }),
            ],
            'start_at' => 'required|date|before_or_equal:today',
            'end_at' => 'required|date|before_or_equal:today|after_or_equal:start_at',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required',
            'project_files' => 'nullable|array|max:5', // Max 5 new files
            'project_files.*' => 'file|mimes:png,jpg,pdf,doc,docx|max:10240',
            'description' => 'required',
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
                    'description' => '-',
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

            // 5. Return a success response
            return redirect()->route('v1.submit-claim.detail',['id'=>$claim->obfuscated_id])->with('success', 'Project has been stored successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            \Log::error('Claim submission failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withErrors(['errors' => 'Failed to submit claim. Please try again.'.$e->getMessage()])->withInput();

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
                $claim->claim_date->format('d M Y h:i')
            )
            ->addColumn('claim_status', function ($claim) {
                return $claim->submit_claim_status;
            })

            ->addColumn('action', function ($claim)  use ($request) {
                $btn = '<div class="btn-group btn-group-vertical" role="group" aria-label="Claim Actions">';
                if($claim->data_status==1){
                    $btn .= '<a class="btn btn-primary btn-sm" href="'.route("v1.submit-claim.create").'?id='.$claim->obfuscated_id.'">Create Item</a>';
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

    public function getSubmitClaimItemsData(Request $request,$submit_claim_id)
    {
        $cacheKey = 'submit_claim_items_' . $submit_claim_id;
        $cacheDuration = 60; // Cache for 60 seconds (1 minute)

        // Use Cache::remember to get data from cache or execute query and store
        $submitClaimItems = Cache::remember($cacheKey, $cacheDuration, function () use ($submit_claim_id) {
            return SubmitClaimItem::query()
                ->where('data_status', '!=', 0)
                ->where('submit_claim_id', $submit_claim_id)
                ->orderBy('created_at', 'DESC')
                ->get(); // Execute the query and get the collection
        });

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
                return match ($claim->data_status) {
                    1 => '<span class="badge bg-warning">Ongoing</span>',
                    2 => '<span class="badge bg-success">Completed</span>',
                    default => '<span class="badge bg-secondary">Unknown Status</span>',
                };
            })

            ->addColumn('action', function ($claim) {
                $btn = '<div class="btn-group btn-group" role="group" aria-label="Claim Actions">';
                $btn .= '<a class="btn btn-info btn-sm view-item-btn" href="#" data-id="' . $claim->obfuscated_id . '">View</a>'; // Changed href to # and added class/data-id
                if($claim->submitClaim->data_status==1){
                    $btn .= '<a class="btn btn-warning btn-sm" href="" disabled>Edit</a>';
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
        $decodedId = IdObfuscator::decode($id);
        $claim = SubmitClaim::with(['staff', 'submitClaimItems'])->find($decodedId);
        return view('submit_claim.detail',compact('claim'))->with('title', 'Submit Claim Detail')->with('breadcrumb', ['Home', 'Staff Task','Submit Claim Detail']);
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
                'start_date' => $item->start_at->format('d M Y'),
                'end_date' => $item->end_at->format('d M Y'),
                'created_at_formatted' => $item->created_at->format('d M Y h:i'),
                'files' => $item->files->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'name' => $file->file_name,
                        'url' => $file->file_url, // Uses the accessor to get public URL
                        'mime_type' => $file->mime_type,
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
                'new_status' => 'required|integer|in:1,2,3,4', // Example: only allow 1, 2, or 3
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

}