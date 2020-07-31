<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;
use App\User; 
use App\Contact;
use Validator;

class UserController extends Controller
{
    public $successStatus = 200;

    /** 
 	* login api 
    * 
    * @return \Illuminate\Http\Response 
    */ 
    public function login()
    { 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            return response()->json(['success' => $success], $this-> successStatus); 
        } 
        else
        { 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
	
	/** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
		
		if ($validator->fails())
		{ 
		    return response()->json(['error'=>$validator->errors()], 401);  
		}
			$input = $request->all(); 
	        $input['password'] = bcrypt($input['password']); 
	        $user = User::create($input); 
	        $success['token'] =  $user->createToken('MyApp')-> accessToken; 
	        $success['name'] =  $user->name;
		return response()->json(['success'=>$success], $this-> successStatus); 
    }

	/** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function details() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
    }

    public function contact(Request $request)
    {
        $user_id = $request->user_id;
        $contact = User::findOrFail($user_id);
        $contactdata = $contact->contacts;
        
        return response()->json(['data'=>$contactdata], 200);
    }

    public function usercontact(Request $request)
    {
        $contact_id = $request->contact_id;
        $contact = Contact::findOrFail($contact_id);
        $userdata = $contact->users;

        return response()->json(['data'=>$userdata], 200);
    }

    public function updatecontact(Request $request)
    {
        $data = array();
        try
        {
            $validator = Validator::make($request->all(),[
                'contactname'=>'required',
                'contactno'=>'required'
            ]);

            if ($validator->fails())
            {
                return response()->json(['errors'=>$validator->errors(),'success'=>false], 401);
            }

            Contact::updateOrInsert(
                ['contactname'=>$request->contactname],
                ['contactno'=>$request->contactno]
            );
        
            return response()->json(['data'=>$data,'success'=>true], $this-> successStatus);
        }
        catch (Exception $e)
        {   
            return response()->json(['error'=>$e->getMessage(),'success'=>false], 401);
        }
    }

    public function createcontact(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(),[
                'contactname'=>'required',
                'contactno'=>'required'
            ]);

            if ($validator->fails())
            {
                return response()->json(['errors'=>$validator->errors(),'success'=>false], 401);
            }

            // $contactdata = [
            //     'user_id'  =>  Auth::user()->id,
            //     'contactname'  =>  "Dixit Dobariya",
            //     'contactno'  =>  1236547890
            // ];
            // Contact::insert($contactdata);
            
            $contactdata['user_id'] = Auth::user()->id;
            $contactdata['contactname'] = $request->contactname;
            $contactdata['contactno'] = $request->contactno;
            $contact = Contact::create($contactdata);

            // $contact = new Contact;
            // $contact->user_id = Auth::user()->id;
            // $contact->contactname = $request->contactname;
            // $contact->contactno = $request->contactno;
            // $contact->save();

            return response()->json(['data'=>$contact,'success'=>true], $this-> successStatus);
        }
        catch (Exception $e)
        {
            return response()->json(['error'=>$e->getMessage(),'success'=>false], 401);
        }
    }
}