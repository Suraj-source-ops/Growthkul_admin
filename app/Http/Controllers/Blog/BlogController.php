<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Documents\DocumentController;
use App\Models\Blog;
use App\Models\Files;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    #call permissions
    public function __construct()
    {
        $this->middleware('permission:blog-lists-blogs|add-blog-button-blogs|edit-blog-button-blogs|update-blog-button-blogs|delete-blog-button-blogs|change-status-button-blogs', ['only' => ['blogLists']]);
        $this->middleware('permission:add-blog-button-blogs', ['only' => ['addBlog','storeBlog']]);
        $this->middleware('permission:edit-blog-button-blogs|update-blog-button-blogs', ['only' => ['editBlogDetails','updateBlogDetails']]);
        $this->middleware('permission:delete-blog-button-blogs', ['only' => ['deleteBlog']]);
        $this->middleware('permission:change-status-button-blogs', ['only' => ['activeOrInactiveBlog']]);
    }

    #blog lists datatable
    public function blogLists(Request $request)
    {
        try {
            if ($request->ajax()) {
                $blogs = Blog::select('*')
                    ->with(['user', 'blogImage'])
                    ->orderBy('id', 'desc');
                return DataTables::of($blogs)
                    ->addIndexColumn()
                    ->editColumn('image', function ($row) {
                        $imageUrl = $row->blogImage ? asset($row->blogImage->file_path) : asset('assets/images/default.png');
                        return '<img src="' . $imageUrl . '" alt="Blog Image" width="50" height="50">';
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('d-m-Y H:i:s') : 'N/A';
                    })
                    ->editColumn('status', function ($row) {
                        return '<label class="switch">
                                <input class="form-check-input check-status-css" type="checkbox" role="switch" id="statusSwitch' . $row->id . '" ' . ($row->status == 1 ? 'checked' : '') . '
                                onchange="activeOrInactiveBlog(' . $row->id . ', ' . ($row->status == 1 ? 0 : 1) . ')">
                                 <span class="slider round"></span>
                            </label>';
                    })
                    ->editColumn('action', function ($row) {
                        if (auth()->user()->can('edit-blog-button-blogs')) {
                            $btn = '<a href="' . route('edit.blog.details', ['id' => $row->id]) . '" class="btn btn-info btn-sm" title="Edit Blog" style="margin-right: 5px;background: #5F4CDD;"><i class="fa fa-edit"></i></a>';
                        }else {
                            $btn = '';
                        }
                        if (auth()->user()->can('delete-blog-button-blogs')) {
                            $btn .= '<a href="' . route('delete.blog', ['id' => $row->id]) . '" class="btn btn-danger btn-sm deleteBlog" title="Delete Blog" style="background: #FF4A4A;"><i class="fa fa-trash"></i></a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['image', 'created_at', 'action', 'status'])
                    ->make(true);
            }
            return view('blogs.blog');
        } catch (Exception $e) {
            Log::channel('exception')->error('blogLists: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch Blog lists', 'alert-type' => 'error']);
        }
    }

    #add blog
    public function addBlog()
    {
        return view('blogs.add');
    }

    #store blog
    public function storeBlog(Request $request)
    {
        $rules = [
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'blog_image' => 'required|image|mimes:jpg,jpeg,png,jpg|max:2048',
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string|max:255',
            'social_media_type' => 'required|string|max:255',
            'social_links' => 'required|string|max:255',
            'content' => 'required|string',
        ];
        $validateData = Validator::make($request->all(), $rules);
        if ($validateData->fails()) {
            return redirect()->back()->with(['message' => $validateData->errors()->first(), 'alert-type' => 'error'])->withInput();
        }
        $fileData = [];
        try {
            $documentController = new DocumentController();
            $blog = new Blog();
            $blog->title = $request->title;
            $blog->slug = Str::slug($request->title);
            $blog->content = $request->content;
            $blog->author = Auth::user()->name;
            $blog->meta_title = $request->meta_title;
            $blog->meta_description = $request->meta_description;
            $blog->social_media_type = $request->social_media_type;
            $blog->social_link = $request->social_links;
            if ($request->hasFile('blog_image')) {
                $fileData[] = [
                    'docIdentifier' => 'blog_image',
                    'file' => $request->file('blog_image'),
                    'previous_file_name' => '',
                ];
                $uploadFile = $documentController->uploadFiles(Auth::user()->id, $fileData);
                if ($uploadFile['success'] == true) {
                    $blog->image = $uploadFile['file_id'];
                }
            }
            $blog->status = 1;
            $blog->save();
            return redirect()->route('blogs')->with(['message' => 'Blog created successfully', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('storeBlog: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to create Blog', 'alert-type' => 'error']);
        }
    }
    #edit blog details
    public function editBlogDetails($id)
    {
        try {
            $blogDetails = Blog::with('blogImage')->where('id', $id)->first();
            if (!$blogDetails) {
                return redirect()->back()->with(['message' => 'Blog not found', 'alert-type' => 'error']);
            }
            return view('blogs.edit', compact('blogDetails'));
        } catch (Exception $e) {
            Log::channel('exception')->error('editBlogDetails: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch Blog details', 'alert-type' => 'error']);
        }
    }

    #update blog details
    public function updateBlogDetails(Request $request, $id)
    {
        $rules = [
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'blog_image' => 'nullable|image|mimes:jpg,jpeg,png,jpg|max:2048',
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string|max:255',
            'social_media_type' => 'required|string|max:255',
            'social_links' => 'required|string|max:255',
            'content' => 'required|string',
        ];
        $validateData = Validator::make($request->all(), $rules);
        if ($validateData->fails()) {
            return redirect()->back()->with(['message' => $validateData->errors()->first(), 'alert-type' => 'error'])->withInput();
        }
        $fileData = [];
        try {
            $documentController = new DocumentController();
            $blog = Blog::where('id', $id)->first();
            if (!$blog) {
                return redirect()->back()->with(['message' => 'Blog not found', 'alert-type' => 'error']);
            }
            $blog->type = $request->type;
            $blog->title = $request->title;
            $blog->slug = Str::slug($request->title);
            $blog->content = $request->content;
            // $blog->author = Auth::user()->name;
            $blog->meta_title = $request->meta_title;
            $blog->meta_description = $request->meta_description;
            $blog->social_media_type = $request->social_media_type;
            $blog->social_link = $request->social_links;
            if ($request->hasFile('blog_image')) {
                $profileImage = Files::where('id', $blog->image)->first();
                if ($profileImage) {
                    $deleteOldFile = $documentController->deleteFile($blog->image);
                    if ($deleteOldFile) {
                        $fileData[] = [
                            'docIdentifier' => 'blog_image',
                            'file' => $request->file('blog_image'),
                            'previous_file_name' => '',
                            'product_id' => '',
                        ];
                        $uploadFile = $documentController->uploadFiles(Auth::user()->id, $fileData);
                        if ($uploadFile['success'] == true) {
                            $blog->image = $uploadFile['file_id'];
                        } else {
                            return redirect()->back()->with(['message' => 'Failed to update blog image', 'alert-type' => 'error'])->withInput();
                        }
                    } else {
                        return redirect()->back()->with(['message' => 'Failed to update blog image', 'alert-type' => 'error'])->withInput();
                    }
                } else {
                    $fileData[] = [
                        'docIdentifier' => 'blog_image',
                        'file' => $request->file('blog_image'),
                        'previous_file_name' => $blog->image,
                    ];
                    $uploadFile = $documentController->uploadFiles(Auth::user()->id, $fileData);
                    if ($uploadFile['success'] == true) {
                        $blog->image = $uploadFile['file_id'];
                    }
                }
            }
            $blog->status = 1;
            $blog->save();
            return redirect()->route('blogs')->with(['message' => 'Blog updated successfully', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('updateBlogDetails: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to update Blog', 'alert-type' => 'error']);
        }
    }

    #delete blog
    public function deleteBlog($id)
    {
        try {
            $blog = Blog::where('id', $id)->first();
            if (!$blog) {
                return redirect()->back()->with(['message' => 'Blog not found', 'alert-type' => 'error']);
            }
            if ($blog->image) {
                $documentController = new DocumentController();
                $documentController->deleteFile($blog->image);
            }
            $blog->delete();
            return redirect()->route('blogs')->with(['message' => 'Blog deleted successfully', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('deleteBlog: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to delete Blog', 'alert-type' => 'error']);
        }
    }

    #active or inactive blog
    public function activeOrInactiveBlog($id)
    {
        try {
            $blog = Blog::where('id', $id)->first();
            if (!$blog) {
                return response()->json(['status' => false, 'message' => 'Blog not found', 'alert-type' => 'error']);
            }
            if ($blog->status == 1) {
                $blog->status = 0;
                $message = 'Blog deactivated successfully';
            } else {
                $blog->status = 1;
                $message = 'Blog activated successfully';
            }
            $blog->save();
            return response()->json(['status' => true, 'message' => $message, 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('activeOrInactiveBlog: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to change blog status', 'alert-type' => 'error']);
        }
    }
}
