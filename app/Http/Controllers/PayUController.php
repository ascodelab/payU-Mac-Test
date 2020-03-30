<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StateList;
use App\CodeDetails;
use Illuminate\Support\Carbon;
use Validator;
use Illuminate\Support\Facades\Cache;
use Excel;

class PayUController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Showing the application dashboard. Storing the states data into the cache file
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Cache::has('state_list')) {
            $StateList = StateList::all()->pluck('state_name', 'id');
            Cache::forever('state_list', json_encode($StateList));
            $stateList = Cache::get('state_list');
        }
        else{
            $stateList = Cache::get('state_list');
        }
        $stateList = json_decode($stateList);
        $CodeData = CodeDetails::whereDate('created_at', '=', Carbon::today()->toDateString())->get();
        
        return view('home',compact('stateList','CodeData'));
    }


    /**
     * Creates new code. Used Mysql transaction and locking code details table at the time
     * of creating the code so that no two users can read the same auto increment key at 
     * the same time
     * @param  Request $request Form Input values
     * @return Json           Success message and new code created
     */
    public function createCode(Request $request){
        $validatedData = Validator::make($request->all(),[
            'state_id' => 'required|exists:state_lists,id',
            'a_type' => 'required|in:C,S',
            'd_type' => 'required|in:G,P,U',
            'ref_no' => 'required|numeric|digits_between:1,20',
        ]);
        if ($validatedData->fails()) {
            return ['status' => 'false', 'errors' => $validatedData->errors()];
        }

        try {
            \DB::beginTransaction();

            $code = CodeDetails::latest()->lockForUpdate()->value('id');
            if(empty($code)){
                $code = 123;
            }
            else{
                $code = $code + 1;    
            }
            
            $stateList = Cache::get('state_list');
            $stateList = json_decode($stateList, true);

            $stateName = $stateList[$request->state_id];
            

            $newCode = substr($stateName, 0,2);
            $newCode = strtoupper($newCode).$request->a_type.$request->d_type.$code;
            
            $codeDetailsObj = new CodeDetails();
            
            $codeDetailsObj->state_id = $request->state_id;
            $codeDetailsObj->a_type = $request->a_type;
            $codeDetailsObj->d_type = $request->d_type;
            $codeDetailsObj->ref_no = $request->ref_no;
            $codeDetailsObj->user_code = $newCode;
            $codeDetailsObj->created_at = date('Y-m-d H:i:s');
            if($codeDetailsObj->save()){
                \DB::commit();
                return response()->json(['status' => 'true', 'newCode' => $newCode]);            
            }
            else{
                \DB::rollBack();
                return response()->json(['status' => 'false', 'newCode' => 'null']);
            }   
        } catch (Exception $e) {
            \Log::info($e->getMessage());
            return response()->json(['status' => 'false', 'newCode' => 'null']);
        }        
    }

    /**
     * Searching the codes from code details table.
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function searchCode(Request $request){
        $validatedData = Validator::make($request->all(),[
            'toDate' => 'required|date_format:Y-m-d',
            'fromDate' => 'required|date_format:Y-m-d',
        ]);
        if ($validatedData->fails()) {
            return ['status' => 'false', 'errors' => $validatedData->errors()];
        }
        try {
            /*
                Converting datetime into date format for the filteration of data
             */
            $data = CodeDetails::whereBetween(\DB::raw('date(created_at)'),[$request->fromDate,$request->toDate])->get();
            $data->load('stateName');
            return response()->json(['status' => 'true', 'data'=> $data]);
        } catch (Exception $e) {
            \Log::info($e->getMessage());
            return response()->json(['status' => 'error', 'data'=> 'null']);
        }  
    }


    /**
     * To Export the data into CSV File. First checking whether start date and end date is
     * available  or not, if not available fetching the data from the data for current date only
     * else fetching based on the filteration value 
     * @param  Request $request Start date and End date
     * @return [type]           [description]
     */

    public function deleteCode($id){
        $codeData = CodeDetails::find($id);
        if(!empty($codeData)){
            $codeData->delete();
            return redirect()->back()->with('success', 'Code has been deleted success');
        }
        else{
            return redirect()->back()->with('error', 'No matched code found'); 
        }
    }



    /**
     * For editing the Ref No. Getting the id in the input, fetching the data from the table,
     * and updating the values
     * @param  Request $request ref No and id
     * @return Redirecting to previous page with message info
     */
    public function editCode(Request $request){
        $validatedData = Validator::make($request->all(),[
            'ref_no' => 'required|numeric|digits_between:1,20',
            'code_id' => 'required|numeric'
        ]);
        if ($validatedData->fails()) {
            return ['status' => 'false', 'errors' => $validatedData->errors()];
        }

        $codeData = CodeDetails::find($request->code_id);

        if(!empty($codeData)){
            $codeData->ref_no = $request->ref_no;
            $codeData->save();
            return redirect()->back()->with('success', 'Ref no. has been updated successfully');
        }
        else{
            return redirect()->back()->with('error', 'No matched code found');
        }
    }
}
