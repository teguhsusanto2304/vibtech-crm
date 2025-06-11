<?php
namespace App\Services;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use Auth;
use Yajra\DataTables\DataTables;

class ProjectService {

    public function getProject($id)
    {
        return Project::with(['projectMembers', 'projectManager'])->find($id);
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
            'addProjectMembers' => 'nullable|array', // Expecting an array of user IDs
            'addProjectMembers.*' => 'integer|exists:users,id', // Each member ID must be an integer and exist in users table
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


            // 4. Return a success response
            return redirect()->route('v1.project-management')->with('success', 'Project has been stored successfully.');

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

        $projects = Project::with(['projectManager', 'showProjectMembers.member']) // Eager load necessary relationships
            ->where(function ($query) use ($userId) {
                $query->where('project_manager_id', $userId);

                $query->orWhereHas('showProjectMembers', function ($subQuery) use ($userId) {
                    $subQuery->where('member_id', $userId); // <--- Use the column name on the pivot table/model
                });
            })
            ->orderBy('created_at', 'ASC')
            ->get();

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
            ->addColumn('progress_percentage',fn($project) => rand(0, 100) )
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group btn-group-vertical" role="group" aria-label="Basic mixed styles example">';
                $btn .= '<a class="btn btn-info btn-sm" href="' . route('v1.project-management.detail',['id'=>$row->id]) . '">View</a>';
                $btn .= '</div>';               

                return $btn;
            })
            
            
            ->addColumn('start_at', function ($project) {
                return $project->start_at->format('d M Y') ;
            })
            ->addColumn('end_at', function ($project) {
                return $project->end_at->addDays(60)->format('d M Y');

            })
            ->escapeColumns([])
            ->make(true);
    }
}