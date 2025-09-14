<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Documents\DocumentController;
use App\Models\Files;
use App\Models\Project;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProjectController extends Controller
{
    #call permissions
    public function __construct()
    {
        $this->middleware('permission:project-lists-projects|add-project-button-projects|edit-project-button-projects|update-project-button-projects|delete-project-button-projects|change-status-button-projects', ['only' => ['projectLists']]);
        $this->middleware('permission:add-project-button-projects', ['only' => ['addProject','storeProject']]);
        $this->middleware('permission:edit-project-button-projects|update-project-button-projects', ['only' => ['editProjectDetails','updateProjectDetails']]);
        $this->middleware('permission:delete-project-button-projects', ['only' => ['deleteProject']]);
        $this->middleware('permission:change-status-button-projects', ['only' => ['activeOrInactiveProject']]);
    }
    #project lists datatable
    public function projectLists(Request $request)
    {
        try {
            if ($request->ajax()) {
                $projects = Project::select('*')
                    ->with(['projectImage'])
                    ->orderBy('id', 'desc');
                return DataTables::of($projects)
                    ->addIndexColumn()
                    ->editColumn('image', function ($row) {
                        $imageUrl = $row->projectImage ? asset($row->projectImage->file_path) : asset('assets/images/default.png');
                        return '<img src="' . $imageUrl . '" alt="Project Image" width="50" height="50">';
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('d-m-Y H:i:s') : 'N/A';
                    })
                    ->editColumn('status', function ($row) {
                        return '<label class="switch">
                                <input class="form-check-input check-status-css" type="checkbox" role="switch" id="statusSwitch' . $row->id . '" ' . ($row->status == 1 ? 'checked' : '') . '
                                onchange="activeOrInactiveProjects(' . $row->id . ', ' . ($row->status == 1 ? 0 : 1) . ')">
                                 <span class="slider round"></span>
                            </label>';
                    })
                    ->editColumn('action', function ($row) {
                        $btn = '<a href="' . route('edit.project.details', ['id' => $row->id]) . '" class="btn btn-info btn-sm" title="Edit Project" style="margin-right: 5px;background: #5F4CDD;"><i class="fa fa-edit"></i></a>';
                        $btn .= '<a href="' . route('delete.project', ['id' => $row->id]) . '" class="btn btn-danger btn-sm deleteProject" title="Delete Project" style="background: #FF4A4A;"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['image', 'created_at', 'action', 'status'])
                    ->make(true);
            }
            return view('projects.project');
        } catch (Exception $e) {
            Log::channel('exception')->error('projectList: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch Project lists', 'alert-type' => 'error']);
        }
    }

    #add project
    public function addProject()
    {
        return view('projects.add');
    }

    #store project
    public function storeProject(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'project_image' => 'nullable|image|mimes:jpg,jpeg,png,jpg|max:2048',
            'project_url' => 'required|string|max:255',
            'project_description' => 'required|string|max:1000',
        ];
        $validateData = Validator::make($request->all(), $rules);
        if ($validateData->fails()) {
            return redirect()->back()->with(['message' => $validateData->errors()->first(), 'alert-type' => 'error'])->withInput();
        }
        $fileData = [];
        try {
            $documentController = new DocumentController();
            $project = new Project();
            $project->name = $request->name;
            $project->title = $request->title;
            $project->user_id = Auth::user()->id;
            $project->project_url = $request->project_url;
            $project->description = $request->project_description;
            if ($request->hasFile('project_image')) {
                $fileData[] = [
                    'docIdentifier' => 'project_image',
                    'file' => $request->file('project_image'),
                    'previous_file_name' => '',
                ];
                $uploadFile = $documentController->uploadFiles(Auth::user()->id, $fileData);
                if ($uploadFile['success'] == true) {
                    $project->image = $uploadFile['file_id'];
                }
            }
            $project->status = 1;
            $project->save();
            return redirect()->route('projects')->with(['message' => 'Project created successfully', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('storeProject: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to create Project', 'alert-type' => 'error']);
        }
    }
    #edit project details
    public function editProjectDetails($id)
    {
        try {
            $projectDetails = Project::with('projectImage')->where('id', $id)->first();
            if (!$projectDetails) {
                return redirect()->back()->with(['message' => 'Project not found', 'alert-type' => 'error']);
            }
            return view('projects.edit', compact('projectDetails'));
        } catch (Exception $e) {
            Log::channel('exception')->error('editProjectDetails: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch Project details', 'alert-type' => 'error']);
        }
    }

    #update project details
    public function updateProjectDetails(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'project_image' => 'nullable|image|mimes:jpg,jpeg,png,jpg|max:2048',
            'project_url' => 'required|string|max:255',
            'project_description' => 'required|string|max:1000',
        ];
        $validateData = Validator::make($request->all(), $rules);
        if ($validateData->fails()) {
            return redirect()->back()->with(['message' => $validateData->errors()->first(), 'alert-type' => 'error'])->withInput();
        }
        $fileData = [];
        try {
            $documentController = new DocumentController();
            $project = Project::where('id', $id)->first();
            if (!$project) {
                return redirect()->back()->with(['message' => 'Project not found', 'alert-type' => 'error']);
            }
            $project->name = $request->name;
            $project->title = $request->title;
            $project->project_url = $request->project_url;
            $project->description = $request->project_description;
            if ($request->hasFile('project_image')) {
                $profileImage = Files::where('id', $project->image)->first();
                if ($profileImage) {
                    $deleteOldFile = $documentController->deleteFile($project->image);
                    if ($deleteOldFile) {
                        $fileData[] = [
                            'docIdentifier' => 'project_image',
                            'file' => $request->file('project_image'),
                            'previous_file_name' => '',
                            'product_id' => '',
                        ];
                        $uploadFile = $documentController->uploadFiles(Auth::user()->id, $fileData);
                        if ($uploadFile['success'] == true) {
                            $project->image = $uploadFile['file_id'];
                        } else {
                            return redirect()->back()->with(['message' => 'Failed to update project image', 'alert-type' => 'error'])->withInput();
                        }
                    } else {
                        return redirect()->back()->with(['message' => 'Failed to update project image', 'alert-type' => 'error'])->withInput();
                    }
                } else {
                    $fileData[] = [
                        'docIdentifier' => 'project_image',
                        'file' => $request->file('project_image'),
                        'previous_file_name' => $project->image,
                    ];
                    $uploadFile = $documentController->uploadFiles(Auth::user()->id, $fileData);
                    if ($uploadFile['success'] == true) {
                        $project->image = $uploadFile['file_id'];
                    }
                }
            }
            $project->save();
            return redirect()->route('projects')->with(['message' => 'Project updated successfully', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('updateProjectDetails: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to update Project', 'alert-type' => 'error']);
        }
    }

    #delete project
    public function deleteProject($id)
    {
        try {
            $project = Project::where('id', $id)->first();
            if (!$project) {
                return redirect()->back()->with(['message' => 'Project not found', 'alert-type' => 'error']);
            }
            if ($project->image) {
                $documentController = new DocumentController();
                $documentController->deleteFile($project->image);
            }
            $project->delete();
            return redirect()->route('projects')->with(['message' => 'Project deleted successfully', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('deleteProject: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to delete Project', 'alert-type' => 'error']);
        }
    }

    #active or inactive project
    public function activeOrInactiveProject($id)
    {
        try {
            $project = Project::where('id', $id)->first();
            if (!$project) {
                return response()->json(['status' => false, 'message' => 'Project not found', 'alert-type' => 'error']);
            }
            if ($project->status == 1) {
                $project->status = 0;
                $message = 'Project status changed successfully';
            } else {
                $project->status = 1;
                $message = 'Project status changed successfully';
            }
            $project->save();
            return response()->json(['status' => true, 'message' => $message, 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('activeOrInactiveProject: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to change project status', 'alert-type' => 'error']);
        }
    }
}
