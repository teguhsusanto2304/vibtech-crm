<?php
namespace App\Services;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProjectStageTask;
use App\Models\ProjectStage;
use App\Models\KanbanStage;
use Auth;
use DB;
use App\Helpers\IdObfuscator;
use App\Models\ProjectFile;

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
        //dd($request->hasFile('task_file'));
        $project_id =  IdObfuscator::decode($project_id);
        // 2. Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'assigned_to_user_id' => 'nullable|integer|exists:users,id',
            'project_files' => 'nullable', // Max 3 files for a task
            'project_files.*' => 'file|mimes:pdf,doc,docx|max:5120',
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
            $task->data_status = 1; // Default status for a new task
            $task->save();

            if ($request->hasFile('task_file')) {
                $file = $request->file('task_file'); // Get the single UploadedFile object directly

                    $originalFileName = $file->getClientOriginalName();
                    $fileMimeType = $file->getClientMimeType();
                    $fileSize = $file->getSize(); // Size in bytes

                    // Store file in storage/app/public/tasks/{task_id}/files
                    $path = $file->store('tasks/' . $task->id . '/files', 'public');

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
        $task = ProjectStageTask::find($task_id);

        try {
            // Eager load necessary relationships for the task details
            $task->load([
                'projectStage.kanbanStage', // To show stage name
                'assignedTo',               // To show assignee details
                'createdBy',                // To show creator details
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
                'message' => 'An error occurred while fetching task details.'
            ], 500);
        }
    }
}