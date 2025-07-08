<?php
namespace App\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SubmitClaim;
use App\Models\SubmitClaimItem;
use App\Models\ClaimType;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SubmitClaimService {
    /**
     * Store a newly created claim in storage.
     */
    public function store(Request $request)
    {
        // 1. Validate the incoming request data
        $request->validate([
            'claim_serial_number' => 'required|string|max:255|unique:claims,serial_number',
            'claim_date' => 'required|date',
            'staff_id' => 'required|exists:users,id', // Ensure staff_id exists in users table
            'items' => 'required|array|min:1', // Ensure at least one claim item
            'items.*.description' => 'required|string|max:255',
            'items.*.amount' => 'required|numeric|min:0.01',
            'items.*.category' => 'required|string|max:255',
        ]);

        // Use a database transaction to ensure atomicity
        // If any part fails, all changes are rolled back.
        try {
            DB::beginTransaction();

            // 2. Create the main Claim record
            $claim = SubmitClaim::create([
                'serial_number' => $this->generateSerialNumber(),
                'claim_date' => $request->claim_date,
                'staff_id' => $request->staff_id,
                'status' => 'pending', // Default status
                'total_amount' => 0, // Will be updated after saving items
            ]);

            $totalAmount = 0;

            // 3. Create Claim Items and associate them with the main claim
            foreach ($request->items as $itemData) {
                SubmitClaimItem::create([
                    'claim_id' => $claim->id,
                    'description' => $itemData['description'],
                    'amount' => $itemData['amount'],
                    'category' => $itemData['category'],
                ]);
                $totalAmount += $itemData['amount'];
            }

            // 4. Update the total_amount for the main claim
            $claim->update(['total_amount' => $totalAmount]);

            DB::commit();

            // 5. Return a success response
            return response()->json(['message' => 'Claim submitted successfully!', 'claim_id' => $claim->id], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            \Log::error('Claim submission failed: ' . $e->getMessage(), ['exception' => $e]);

            // Return an error response
            return response()->json(['message' => 'Failed to submit claim. Please try again.', 'error' => $e->getMessage()], 500);
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

            ->addColumn('claim_status', function ($claim) {
                return match ($claim->data_status) {
                    1 => '<span class="badge bg-warning">Ongoing</span>',
                    2 => '<span class="badge bg-success">Completed</span>',
                    default => '<span class="badge bg-secondary">Unknown Status</span>',
                };
            })

            ->addColumn('action', function ($claim) {
                return '<div class="btn-group btn-group-vertical" role="group" aria-label="Claim Actions">'
                    . '<a class="btn btn-info btn-sm" href="' . route('claims.detail', ['claim' => $claim->obfuscated_id]) . '">View</a>'
                    . '</div>';
            })

            ->escapeColumns(['staff', 'claim_status', 'action']) // These contain raw HTML
            ->make(true);
    }

    

}