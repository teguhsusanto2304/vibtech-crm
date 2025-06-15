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
}