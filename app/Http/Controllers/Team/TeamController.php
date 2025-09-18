<?php

namespace App\Http\Controllers\Team;

use App\Events\UserCreation;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Documents\DocumentController;
use App\Http\Requests\TeamMemberRequest;
use App\Http\Requests\TeamRequest;
use App\Models\Files;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    #call permissions
    public function __construct()
    {
        $this->middleware('permission:add-team-button-teams|delete-team-button-teams|team-lists-teams', ['only' => ['listTeams']]);
        $this->middleware('permission:add-team-button-teams', ['only' => ['addTeams', 'storeTeam']]);
        $this->middleware('permission:delete-team-button-teams', ['only' => ['deleteTeam']]);
        $this->middleware('permission:add-member-button-members|edit-member-button-members|update-member-button-members|change-status-button-members|member-lists-members', ['only' => ['listMembers']]);
        $this->middleware('permission:add-member-button-members', ['only' => ['addMembers', 'storeTeamMembers']]);
        $this->middleware('permission:edit-member-button-members', ['only' => ['editMemberDetails', 'updateMemberDetails']]);
        $this->middleware('permission:update-member-button-members', ['only' => ['updateMemberDetails']]);
        $this->middleware('permission:change-status-button-members', ['only' => ['activeOrInactiveMember']]);
    }
    #list and add team members
    public function listTeams(Request $request)
    {
        try {
            if ($request->ajax()) {
                $usersList = Team::select(['id', 'name', 'description'])->orderBy('id', 'desc');
                return DataTables::of($usersList)
                    ->addIndexColumn()
                    ->make(true);
            }
            return view('Teams.index');
        } catch (Exception $e) {
            Log::channel('exception')->error('listTeams: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch teams', 'alert-type' => 'error']);
        }
    }

    #add team view page
    public function addTeams()
    {
        return view('Teams.create');
    }

    #store team
    public function storeTeam(TeamRequest $request)
    {
        try {
            $teamData = [
                'name' => $request->name,
                'description' => $request->description,
            ];
            $team = Team::create($teamData);
            if ($team) {
                return redirect()->route('teams')->with(['message' => 'Team created successfully!', 'alert-type' => 'success']);
            } else {
                return redirect()->back()->with(['message' => 'Failed to create team', 'alert-type' => 'error'])->withInput();
            }
        } catch (\Exception $e) {
            Log::channel('exception')->error('storeTeam: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to create team', 'alert-type' => 'error'])->withInput();
        }
    }

    #delete team
    public function deleteTeam($id)
    {
        Log::channel('daily')->info('deleteTeam: Attempting to delete team with ID ' . $id . ' by the user ' . Auth::user()->name);
        try {
            $team = Team::find($id);
            if (!$team) {
                return response()->json(['status' => false, 'message' => 'Team not found', 'alert-type' => 'error']);
            }
            // Check if the team has any members
            $teamMembers = User::where('team_id', $id)->count();
            if ($teamMembers > 0) {
                return response()->json(['status' => false, 'message' => 'Cannot delete team with members assigned', 'alert-type' => 'error']);
            }
            // Delete the team
            if ($team->delete()) {
                return response()->json(['status' => true, 'message' => 'Team deleted successfully!', 'alert-type' => 'success']);
            } else {
                return response()->json(['status' => false, 'message' => 'Failed to delete team', 'alert-type' => 'error']);
            }
        } catch (\Exception $e) {
            Log::channel('exception')->error('deleteTeam: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to delete team', 'alert-type' => 'error']);
        }
    }

    #list and add and store members
    public function listMembers(Request $request)
    {
        try {
            if ($request->ajax()) {
                $usersList = User::select(['id', 'name', 'email', 'team_id', 'role', 'file_id', 'created_at', 'status'])->where('email', '!=', config('global.useremail'))->with(['team', 'profile'])->orderBy('id', 'desc');
                if ($request->team_id) {
                    $usersList->where('team_id', $request->team_id);
                }
                return DataTables::of($usersList)
                    ->addIndexColumn()
                    ->editColumn('status', function ($row) {
                        return '<label class="switch">
                                <input class="form-check-input check-status-css" type="checkbox" role="switch" id="statusSwitch' . $row->id . '" ' . ($row->status == 1 ? 'checked' : '') . '
                                onchange="activeOrInactiveMember(' . $row->id . ')">
                                 <span class="slider round"></span>
                            </label>';
                    })
                    ->editColumn('name', function ($row) {
                        $name = $row->name ?? 'N/A';
                        $color = '#' . substr(md5($name), 0, 6);
                        return view('components.circle-name', ['name' => $name, 'color' => $color, 'profile' => $row->profile]);
                    })
                    ->editColumn('team', function ($row) {
                        return $row->team ? $row->team->name : 'No Team Assigned';
                    })
                    ->editColumn('created_at', function ($row) {
                        return '<div style="overflow: hidden;">' . $row->created_at->format('Y-m-d') . '</div>';
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="action-btn-box"><a href="' . route('edit.member.details', $row->id) . '" class="edit-comman-btn">Edit</a></div>';
                    })
                    ->rawColumns(['name', 'team', 'status', 'created_at', 'action'])
                    ->make(true);
            }
            return view('members.index')->with(['teams' => Team::pluck('name', 'id')->toArray()]);
        } catch (Exception $e) {
            Log::channel('exception')->error('listMembers: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch team members', 'alert-type' => 'error']);
        }
    }

    #add members view
    public function addMembers()
    {
        try {
            #Roles
            $roles = Role::pluck('name', 'id')->toArray();
            #Team Members
            $teams = Team::pluck('name', 'id')->toArray();
            return view('members.create')->with([
                'roles' => $roles,
                'teams' => $teams,
            ]);
        } catch (Exception $e) {
            Log::channel('exception')->error('addMembers: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to load add team member page', 'alert-type' => 'error']);
        }
    }

    #store team members
    public function storeTeamMembers(TeamMemberRequest $request)
    {
        try {
            $documentController = new DocumentController();
            $password = Str::password(12, true, true, true, false);
            $userData = [
                'name' => $request->member_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'password' => Hash::make($password),
                'team_id' => $request->team,
                'role' => $request->role,
            ];
            $createTeamMember = User::create($userData);
            if (!$createTeamMember) {
                return redirect()->back()->with(['message' => 'Failed to add team member.', 'alert-type' => 'error'])->withInput();
            }
            # Fire an event
            $user = ['name' => $request->member_name, 'email' => $request->email, 'tmpPassword' => $password];
            event(new UserCreation($user));
            #end Event                

            if ($request->hasFile('profile_pic')) {
                $fileData[] = [
                    'docIdentifier' => 'profile_pic',
                    'file' => $request->file('profile_pic'),
                    'previous_file_name' => '',
                    'product_id' => '',
                ];
                $uploadFile = $documentController->uploadFiles(Auth::user()->id, $fileData);
                if ($uploadFile['success'] == true) {
                    $createTeamMember->file_id = $uploadFile['file_id'];
                    $createTeamMember->save();
                }
            }
            #assign role to the user
            $createTeamMember->assignRole($request->role);
            return redirect()->route('members')->with(['message' => 'Team members added successfully!', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('storeTeamMembers: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to add team member', 'alert-type' => 'error'])->withInput();
        }
    }

    #active or inactive member
    public function activeOrInactiveMember($id)
    {
        Log::channel('daily')->info('activeOrInactiveMember: Attempting to change member status of userId: ' . $id . ' by the user ' . Auth::user()->name);
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User not found.', 'alert-type' => 'error']);
            }
            $user->status = !$user->status;
            if ($user->save()) {
                return response()->json(['status' => true, 'message' => 'User status updated successfully.', 'alert-type' => 'success']);
            } else {
                return response()->json(['status' => false, 'message' => 'Failed to update user status.', 'alert-type' => 'error']);
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('activeOrInactiveMember: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to update user status.', 'alert-type' => 'error']);
        }
    }

    #get team member list
    public function getTeamMemberList($teamid)
    {
        try {
            $teamMembers = User::where(['team_id' => $teamid, 'status' => 1])->pluck('name', 'id')->toArray();
            if (empty($teamMembers)) {
                return response()->json(['status' => false, 'message' => 'No members found', 'alert-type' => 'error']);
            }
            return response()->json(['status' => true, 'members' => $teamMembers, 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('getTeamMemberList: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to fetch team members.', 'alert-type' => 'error']);
        }
    }

    #edit member details
    public function editMemberDetails($id)
    {
        try {
            $memberDetails = User::where('status', 1)->with(['team', 'profile', 'roledetail'])->find($id);
            if (empty($memberDetails)) {
                return redirect()->back()->with(['message' => 'Member either inactive or failed to edit member details', 'alert-type' => 'error']);
            }
            #Roles
            $roles = Role::pluck('name', 'id')->toArray();
            #Team Members
            $teams = Team::pluck('name', 'id')->toArray();
            return view('members.edit', [
                'roles' => $roles,
                'teams' => $teams,
                'user' => $memberDetails
            ]);
        } catch (Exception $e) {
            Log::channel('exception')->error('editMemberDetails: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to load edit team member page', 'alert-type' => 'error']);
        }
    }

    #update member details
    public function updateMemberDetails(Request $request, $id)
    {
        Log::channel('daily')->info('updateMemberDetails: Attempting to change details:' . json_encode($request->all()) . ' of userId: ' . $id . ' by the user ' . Auth::user()->name);
        try {
            $validate = Validator::make($request->all(), [
                'team' => 'required',
                'member_name' => 'required|string|max:255',
                'role' => 'required',
                'mobile' => 'required|numeric',
                'email' => 'required|email|unique:users,email,' . $id,
                'profile_pic' => 'nullable|file|mimes:jpg,jpeg,png|max:200'
            ], [
                'member_name.required' => 'Please enter a name',
                'mobile.required' => 'Please enter a mobile number',
                'mobile.numeric' => 'The mobile number must be a valid number',
                'email.required' => 'Please enter an email address',
                'email.email' => 'The email must be a valid email address',
                'role.required' => 'Please select a role',
                'team.required' => 'Please select a team',
                'profile_pic.mimes' => 'Profile picture must be a JPG, JPEG, or PNG file',
                'profile_pic.max' => 'Profile picture must not exceed 200KB in size',
            ]);

            if ($validate->fails()) {
                return redirect()->back()->with(['message' => $validate->errors()->first(), 'alert-type' => 'error'])->withInput();
            }

            $documentController = new DocumentController();
            $user = User::findOrFail($id);
            if (!$user) {
                return redirect()->back()->with(['message' => 'Failed to fetch member detail', 'alert-type' => 'error'])->withInput();
            }
            // Update fields
            $user->name = $request->member_name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->team_id = $request->team;
            $user->role = $request->role;
            $user->save();

            if ($request->hasFile('profile_pic')) {
                $profileImage = Files::where('id', $user->file_id)->first();
                if ($profileImage) {
                    $deleteOldFile = $documentController->deleteFile($user->file_id);
                    if ($deleteOldFile) {
                        $fileData[] = [
                            'docIdentifier' => 'profile_pic',
                            'file' => $request->file('profile_pic'),
                            'previous_file_name' => '',
                            'product_id' => '',
                        ];
                        $uploadFile = $documentController->uploadFiles(Auth::user()->id, $fileData);
                        if ($uploadFile['success'] == true) {
                            $user->file_id = $uploadFile['file_id'];
                            $user->save();
                        } else {
                            return redirect()->back()->with(['message' => 'Failed to update user profile', 'alert-type' => 'error'])->withInput();
                        }
                    } else {
                        return redirect()->back()->with(['message' => 'Failed to update user profile', 'alert-type' => 'error'])->withInput();
                    }
                } else {
                    $fileData[] = [
                        'docIdentifier' => 'profile_pic',
                        'file' => $request->file('profile_pic'),
                        'previous_file_name' => '',
                        'product_id' => '',
                    ];
                    $uploadFile = $documentController->uploadFiles(Auth::user()->id, $fileData);
                    if ($uploadFile['success'] == true) {
                        $user->file_id = $uploadFile['file_id'];
                        $user->save();
                    } else {
                        return redirect()->back()->with(['message' => 'Failed to update user profile', 'alert-type' => 'error'])->withInput();
                    }
                }
            }
            #assign role to the user
            $user->syncRoles([$request->role]);
            return redirect()->route('members')->with(['message' => 'Team members updated successfully!', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('updateMemberDetails: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to update team member', 'alert-type' => 'error'])->withInput();
        }
    }
}
