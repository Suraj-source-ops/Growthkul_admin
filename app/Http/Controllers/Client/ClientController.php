<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ClientController extends Controller
{
    #call permissions
    public function __construct()
    {
        $this->middleware('permission:client-lists-clients|add-client-button-clients|change-status-button-clients|edit-client-button-clients|update-client-button-clients|delete-client-product-clients|view-client-detail-button-clients|add-client-product-button-clients', ['only' => ['clients']]);
        $this->middleware('permission:add-client-button-clients', ['only' => ['addClients', 'storeClient']]);
        $this->middleware('permission:add-client-product-button-clients', ['only' => ['clientsDetails']]);
        $this->middleware('permission:edit-client-button-clients', ['only' => ['editClientsDetails']]);
        $this->middleware('permission:update-client-button-clients', ['only' => ['updateClientsDetails']]);
        $this->middleware('permission:change-status-button-clients', ['only' => ['activeOrInactiveClient']]);
    }

    #list clients
    public function clients(Request $request)
    {
        try {
            if ($request->ajax()) {
                $clientsLists = Client::select('*')->with('products')->orderBy('id', 'desc');
                return DataTables::of($clientsLists)
                    ->addIndexColumn()
                    ->editColumn('products_count', function ($row) {
                        return "<a href='" . route('product.lists', ['client_id' => $row->id]) . "'>" . ($row->products ? $row->products->count() : 0) . "</a>";
                    })
                    ->editColumn('status', function ($row) {
                        return view('clients.components.switch', ['row' => $row]);
                    })
                    ->editColumn('action', function ($row) {
                        return view('clients.components.client-buttons', ['row' => $row]);
                    })
                    ->rawColumns(['products_count', 'status', 'action'])
                    ->make(true);
            }
            return view('clients.clients');
        } catch (Exception $e) {
            Log::channel('exception')->error('clients: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch clients', 'alert-type' => 'error']);
        }
    }

    #add clients view   
    public function addClients()
    {
        return view('clients.create');
    }

    #store clients
    public function storeClient(ClientRequest $request)
    {
        Log::channel('daily')->info('storeClient: Try to add client details: ' . json_encode($request->all()) . ' by the user ' . Auth::user()->name);
        try {
            $client = [
                'clientid' => 'LT-CLT-' . time() . '-' . rand(1000, 9999),
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
            ];
            #create client
            $client = Client::create($client);
            if ($client) {
                return redirect()->route('client.details', ['id' => $client->clientid])->with(['client_id' => $client->clientid, 'message' => 'Client added successfully', 'alert-type' => 'success']);
            } else {
                return redirect()->back()->with(['client_id' => '', 'message' => 'Failed to add client', 'alert-type' => 'error']);
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('storeClient: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to add client', 'alert-type' => 'error']);
        }
    }

    #clients details
    public function clientsDetails($id)
    {
        try {
            $client = Client::where(['is_active' => 1, 'clientId' => $id])->with('products')->first();
            if (!$client) {
                return redirect()->route('clients')->with(['message' => 'Client records either missing or inactive', 'alert-type' => 'error']);
            }
            $products = empty($client->products) ? [] : $client->products;
            return view('clients.clients-details', ['client' => $client, 'products' => $products]);
        } catch (Exception $e) {
            Log::channel('exception')->error('clientsDetails: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch clients details', 'alert-type' => 'error']);
        }
    }

    #edit clients details
    public function editClientsDetails($clientid = null)
    {
        try {
            $client = Client::where(['clientid' => $clientid, 'is_active' => 1])->with('products')->first();
            if (!$client) {
                return redirect()->route('clients')->with(['message' => 'Please activate the client before edit the client details', 'alert-type' => 'error']);
            }
            $products = empty($client->products) ? [] : $client->products;
            return view('clients.edit-clients-details', ['client' => $client, 'products' => $products]);
        } catch (Exception $e) {
            Log::channel('exception')->error('editClientsDetails: ' . $e->getMessage());
            return redirect()->route('clients')->with(['message' => 'Failed to edit the client details', 'alert-type' => 'error']);
        }
    }

    #store clients
    public function updateClientsDetails(ClientRequest $request)
    {
        $client = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
        ];
        Log::channel('daily')->info('updateClientsDetails: Try to update client details: ' . json_encode($client) . ' by the user ' . Auth::user()->name);
        try {
            #create client
            $client = Client::where('id', $request->id)->update($client);
            if ($client) {
                return redirect()->route('clients')->with(['message' => 'Client updated successfully', 'alert-type' => 'success']);
            } else {
                return redirect()->back()->with(['client_id' => '', 'message' => 'Failed to update client record', 'alert-type' => 'error']);
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('updateClientsDetails: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to update client details', 'alert-type' => 'error']);
        }
    }

    #active or inactive Clients
    public function activeOrInactiveClient($id)
    {
        Log::channel('daily')->info('activeOrInactiveClient: Attempting to change client status of ID: ' . $id . ' by the user ' . Auth::user()->name);
        try {
            $client = Client::find($id);
            if (!$client) {
                return response()->json(['status' => false, 'message' => 'Client not found', 'alert-type' => 'error']);
            }
            $client->is_active = !$client->is_active;
            if ($client->save()) {
                return response()->json(['status' => true, 'message' => 'Client status updated successfully', 'alert-type' => 'success']);
            } else {
                return response()->json(['status' => false, 'message' => 'Failed to update client status', 'alert-type' => 'error']);
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('activeOrInactiveClient: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to update client status', 'alert-type' => 'error']);
        }
    }
}
