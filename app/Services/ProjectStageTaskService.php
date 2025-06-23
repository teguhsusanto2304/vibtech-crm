<?php
namespace App\Services;
use Illuminate\Http\Request;
use App\Models\ProjectStageTask;
use App\Models\ProjectStage;
use Auth;
use DB;
use App\Helpers\IdObfuscator;
use App\Models\ProjectFile;
use Illuminate\Support\Facades\Storage;

class ProjectStageTaskService {
    /**
     * Store a newly created project stage task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project (Resolved via obfuscated ID)
     * @param  \App\Models\KanbanStage  $kanbanStage (Resolved via direct ID)
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $project_id, $stage_id)
    {
        $project_id =  IdObfuscator::decode($project_id);
        // 2. Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'assigned_to_user_id' => 'nullable|integer|exists:users,id',
            'update_log' => 'nullable|string',
            'project_files' => 'nullable|array|max:3', // Max 5 new files
            'project_files.*' => 'file|mimes:pdf,doc,docx|max:10240',
            // 'status' => 'required|string|in:pending,in_progress,completed,blocked', // If status is user-selectable
        ]);

        DB::beginTransaction();
        try {
            // 3. Find or Create the ProjectStage record for this Project and KanbanStage
            // A project might not have an entry in 'project_stages' for a given 'kanban_stage_id' yet.
            // We need to ensure that specific project-stage instance exists before adding tasks to it.
            $projectStage = ProjectStage::firstOrCreate(
                [
                    'project_id' => $project_id,
                    'kanban_stage_id' => $stage_id,
                ],
                [
                    'data_status' => 1, // Default status for a newly created ProjectStage
                    'notes' => 'Auto-created by first task.',
                ]
            );

            // 4. Create the ProjectStageTask
            $task = new ProjectStageTask();
            $task->project_stage_id = $projectStage->id;
            $task->name = $validatedData['name'];
            $task->description = $validatedData['description'];
            $task->start_at = $validatedData['start_date'];
            $task->end_at = $validatedData['end_date'];
            $task->assigned_to_user_id = $validatedData['assigned_to_user_id'];
            $task->update_log = $validatedData['update_log'];
            $task->data_status = 1; // Default status for a new task
            $task->save();

            // 4. Handle File Uploads
            if ($request->hasFile('project_files')) {
                foreach ($request->file('project_files') as $file) {
                    $originalFileName = $file->getClientOriginalName();
                    $fileMimeType = $file->getClientMimeType();
                    $fileSize = $file->getSize(); // Size in bytes

                    // Store file in storage/app/public/projects/{project_id}/files
                    $path = $file->store('projects/' . $task->id . '/files', 'public');

                    // Save file details to project_files table
                    $taskFile = new ProjectFile();
                    // Save file details to project_files table, linking to the task
                    $taskFile->project_id = $project_id;
                    $taskFile->project_stage_task_id = $task->id;
                    $taskFile->file_name = $originalFileName;
                    $taskFile->file_path = $path;
                    $taskFile->mime_type = $fileMimeType;
                    $taskFile->file_size = $fileSize;
                    $taskFile->uploaded_by_user_id = Auth::id();
                        // 'project_id' is nullable, so we don't set it here if files belong to tasks
                    $taskFile->save();
                }
            }

            DB::commit();

            // 5. Return success response
            return response()->json([
                'success' => true,
                'message' => 'Task created successfully!',
                'task' => $task->load('assignedTo'), // Eager load assignee for frontend if needed
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error creating task for project {$project_id}, stage {$stage_id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the task. Please try again.'.$e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    /**
     * Display the specified project stage task.
     *
     * @param  \App\Models\Project  $project (resolved via obfuscated ID)
     * @param  \App\Models\ProjectStageTask  $task (resolved via obfuscated ID)
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($task_id)
    {
        $task_id =  IdObfuscator::decode($task_id);
        $task = ProjectStageTask::find($task_id);

        try {
            // Eager load necessary relationships for the task details
            $task->load([
                'projectStage.kanbanStage', // To show stage name
                'assignedTo',               // To show assignee details               // To show creator details
                'files'                     // To show associated files
            ]);

            return response()->json([
                'success' => true,
                'task' => $task,
            ]);

        } catch (\Exception $e) {
            \Log::error("Error fetching task details for task {$task->id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching task details.'. $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the status and add a log entry for a specific task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project (resolved via obfuscated ID)
     * @param  \App\Models\ProjectStageTask  $task (resolved via obfuscated ID)
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $task_id)
    {
        $task_id =  IdObfuscator::decode($task_id);
        $task = ProjectStageTask::find($task_id);
        // 2. Validate Request Data
        $validatedData = $request->validate([
            'status' => 'required',
        ]);

        

        DB::beginTransaction();
        try {
            // Update task status
            $task->data_status = $validatedData['status'];           

            // If status is 'completed', set completed_at timestamp
            if ($task->data_status === 4 && is_null($task->completed_at)) {
                $task->completed_at = now();
            } 

            $task->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task status updated successfully!',
                'new_status' => $task->status, // Send back updated log for possible UI refresh
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating task status for task {$task->id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating task status. Please try again.'
            ], 500);
        }
    }

    /**
     * Add a new log entry to a specific task.
     * Assumes 'update_log' column in project_stage_tasks is JSON type and cast to array in model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project (resolved via obfuscated ID)
     * @param  \App\Models\ProjectStageTask  $task (resolved via obfuscated ID)
     * @return \Illuminate\Http\JsonResponse
     */
    public function addLog(Request $request, $task_id)
    {
        $task_id =  IdObfuscator::decode($task_id);
        $task = ProjectStageTask::find($task_id);

        // 2. Validate Request Data
        $validatedData = $request->validate([
            'new_log_entry' => 'required|string|max:1000', // Validate the new log entry text
        ]);

        DB::beginTransaction();
        try {


            $task->update_log =$validatedData['new_log_entry']; // Assign the updated array back
            $task->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Log entry added successfully!',
                'new_log_entry_added' => $validatedData['new_log_entry'],
                'updated_logs' => $task->update_log, // Send back full updated logs
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error adding log to task {$task->id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the log entry. Please try again.'
            ], 500);
        }
    }

    /**
     * Update the specified task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $project_obfuscated_id
     * @param  string  $task_obfuscated_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $task_obfuscated_id)
    {
        $taskId = IdObfuscator::decode($task_obfuscated_id);
        $task = ProjectStageTask::findOrFail($taskId);


        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        DB::beginTransaction();
        try {
            // Update basic task fields
            $task->name = $validatedData['name'];
            $task->description = $validatedData['description'] ?? null;
            $task->end_at = $validatedData['end_date'] ?? null;
            $task->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully!',
                'task' => $task->load(['assignedTo', 'files']) // Reload relationships for fresh data
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error updating task {$taskId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the task: ' . $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    /**
     * Remove the specified task from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $project_obfuscated_id
     * @param  string  $task_obfuscated_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, string $task_obfuscated_id)
    {
        // Decode IDs (if using obfuscated IDs)
        $taskId = IdObfuscator::decode($task_obfuscated_id);
        $task = ProjectStageTask::findOrFail($taskId);

        DB::beginTransaction();
        try {
            // Delete associated files from storage and database first
            $task->data_status = 0;
            // Now delete the task itself
            $task->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error deleting task {$taskId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the task. Please try again.'
            ], 500);
        }
    }
}