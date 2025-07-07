<?php
namespace App\Services;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectStage;
use App\Models\ProjectFile;
use App\Models\ProjectMember;
use Auth;
use App\Helpers\IdObfuscator;
use App\Models\ProjectStageLog;
use App\Models\ProjectStageTask;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProjectService {

    public function getProject($id)
    {
        $decodedId = IdObfuscator::decode($id);
        $project = Project::with(['projectMembers', 'projectManager'])->find($decodedId);
        $projectStage = $project->projectStages() // Access the relationship as a Query Builder
                                    ->where('data_status', '!=', 0) // 'whereNot' for DB is 'whereColumn', or simple '!='
                                    ->first();

        $tasks = $projectStage ? $projectStage->tasks->where('data_status', '!=', 0) : collect();
        foreach ($project->projectStages as $stage) {
            foreach ($stage->tasks->where('data_status', '!=', 0) as $task) {
                if ($task->end_at->isPast() && $task->data_status == 1) {
                    $task->data_status = 3;
                    $task->save();
                }
            }
        }
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
                foreach ($request->file('project_files') as $index => $file) {
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
                        'description' => $request->input('project_file_descriptions')[$index] ?? null,
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

    public function getAllProjectsData(Request $request)
    {
        $projectsQuery = Project::whereNot('data_status',0)->with(['projectManager', 'showProjectMembers.member']); // Assuming projectMembers is the correct relationship name

        // Apply conditional filtering based on $request->type
        if ($request->type === 'my') {
            // Filter projects where the authenticated user is the project manager
            $projectsQuery->where('data_status',2);
        } elseif ($request->type === 'others') {
           $projectsQuery->where('data_status', 1);
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
                $btn .= '<a class="btn btn-info btn-sm" href="' . route('v1.project-management.management-detail', ['project' => $row->obfuscated_id]) . '">View</a>';

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

        if ($request->hasFile('project_files')) {
                foreach ($request->file('project_files') as $index => $file) {
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
                        'description' => $request->input('project_file_descriptions')[$index] ?? null,
                        'uploaded_by_user_id' => Auth::id(),
                    ]);
                }
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
            $countProjectStageTasks = ProjectStageTask::where('project_stage_id', $decodedProjectStageId)
            ->whereIn('data_status', [1, 3])
            ->count();
            // 1. Authorization Check:
            // Only the project manager should be able to mark stages complete.
            if ($project->project_manager_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Only the project manager can complete stages.'
                ], 403); // Forbidden
            }

            if ($countProjectStageTasks > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'there is still task(s) that is not complete, could you complete first'
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

    /**
     * Remove the specified project file from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $project_obfuscated_id // The project ID from the route
     * @param  string  $projectFile_obfuscated_id // The ProjectFile ID from the route
     * @return \Illuminate\Http\JsonResponse
     */
    public function fileDestroy(string $projectFileId)
    {
        $projectFile = ProjectFile::findOrFail($projectFileId); // Find the file

        \DB::beginTransaction();
        try {
            // Delete the file from storage
            if (Storage::disk('public')->exists($projectFile->file_path)) {
                Storage::disk('public')->delete($projectFile->file_path);
            }

            // Delete the file record from the database
            $projectFile->delete();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully!'
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error deleting project file {$projectFile->id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the file. Please try again.'
            ], 500);
        }
    }

    /**
     * Get bulletins for a specific project stage via AJAX.
     * Returns JSON data.
     */
    public function getStageBulletinsData($projectStageId,$projectId)
    {
        // Eager load the 'createdBy' relationship to get user names
        $bulletins = ProjectStageLog::where(['stage_id'=>$projectStageId,'project_id'=>$projectId])->orderBy('created_at','DESC')->get();

        return response()->json([
            'bulletins' => $bulletins->map(function ($bulletin) {
                return [
                    'id' => $bulletin->id,
                    'description' => $bulletin->description,
                    'created_at' => $bulletin->created_at, // Will be converted to ISO 8601 string
                    'updated_at' => $bulletin->updated_at,
                    'project_stage_id' => $bulletin->project_stage_id,
                    'created_by' => $bulletin->created_by,
                    'created_by_user' => $bulletin->createdBy ? ['name' => $bulletin->createdBy->name] : null,
                ];
            })
        ]);
    }

    /**
     * Store a new bulletin via AJAX.
     */
    public function storeBulletin(Request $request, $projectStageId,$projectId)
    {
        // Add authorization check if needed
        // $this->authorize('create project stage bulletins');
        $projectStageLog = ProjectStageLog::where(['stage_id'=>$projectStageId,'project_id'=>$projectId])->get();

        $request->validate([
            'description' => 'required|string|max:1000',
        ]);

        // Create a NEW ProjectStageLog record
        $bulletin = ProjectStageLog::create([
            'project_id' => $projectId,       // Assign the project ID
            'stage_id' => $projectStageId,   // Assign the stage ID
            'description' => $request->input('description'),
            'created_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'Bulletin added successfully!']);
    }

    public function monthlyStatusChartData()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Count ongoing projects for the current month:
        // Projects where status is 1 AND their project period overlaps the current month
        $ongoingProjectsCount = Project::where('data_status', 1) // Assuming 1 is 'ongoing'
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->where('start_at', '<=', $endOfMonth)
                      ->where('end_at', '>=', $startOfMonth);
            })
            ->count();

        // Count completed projects for the current month:
        // Projects where status is 3 AND their 'end_at' date is within the current month
        $completedProjectsCount = Project::where('data_status', 3) // Assuming 3 is 'completed'
            ->whereBetween('end_at', [$startOfMonth, $endOfMonth])
            ->count();

        return response()->json([
            'labels' => ['Ongoing', 'Completed'],
            'data' => [$ongoingProjectsCount, $completedProjectsCount],
            'title' => 'Project Status for ' . Carbon::now()->format('F Y')
        ]);
    }

    /**
     * API endpoint for DataTables to fetch project files.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProjectFileData(Request $request)
    {
        // Get filters from DataTables AJAX request (or custom parameters)
        $projectId = $request->input('project_id');
        
        $query = ProjectFile::query()->with(['uploadedBy']);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by project_id (mandatory for project files)
        if ($projectId) {
            $query->where('project_id', $projectId)->select('project_files.*');
        }

        if ($request->filled('uploaded_by_user_id')) {
            $query->where('uploaded_by_user_id', $request->uploaded_by_user_id)->select('project_files.*');
        }
        if ($request->filled('section')) {
            if($request->section=='task'){
                    $query->whereNotNull('project_stage_task_id');
            } else {
                $query->whereNull('project_stage_task_id');
            }            
        }

        return DataTables::eloquent($query)
            ->addColumn('file_name_link', function (ProjectFile $file) {
                // Generate a clickable link to download/view the file
                $url = Storage::url($file->file_path); // Using the accessor you defined in the model
                $icon = '';
                // Add icons based on mime type
                if (\Str::contains($file->mime_type, ['pdf'])) {
                    $icon = '<i class="fas fa-file-pdf text-danger me-1"></i>';
                } elseif (\Str::contains($file->mime_type, ['word', 'document'])) {
                    $icon = '<i class="fas fa-file-word text-primary me-1"></i>';
                } elseif (\Str::contains($file->mime_type, ['excel', 'spreadsheet'])) {
                    $icon = '<i class="fas fa-file-excel text-success me-1"></i>';
                } elseif (\Str::contains($file->mime_type, ['image'])) {
                    $icon = '<i class="fas fa-file-image text-info me-1"></i>';
                } else {
                    $icon = '<i class="fas fa-file text-muted me-1"></i>';
                }

                return $file->description.'<p><small><a href="'.$url.'" target="_blank" download class="text-decoration-none">' . $icon . e($file->short_file_name) . '</a> ('.number_format($file->file_size / 1024, 2).' KB)</small></p>';
            })
            ->addColumn('uploaded_by', function ($query) {
                return $query->uploadedBy->name.'<p><small>'.$query->created_at->format('l, F d, Y \a\t h:i A').'</small></p>' ?? 'N/A';
            })
            ->addColumn('associated_task', function (ProjectFile $file) {
                return !is_null($file->project_stage_task_id) ? '<span class="badge badge-sm bg-primary"><small>'.$file->task->projectStage->kanbanStage->name.'</small></span>':'<span class="badge bg-success"><small>Project</small></span>'; // Display task name or 'Project-level'
                
            })
            ->addColumn('file_size_formatted', function (ProjectFile $file) {
                // Convert bytes to KB, MB, etc.
                $bytes = $file->file_size;
                $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                $i = 0;
                while ($bytes >= 1024 && $i < count($units) - 1) {
                    $bytes /= 1024;
                    $i++;
                }
                return round($bytes, 2) . ' ' . $units[$i];
            })
            ->addColumn('action', function (ProjectFile $file) {
                $buttons = '<a href="'. Storage::url($file->file_path) .'" target="_blank" download class="btn btn-sm btn-outline-info me-1" title="Download"><i class="fas fa-download"></i></a>';
                if($file->project->project_manager_id == auth()->user()->id || $file->uploaded_by_user_id == auth()->user()->id){
                    $buttons .= '<button type="button" class="btn btn-sm btn-outline-danger delete-project-file-btn" 
                    data-file-id="'.$file->id.'" data-file-name="'.e($file->file_name).'" title="Delete"><i class="fas fa-trash"></i></button>';
                }
                
                return $buttons;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getFileCreator($project_id)
    {

        $decodedId = IdObfuscator::decode($project_id);
        $project = Project::find($decodedId);
        $projectManagerId = $project->project_manager_id;

        // 2. Get the IDs of Project Members for the current project
        $memberIds = ProjectMember::where('project_id', $project->id)
                                  ->pluck('member_id')
                                  ->toArray();

        // 3. Combine all unique user IDs
        $allRelatedUserIds = array_unique(array_merge($memberIds, [$projectManagerId]));

        // 4. Fetch the User models for these IDs
        // Select 'id' and 'name' as that's what's needed for the dropdown
        $uploaders = User::whereIn('id', $allRelatedUserIds)
                         ->orderBy('name')
                         ->get(['id', 'name']);
        return $uploaders;
    }
    
}