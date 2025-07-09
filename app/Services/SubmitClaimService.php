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

class SubmitClaimService {
    /**
     * Store a newly created claim in storage.
     */
    public function store(Request $request)
    {
        // 1. Validate the incoming request data
        $request->validate([
            'claim_serial_number' => 'nullable|string|max:255|unique:submit_claims,serial_number',
            'claim_type_id' => 'required',
            'start_at' => 'required|date', // Ensure at least one claim item
            'end_at' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required',
            'items.*.category' => 'required|string|max:255',
        ]);

        // Use a database transaction to ensure atomicity
        // If any part fails, all changes are rolled back.
        try {
            DB::beginTransaction();

            // 2. Create the main Claim record
            $claim = SubmitClaim::create([
                'serial_number' => $this->generateSerialNumber(),
                'claim_date' => date('Y-m-d'),
                'staff_id' => auth()->user()->id,
                'data_status' => 1, // Default status
                'total_amount' => 0, // Will be updated after saving items
            ]);

            $totalAmount = 0;

            // 3. Create Claim Items and associate them with the main claim
            //foreach ($request->items as $itemData) {
                SubmitClaimItem::create([
                    'submit_claim_id' => $claim->id,
                    'description' => '-',
                    'amount' => $request->amount,
                    'currency' => $request->currency,
                    'claim_type_id' => $request->claim_type_id,
                    'start_at' => $request->start_at,
                    'end_at' => $request->end_at,
                    'data_status' => 1,
                ]);
                //$totalAmount += $itemData['amount'];
            //}

            // 4. Update the total_amount for the main claim
            $claim->update(['total_amount' => $request->amount]);

            DB::commit();

            // 5. Return a success response
            return redirect()->route('v1.submit-claim')->with('success', 'Project has been stored successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            \Log::error('Claim submission failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withErrors(['errors' => 'Failed to submit claim. Please try again.'.$e->getMessage()])->withInput();

            // Return an error response
            //return response()->json(['message' => 'Failed to submit claim. Please try again.', 'error' => $e->getMessage()], 500);
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
                $claim->submitClaimItems->count().' item(s)'
            )

            ->addColumn('total_amount_currency', fn($claim) =>
                $claim->currency . ' ' . number_format($claim->total_amount, 2)
            )
            ->addColumn('claim_date',fn($claim) => 
                $claim->claim_date->format('d M Y h:i')
            )
            ->addColumn('claim_status', function ($claim) {
                return match ($claim->data_status) {
                    1 => '<span class="badge bg-warning">Ongoing</span>',
                    2 => '<span class="badge bg-success">Completed</span>',
                    default => '<span class="badge bg-secondary">Unknown Status</span>',
                };
            })

            ->addColumn('action', function ($claim) {
                return '<div class="btn-group btn-group-vertical" role="group" aria-label="Claim Actions">'
                    . '<a class="btn btn-info btn-sm" href="' . route('v1.submit-claim.detail', ['id' => $claim->obfuscated_id]) . '">View</a>'
                    . '</div>';
            })

            ->escapeColumns([])
            ->make(true);
    }

    public function show($id)
    {
        $decodedId = IdObfuscator::decode($id);
        $claim = SubmitClaim::with(['staff', 'submitClaimItems'])->find($decodedId);
        return view('submit_claim.detail',compact('claim'))->with('title', 'Submit Claim Detail')->with('breadcrumb', ['Home', 'Staff Task','Submit Claim Detail']);
    }

}