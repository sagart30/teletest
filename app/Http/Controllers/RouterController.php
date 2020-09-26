<?php

namespace App\Http\Controllers;

use App\Http\Services\RouterService;
use Illuminate\Http\Request;
use App\Models\User;

class RouterController extends Controller
{
    protected $service;

    public function __construct(RouterService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $domain 	=  isset($request['domain']) && $request['domain'] != '' ? $request['domain'] : '';
        $loopback   =  isset($request['loopback']) && $request['loopback'] != '' ? $request['loopback'] : '';
        $mac   =  isset($request['mac']) && $request['mac'] != '' ? $request['mac'] : '';

        $params = ['domain' => $domain, 'loopback' => $loopback, 'mac' => $mac];

        $items = $this->service->paginateWithParams($params, $perPage=10);
        
        return view('router.index', ['items' => $items, 'params' => $params]);
    }

    public function create()
    {
        return view('router.create');
    }

    public function store(Request $request)
    {   
    	$data = $request->toArray();
    	unset($data['_token']);

    	$regexp = '/^[a-zA-Z0-9][a-zA-Z0-9\-\_]+[a-zA-Z0-9]$/';
		if (false === preg_match($regexp, $data['domain'])) {
	        return back()->with('errorMsg','Please enter valid domain');
		}
		if(!filter_var($data['loopback'], FILTER_VALIDATE_IP)) {
	        return back()->with('errorMsg','Please enter valid loopback');
		}

		if(!filter_var($data['mac'], FILTER_VALIDATE_MAC)) {
	        return back()->with('errorMsg','Please enter valid mac');
		}

        $this->service->store($data);
        return redirect()->route('router.index')->with('success', 'Product added successfully');
    }

    public function edit($id)
    {
        $item = $this->service->edit($id);
        return view('router.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
    	$data = $request->toArray();
    	unset($data['_token']);
    	$data['status'] = (isset($data['status'])) ? '1' : '0';
    	#print_r($data);exit;
    	$regexp = '/^[a-zA-Z0-9][a-zA-Z0-9\-\_]+[a-zA-Z0-9]$/';
		if (false === preg_match($regexp, $data['domain'])) {
	        return back()->with('errorMsg','Please enter valid domain');
		}
		if(!filter_var($data['loopback'], FILTER_VALIDATE_IP)) {
	        return back()->with('errorMsg','Please enter valid loopback');
		}

		if(!filter_var($data['mac'], FILTER_VALIDATE_MAC)) {
	        return back()->with('errorMsg','Please enter valid mac');
		}
        $this->service->update($data, $id);
        return redirect()->route('router.index')->with('success', 'Product updated successfully');
    }

    public function getToken(Request $request){
	    $user = User::where('email', 'jd@test.com')->firstOrFail();
	    echo $user->createToken('bearer')->accessToken;
    }
}
