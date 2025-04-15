<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Yajra\DataTables\DataTables;
use Str;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-getting-started|create-getting-started|edit-getting-started|delete-getting-started', ['only' => ['index','show']]);
        $this->middleware('permission:create-getting-started', ['only' => ['create','store']]);
        $this->middleware('permission:edit-getting-started', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-getting-started', ['only' => ['destroy']]);
    }
    public function index()
    {
        $posts = Post::latest()->paginate(20);
        return view('post.list',compact('posts'))->with('title', 'Getting Started')->with('breadcrumb', ['Home', 'Staff Information Hub', 'Getting Started']);
    }

    public function create()
    {
        return view('post.form')->with('title', 'Create a New Getting Started')->with('breadcrumb', ['Home', 'Staff Information Hub', 'Getting Started', 'Creat a Getting Started']);
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

    public function edit(string $id)
    {
        $post = Post::findOrFail($id);
        return view('post.edit',compact('post'))->with('title', 'Edit Getting Started')->with('breadcrumb', ['Home', 'Staff Information Hub', 'Getting Started', 'Edit a Getting Started']);
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

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->data_status = 0;
        $post->save();

        return redirect()->route('v1.getting-started')->with('success', 'Post updated successfully.');
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
