<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-getting-started|create-getting-started|edit-getting-started|delete-getting-started', ['only' => ['index','show']]);
        $this->middleware('permission:create-getting-started', ['only' => ['create','store']]);
        $this->middleware('permission:edit-getting-started', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-getting-started', ['only' => ['destroy']]);
        $this->middleware('permission:view-employee-handbook', ['only' => ['handbook']]);
    }
    public function index()
    {
        $posts = Post::latest()->paginate(20);
        return view('post.list',compact('posts'))->with('title', 'Getting Started')->with('breadcrumb', ['Home', 'Staff Information Hub', 'Getting Started']);
    }

    public function handbook()
    {
        if(auth()->user()->can('create-employee-handbook')){
            $posts = Post::where('post_type', 1)
            ->whereNot('data_status', 3)->latest()->paginate(20);
        } else {
            $posts = Post::where('post_type', 1)
            ->where('data_status', 1)->latest()->paginate(20);
        }

        return view('handbook.list',compact('posts'))->with('title', 'Employee Handbooks')->with('breadcrumb', ['Home', 'Staff Information Hub', 'Getting Started']);
    }

    public function memo()
    {
        if(auth()->user()->can('create-management-memo')){
            $posts = Post::where('post_type', 2)
            ->whereNot('data_status', 3)->latest()->paginate(20);
        } else {
            $posts = Post::where('post_type', 2)
            ->where('data_status', 1)->latest()->paginate(20);
        }

        return view('memo.list',compact('posts'))->with('title', 'Management Memo')->with('breadcrumb', ['Home', 'Staff Information Hub', 'Management Memo']);
    }



    public function create()
    {
        return view('post.form')->with('title', 'Create a New Getting Started')->with('breadcrumb', ['Home', 'Staff Information Hub', 'Getting Started', 'Creat a Getting Started']);
    }

    public function create_handbook()
    {
        return view('handbook.form')->with('title', 'Create a New Employee Handbook')->with('breadcrumb', ['Home', 'Staff Information Hub', 'Employee Hanbooks', 'Create a New Employee Handbook']);
    }

    public function create_memo()
    {
        return view('memo.form')->with('title', 'Create a New Management Memo')->with('breadcrumb', ['Home', 'Staff Information Hub', 'Management Memo', 'Create a New Management Memo']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'required|string',
        ]);

        Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'created_by' => auth()->user()->id
        ]);

        return redirect()->route('v1.getting-started.index')->with('success', 'Post created successfully.');
    }

    public function store_handbook(Request $request)
    {
        $data = $request->validate([
          'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
          'pdf'   => 'nullable|file|mimes:pdf|max:5120', // max 5MB
        ]);
        $data['content'] = $request->input('title');
          $data['post_type'] = 1;
          $data['data_status'] = 1;
          $data['created_by'] = auth()->user()->id;

        if ($request->hasFile('path_file')) {
          $file = $request->file('path_file');
          $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
          //$path = $file->storeAs('public/pdfs', $filename);
          $file->move(public_path('pdfs'), $filename);
          $data['path_file'] = 'pdfs/' . $filename;
        }

        Post::create($data);

        return redirect()->route('v1.employee-handbooks.list')
                         ->with('success','Employee handbook has created!');
    }

    public function store_memo(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'required|string',
        ]);

        Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'post_type'=>2,
            'created_by' => auth()->user()->id
        ]);

        return redirect()->route('v1.management-memo.index')->with('success', 'Management Memo created successfully.');
    }

    public function edit(string $id)
    {
        $post = Post::findOrFail($id);
        return view('post.edit',compact('post'))->with('title', 'Edit Getting Started')->with('breadcrumb', ['Home', 'Staff Information Hub', 'Getting Started', 'Edit a Getting Started']);
    }

    public function edit_handbook($id)
    {
        $handbook = Post::findOrFail($id);
        return view('handbook.edit',compact('handbook'))->with('title', 'Edit Employee Handbook')->with('breadcrumb', ['Home', 'Staff Information Hub', 'Employee Handbooks', 'Edit a Employee Handbook']);
    }

    public function edit_memo(string $id)
    {
        $post = Post::findOrFail($id);
        return view('memo.edit',compact('post'))->with('title', 'Edit Management Memo')->with('breadcrumb', ['Home', 'Staff Information Hub', 'Management Memo', 'Edit a Management Memo']);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:150',
            'content' => 'required|string',
        ]);

        $post->update($request->all());

        return redirect()->route('v1.getting-started')->with('success', 'Post updated successfully.');
    }

    public function update_handbook(Request $request,$id)
    {
        $post = Post::findOrFail($id);
        $data = $request->validate([
            'title' => 'required|string|max:255',
              'description' => 'nullable|string|max:500',
            'pdf'   => 'nullable|file|mimes:pdf|max:5120', // max 5MB
          ]);

          if ($request->hasFile('path_file')) {
            $file = $request->file('path_file');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            //$path = $file->storeAs('public/pdfs', $filename);
            $file->move(public_path('pdfs'), $filename);
            $data['path_file'] = 'pdfs/' . $filename;
            //$request->merge($data);
          }
          $post->update($data);
        return redirect()->route('v1.employee-handbooks.list')->with('success', 'Handbook updated successfully.');
    }

    public function update_memo(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:150',
            'content' => 'required|string',
        ]);

        $post->update($request->all());

        return redirect()->route('v1.management-memo.list')->with('success', 'Management memo updated successfully.');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->data_status = 0;
        $post->save();

        return redirect()->route('v1.getting-started')->with('success', 'Post updated successfully.');
    }

    public function destroy_handbook($id,$status)
    {
        $post = Post::findOrFail($id);
        $post->data_status = $status;
        $post->save();

        return redirect()->route('v1.employee-handbooks.list')->with('success', 'Handbook updated successfully.');
    }

    public function destroy_memo($id,$status)
    {
        $post = Post::findOrFail($id);
        $post->data_status = $status;
        $post->save();

        return redirect()->route('v1.management-memo.list')->with('success', 'Management memo updated successfully.');
    }

    public function read($id)
    {
        $post = Post::findOrFail($id);
        return view('post.show',compact('post'))->with('title', $post->title)->with('breadcrumb', ['Home', 'Staff Information Hub', 'Getting Started', 'Read Getting Started']);
    }



    public function getPosts(Request $request)
    {
        if ($request->ajax()) {
            $data = Post::select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('v1.roles.edit',['id'=>$row->id]).'" class="edit btn btn-success btn-sm">Edit</a>';
                    $btn .= ' <a href="'.route('v1.roles.show',['id'=>$row->id]).'" class="edit btn btn-info btn-sm">Permission</a>';
                    return $btn;
                })
                ->addColumn('card', function ($post) {
                    return '
                        <div class="card mb-3" style="max-width: 540px;">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-4">
                            <img src="https://via.placeholder.com/150" class="img-fluid rounded-start" alt="...">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title">' . e($post->title) . '</h5>
                                <p class="card-text">' . e(Str::limit($post->content, 100)) . '</p>
                                <p class="card-text"><small class="text-muted">' . $post->created_at->diffForHumans() . '</small></p>
                            </div>
                        </div>
                    </div>
                </div>
                    ';
                })
                ->rawColumns(['action','card'])
                ->make(true);
        }
    }
}
