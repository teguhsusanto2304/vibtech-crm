<?php
namespace App\Services;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectStage;
use Auth;
use Yajra\DataTables\DataTables;
use App\Helpers\IdObfuscator;
use Carbon\Carbon;

class ProjectService {

    public function getProject($id)
    {
        $decodedId = IdObfuscator::decode($id);
        $project = Project::with(['projectMembers', 'projectManager'])->find($decodedId);
        return $project;
    }
    public function store(Request $request)
    {
        // 1. Validate the incoming request data
        // Adjust validation rules as per your form's requirements
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string', // Description can be optional
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at', // End date must be on or after start date
            'addProjectMembers' => 'required|array', // Expecting an array of user IDs
            'addProjectMembers.*' => 'integer|exists:users,id', // Each member ID must be an integer and exist in users table
            'project_files' => 'nullable|array|max:5', // Max 5 new files
            'project_files.*' => 'file|mimes:pdf,doc,docx|max:10240',
        ]);

        try {
            // 2. Create the new Project record
            $project = Project::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'start_at' => $validatedData['start_at'],
                'end_at' => $validatedData['end_at'],
                'project_manager_id' => Auth::id(), // Set the authenticated user as the project manager
            ]);

            // 3. Attach Project Members (many-to-many relationship)
            if (!empty($validatedData['addProjectMembers'])) {
                // The sync method will attach any number of models to the
                // given model. It accepts an array of IDs to place on the
                // intermediate table. Any IDs not in the given array will
                // be removed from the intermediate table.
                $project->projectMembers()->sync($validatedData['addProjectMembers']);
            } else {
                // If no members are selected, ensure no previous members are attached
                $project->projectMembers()->detach(); // Or sync([])
            }

            // 4. Handle File Uploads
            if ($request->hasFile('project_files')) {
                foreach ($request->file('project_files') as $file) {
                    $originalFileName = $file->getClientOriginalName();
                    $fileMimeType = $file->getClientMimeType();
                    $fileSize = $file->getSize(); // Size in bytes

                    // Store file in storage/app/public/projects/{project_id}/files
                    $path = $file->store('projects/' . $project->id . '/files', 'public');

                    // Save file details to project_files table
                    $project->files()->create([
                        'file_name' => $originalFileName,
                        'file_path' => $path, // The path returned by store()
                        'mime_type' => $fileMimeType,
                        'file_size' => $fileSize,
                        'uploaded_by_user_id' => Auth::id(),
                    ]);
                }
            }


            // 4. Return a success response
            return redirect()->route('v1.project-management.detail', ['project' => $project->obfuscated_id])->with('success', 'Project has been stored successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // This block is actually rarely hit because $request->validate()
            // automatically handles validation exceptions by returning a 422 JSON response.
            // However, it's good practice for other custom validation or if you want to capture it.
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // Catch any other unexpected errors
            \Log::error('Error creating project: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create project. Please try again later.'.$e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }

    public function getProjectsData(Request $request)
    {
        $userId = Auth::id(); // Get the ID of the currently authenticated user

        //$projects = Project::with(['projectManager', 'showProjectMembers.member']) // Eager load necessary relationships
            //->where(function ($query) use ($userId) {
            //    $query->where('project_manager_id', $userId);

           //     $query->orWhereHas('showProjectMembers', function ($subQuery) use ($userId) {
           //         $subQuery->where('member_id', $userId); // <--- Use the column name on the pivot table/model
           //     });
           // })
            //->orderBy('created_at', 'ASC')
            //->get();
        $projectsQuery = Project::whereNot('data_status',0)->with(['projectManager', 'showProjectMembers.member']); // Assuming projectMembers is the correct relationship name

        // Apply conditional filtering based on $request->type
        if ($request->type === 'my') {
            // Filter projects where the authenticated user is the project manager
            $projectsQuery->where('project_manager_id', $userId);
        } elseif ($request->type === 'others') {
            // Filter projects where the authenticated user is NOT the project manager
            //$projectsQuery->where('project_manager_id', '!=', $userId);
            $projectsQuery->whereHas('showProjectMembers', function ($subQuery) use ($userId) {
                    $subQuery->where('member_id', $userId); // <--- Use the column name on the pivot table/model
            });
        }
        $projects = $projectsQuery->orderBy('created_at', 'ASC')->get();


        return DataTables::of($projects)
            ->addIndexColumn()
            ->addColumn('project_manager', function ($project) {
                 return '<img src="'.$project->projectManager->avatar_url.'" alt="Project Manager Avatar" 
                        class="rounded-circle me-2" data-bs-toggle="tooltip"
                 data-bs-placement="top"
                 width="40" height="40">&nbsp;'.$project->projectManager->name;
            })
            ->addColumn('project_members', function ($project) {
                $return = null;
                foreach($project->showProjectMembers as $row)
                {
                    $return .= '<span class="badge bg-primary">'.$row->member->name.'</span>&nbsp;';
                }
                return $return;
            })
            ->addColumn('total_project_members', fn($project) => 
                $project->showProjectMembers()->count().' Person(s)' 
            )
            ->addColumn('progress_percentage',fn($project) => $project->work_progress_percentage )
           ->addColumn('action', function ($row) {
    // Start with the vertical button group container
    $btn = '<div class="btn-group btn-group-vertical" role="group" aria-label="Project Actions">';

    // View button
    $btn .= '<a class="btn btn-info btn-sm" href="' . route('v1.project-management.detail', ['project' => $row->obfuscated_id]) . '">View</a>';

    // Conditional Edit and Delete buttons (only for project manager)
    if ($row->project_manager_id == auth()->user()->id && $row->data_status==1) {
        // Edit button
        $btn .= '<a class="btn btn-primary btn-sm" href="' . route('v1.project-management.edit', ['id' => $row->obfuscated_id]) . '">Edit</a>';

        // Delete button (using a form for proper DELETE request)
        //$btn .= '<form action="' . route('v1.project-management.destroy', ['project' => $row->obfuscated_id]) . '" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this project?\');">';
        //$btn .= csrf_field(); // Laravel CSRF token
        //$btn .= method_field('DELETE'); // Spoof DELETE method for Laravel
        //$btn .= '<button type="submit" class="btn btn-danger btn-sm">Delete</button>'; // Use btn-danger for styling
        //$btn .= '</form>';
        $btn .= '<button type="button" class="btn btn-danger btn-sm delete-project-btn"';
        $btn .= ' data-bs-toggle="modal"';
        $btn .= ' data-bs-target="#confirmationModal"'; // Target our custom modal
        $btn .= ' data-project-id="' . $row->obfuscated_id . '"'; // Pass the project ID
        $btn .= ' data-confirm-message="Are you sure you want to delete the project \'' . htmlspecialchars($row->name) . '\'? This action cannot be undone."'; // Custom message
        $btn .= '>Delete</button>';
    }

    // Close the button group container
    $btn .= '</div>';

    return $btn;
})
            
            
            ->addColumn('start_at', function ($project) {
                return $project->start_at->format('d M Y') ;
            })
            ->addColumn('end_at', function ($project) {
                return $project->end_at->format('d M Y');

            })
            ->addColumn('project_status', function ($project) {
                return $project->data_status==1 ? '<span class="badge bg-warning">Ongoing</span>':'<span class="badge bg-success">Completed</span>';

            })
            ->escapeColumns([])
            ->make(true);
    }

    public function update(Request $request, $id)
    {
        // Add your validation rules
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'addProjectMembers' => 'required|array',
            'addProjectMembers.*' => 'integer|exists:users,id',
        ]);

        $decodedId = IdObfuscator::decode($id);
        $project = Project::with(['projectMembers', 'projectManager'])->find($decodedId);
        // Update project details
        $project->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'start_at' => $validatedData['start_at'], // Assuming your DB columns are 'start_date'
            'end_at' => $validatedData['end_at'],     // and 'end_date'
        ]);

        // Sync project members (this works if projectMembers is belongsToMany)
        if (isset($validatedData['addProjectMembers'])) {
            $project->projectMembers()->sync($validatedData['addProjectMembers']);
        } else {
            $project->projectMembers()->detach(); // If no members selected, detach all
        }

        return redirect()->route('v1.project-management.detail', ['project' => $project->obfuscated_id])
                         ->with('success', 'Project updated successfully!');
    }

    public function destroy($id)
    {       

        $decodedId = IdObfuscator::decode($id);
        $project = Project::find($decodedId);
        // Update project details
        $project->update([
            'data_status' => 0
        ]);

        return redirect()->route('v1.project-management.list')
                         ->with('success', 'Project deleted successfully!');
    }

    public function addMember(Request $request, $id)
    {

        $decodedId = IdObfuscator::decode($id);
        $project = Project::with(['projectMembers'])->find($decodedId);
        // 1. Authorization Check:
        // Only the project manager should be able to add members.
        if ($project->project_manager_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Only the project manager can add members.'
            ], 403); // Forbidden
        }

        // 2. Validate the incoming request
        $validatedData = $request->validate([
            'member_id' => 'required|integer|exists:users,id', // Ensure member_id exists in users table
        ]);

        $memberIdToAdd = $validatedData['member_id'];
        $memberToAdd = User::find($memberIdToAdd); // Get the User model instance

        // 3. Prevent adding the Project Manager as a regular member if they already are
        if ($project->project_manager_id == $memberIdToAdd) {
            return response()->json([
                'success' => false,
                'message' => 'The project manager is already implicitly part of the project. No need to add them as a regular member.'
            ], 400); // Bad Request
        }

        // 4. Check if the member is already associated with the project
        if ($project->projectMembers->contains($memberIdToAdd)) {
            return response()->json([
                'success' => false,
                'message' => 'This user is already a member of the project.'
            ], 409); // Conflict
        }

        \DB::beginTransaction();
        try {
            // 5. Attach the new member to the project
            // The attach() method is used for adding a single new association in a many-to-many relationship.
            $project->projectMembers()->attach($memberIdToAdd);

            \DB::commit();

            // Return success response with updated member count or new member data
            return response()->json([
                'success' => true,
                'message' => 'Member added successfully to the project!',
                'new_member' => [
                    'id' => $memberToAdd->id,
                    'name' => $memberToAdd->name,
                    'avatar_url' => $memberToAdd->avatar_url, // Use accessor
                    'initials' => $memberToAdd->initials, // Use accessor
                ],
                'total_members_count' => $project->projectMembers()->count() + 1, // Include the newly added member
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error adding member {$memberIdToAdd} to project {$project->id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the member. Please try again.'
            ], 500); // Internal Server Error
        }
    }

    public function removeMember($project_id, $user_id)
    {
        $decodedId = IdObfuscator::decode($project_id);
        $project = Project::find($decodedId);
        // 1. Authorization Check:
        // Only the project manager should be able to remove members.
        if ($project->project_manager_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Only the project manager can remove members.'
            ], 403); // Forbidden
        }

        // 2. Prevent removing the Project Manager if they are also listed as a member.
        // This is a business rule: if the user being removed is the manager,
        // you might want to prevent it or require a manager change first.
        if ($project->project_manager_id == $user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove the Project Manager from the member list directly. Assign a new manager first if needed.'
            ], 400); // Bad Request
        }

        // 3. Verify that the user is indeed a member of this project
        // (This check is good practice, though detach() itself won't throw an error if not found)
        if (!$project->projectMembers()->where('member_id', $user_id)->exists()) {
             return response()->json([
                'success' => false,
                'message' => 'This user is not an active member of this project.'
            ], 404); // Not Found
        }

        \DB::beginTransaction();
        try {
            // 4. Detach the member from the project
            // Pass the ID of the user to detach.
            $project->projectMembers()->detach($user_id);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Member removed successfully from the project.',
                'total_members_count' => $project->projectMembers()->count(), // Get updated count
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error detaching member {$user_id} from project {$project->id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing the member. Please try again.'
            ], 500); // Internal Server Error
        }
    }

    /**
     * Mark a specific ProjectStage as 'completed'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project (Resolved via obfuscated ID)
     * @param  \App\Models\ProjectStage  $projectStage (Resolved via obfuscated ID)
     * @return \Illuminate\Http\JsonResponse
     */
    public function markComplete(Request $request, $project_id, $project_stage_id)
    {
        $decodedId = IdObfuscator::decode($project_id);
        $project = Project::find($decodedId);
        if($project_stage_id=='-'){
            \DB::beginTransaction();
            try {
                $projectStage = new ProjectStage;
                // 5. Update the ProjectStage status and completed_at timestamp
                $projectStage->data_status = 2;
                $projectStage->completed_at = Carbon::now();
                $projectStage->kanban_stage_id = $request->input('kanban_stage_id');
                $projectStage->project_id = $decodedId;
                $projectStage->notes = 'N/A';
                $projectStage->save();

                \DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Project stage marked as completed successfully!',
                    'project_stage' => $projectStage->load('kanbanStage'), // Return updated stage data
                ]);

            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error("Error completing project stage {$projectStage->id} for project {$project->id}: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while marking the stage complete. Please try again.'
                ], 500); // Internal Server Error
            }

        } else {
            $decodedProjectStageId = IdObfuscator::decode($project_stage_id);
            $projectStage = ProjectStage::find($decodedProjectStageId);
            // 1. Authorization Check:
            // Only the project manager should be able to mark stages complete.
            if ($project->project_manager_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Only the project manager can complete stages.'
                ], 403); // Forbidden
            }

            // 2. Ensure the ProjectStage belongs to the correct Project
            if ($projectStage->project_id !== $project->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'The specified project stage does not belong to this project.'
                ], 404); // Not Found
            }

            // 3. Prevent marking complete if it's already complete
            if ($projectStage->data_status === 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'This project stage is already marked as completed.'
                ], 400); // Bad Request
            }

            // 4. (Optional but Recommended) Check if all tasks within this stage are completed.
            // You might want to enforce this before allowing stage completion.
            //if ($projectStage->tasks()->where('status', '!=', 2)->exists()) {
            //     return response()->json([
            //        'success' => false,
            //        'message' => 'All tasks in this stage must be completed before marking the stage as complete.'
            //    ], 400); // Bad Request
            //}

            \DB::beginTransaction();
            try {
                // 5. Update the ProjectStage status and completed_at timestamp
                $projectStage->data_status = 2;
                $projectStage->completed_at = Carbon::now();
                $projectStage->save();

                \DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Project stage marked as completed successfully!',
                    'project_stage' => $projectStage->load('kanbanStage'), // Return updated stage data
                ]);

            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error("Error completing project stage {$projectStage->id} for project {$project->id}: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while marking the stage complete. Please try again.'
                ], 500); // Internal Server Error
            }

        }
        
    }

    /**
     * Mark an entire project as 'completed'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project (Resolved via obfuscated ID)
     * @return \Illuminate\Http\JsonResponse
     */
    public function markProjectComplete(Request $request, $project_id)
    {
        $decodedId = IdObfuscator::decode($project_id);
        $project = Project::find($decodedId);
        // 1. Authorization Check:
        // Only the project manager should be able to complete the entire project.
        if ($project->project_manager_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Only the project manager can complete this project.'
            ], 403); // Forbidden
        }

        // 2. Business Logic Check:
        // Use the can_complete accessor logic to ensure all stages are complete.
        if ($project->work_progress_percentage < 100) {
            return response()->json([
                'success' => false,
                'message' => 'All project stages must be completed before marking the entire project complete.'
            ], 400); // Bad Request
        }

        // 3. Prevent marking complete if it's already complete (if you have a 'project_status' column)
        // Assuming your Project model has a 'status' or similar column for overall project status
        // if ($project->status === 'completed') { // Adjust column name as needed
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'This project is already marked as completed.'
        //     ], 400);
        // }

        \DB::beginTransaction();
        try {
            // 4. Update the Project's overall status and completion timestamp
            // Assuming you have a 'status' column and 'completed_at' timestamp on your Project model
            $project->data_status = 2; // Or whatever status string you use
            $project->updated_at = Carbon::now();
            $project->save();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Project marked as completed successfully!',
                'project_status' => $project->data_status, // Return new status
            ]);

        } catch (\Exception $e) {
           \DB::rollBack();
            \Log::error("Error completing project {$project->id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while marking the project complete. Please try again.'
            ], 500); // Internal Server Error
        }
    }

    /**
     * Get a list of users assignable to tasks for a specific project.
     * This includes the project manager and all project members.
     *
     * @param  \App\Models\Project  $project (resolved via obfuscated ID)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssignableUsers($id)
    {
        $decodedId = IdObfuscator::decode($id);
        $project = Project::find($decodedId);
        // Eager load project manager and members for efficiency
        $project->load(['projectManager', 'projectMembers']);

        // --- Step 1: Collect all actual User models ---
        $userIdsToInclude = collect();

        // Add the Project Manager (if exists)
        if ($project->projectManager) {
           $userIdsToInclude->push($project->projectManager->id);
        }

        // Add all project members (loop through ProjectMember pivot models
        // and extract the associated User model via ->member)
        foreach ($project->showProjectMembers as $projectMember) {
            if ($projectMember->member) { // Ensure the member relationship loaded successfully
                $userIdsToInclude->push($projectMember->member->id);
            }
        }
        //$userIdsToInclude = $userIdsToInclude->unique()->values();

        

        $assignableUsersQuery = User::whereIn('id', $userIdsToInclude);

        // Execute the query and sort the results
        $assignableUsers = $assignableUsersQuery->orderBy('name')->get();

        // --- Step 3: Map to desired format for frontend (optional but good practice) ---
        $formattedUsers = $assignableUsers->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar_url' => $user->avatar_url, // Make sure User model has this accessor/property
                'initials' => $user->initials,     // Make sure User model has this accessor/property
            ];
        });

        return response()->json([
            'success' => true,
            'users' => $formattedUsers,
        ]);
    }

    
}