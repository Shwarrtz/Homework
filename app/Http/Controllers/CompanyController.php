<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Response;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Hash;
// use Throwable;
// use App\Exceptions;
use Psy\Exception\ErrorException;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Company::all();
    }

    /**
     * Prepares the given dara for insertion.
     *
     * @param  array  $data
     */
    public function prepare(&$data)
    {
        $data['email'] = strtolower($data['email']);
        $data['password'] = Hash::make($data['password']);
        
        $countryId = (new CountryController)->getIdByName($data['country_id']);
        if (empty($countryId)){
            $countryRequest = $this->createRequest(['name' => $data['country_id']]);
            $countryId = (new CountryController)->store($countryRequest)['id'];
        }
        $data['country_id'] = $countryId;
        
        $cityId = (new CityController)->getIdByName($data['city_id']);
        if (empty($cityId)){
            $cityRequest = $this->createRequest(['name' => $data['city_id']]);
            $cityId = (new CityController)->store($cityRequest)['id'];
        }
        $data['city_id'] = $cityId;
        
        $activityId = (new ActivityController)->getIdByName($data['activity_id']);
        if (empty($activityId)){
            $activityRequest = $this->createRequest(['name' => $data['activity_id']]);
            $activityId = (new ActivityController)->store($activityRequest)['id'];
        }
        $data['activity_id'] = $activityId;
    }
    
    /**
     * Validates the given data..
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function validateRequest(Request $request)
    {
        return 
        $request->validate([
            'name' => 'required',
            'reg_num' => 'required',
            'found_date' => 'required',
            'country_id' => 'required',
            'city_id' => 'required',
            'owner' => 'required',
            'employees' => 'required',
            'activity_id' => 'required',
            'active' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);
        
        //'email' => 'email:rfc,dns',
    }
    
    /**
     * Create simple post request with data array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function createRequest($data) {
        $request = new \Illuminate\Http\Request();
        $request->setMethod('POST');
        $request->request->add($data);
        
        return $request;
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Company::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function insert(Request $request)
    {
        $this->validateRequest($request);
        $data = $request->all();
        $company = $this->getIdByRegNum($data['reg_num']);
        
        if (empty($company))
        {
            $this->prepare($data);
            $req = $this->createRequest($data);
            
            return $this->store($req);
        } else {
            return true;   
        }
    }
    
    //Add user method in existing post model
    public function country(){
        return $this->belongsTo('App\Models\Country');
    }
    
    /**
     * Returns companies data by given ids.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $ids
     * @return \Illuminate\Http\Response
     */
    public function getByIds(Request $request, $ids)
    {
//         return Company::with('country:countries.id,name')
//                       ->with('city:cities.id,name')
//                       ->with('activity:activities.id,name')
//                       ->whereIn('companies.id', explode(";", $ids))
//                       ->get();
        
        return Company::join('countries', 'companies.country_id', '=', 'countries.id')
                      ->join('cities', 'companies.city_id', '=', 'cities.id')
                      ->join('activities', 'companies.activity_id', '=', 'activities.id')
                      ->select('companies.*', 'countries.name AS country_name', 'cities.name AS city_name', 'activities.name AS activity_name')
                      ->whereIn('companies.id', explode(";", $ids))
                      ->get();
    }
    
    /**
     * Returns companies data by given ids.
     *
     * @param  string  $reg_num
     * @return \Illuminate\Http\Response
     */
    public function getByIdsV2(Request $request)
    {
        $params = $request->all();
        return Company::whereIn('id', explode(";", $params['ids']))->get();
    }
    
    /**
     * Lists the companies by Activities.
     *
     * @param  string $start_date
     * @param  string $end_date
     * @return \Illuminate\Http\Response
     */
    public function listCompanyActivity()
    {
        $activities = (new ActivityController)->index()->pluck('name','id')->toArray();
        foreach ($activities as $value){
            $ids[$value] = ''; 
        }
        $companies = Company::select('companies.name', 'activity_id', 'activities.name AS activity_name')
                            ->join('activities', 'companies.activity_id', '=', 'activities.id')
                            ->orderBy('name', 'asc')
                            ->get()
                            ->toArray();
        $ret = [];
        foreach ($companies as $key => $value){
            $ret[$key] = $ids;
            $ret[$key][$value['activity_name']] = $value['name'];
        }
        
        return $ret;
    }
    
    /**
     * Lists the dates between the given dates by daily increments and the founded companies on the given days.
     *
     * @param  string $start_date
     * @param  string $end_date
     * @return \Illuminate\Http\Response
     */
    public function listFoundDate($start_date='', $end_date='')
    {
        return DB::select("SELECT * FROM list_found_date('$start_date', '$end_date')");
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Company::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $company = Company::find($id);
        $company->update($request->all());
        return Company::find($id);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateV2(Request $request)
    {
        $params = $request->all();
        $company = Company::find($params['id']);
        $company->update($request->all());
        return Company::find($params['id']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Company::destroy($id);
    }
    
    /**
     * Search for a name.
     *
     * @param  string  $reg_num
     * @return \Illuminate\Http\Response
     */
    public function getIdByRegNum($reg_num)
    {
        return Company::where('reg_num', $reg_num)->value('id');
    }
    
    public function uploadContent(Request $request)
    {
        $file = $request->file('uploaded_file');
        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            
            $this->checkUploadedFileProperties($extension, $fileSize);
            
            $location = 'uploads';
            $file->move($location, $filename);
            $filepath = public_path($location . "/" . $filename);
            
            $file = fopen($filepath, "r");
            $importData_arr = array();
            $i = 0;
            
            while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
                $num = count($filedata);
                if (!empty($filedata[0]) && is_numeric($filedata[0])) {
                    for ($c = 0; $c < $num; $c++) {
                        $importData_arr[$i][] = $filedata[$c];
                    }
                    $i++;
                } else {
                    continue;
                }
            }
            
            fclose($file);
            $j = 0;
            foreach ($importData_arr as $importData) {
                $j++;
                try {
                    DB::beginTransaction();
                    
                    $data = [
                        'name' => $importData[1],
                        'reg_num' => $importData[2],
                        'found_date' => $importData[3],
                        'country_id' => $importData[4],
                        'zip_code' => $importData[5],
                        'city_id' => $importData[6],
                        'street_address' => $importData[7],
                        'latitude' => $importData[8],
                        'longitude' => $importData[9],
                        'owner' => $importData[10],
                        'employees' => $importData[11],
                        'activity_id' => $importData[12],
                        'active' => $importData[13],
                        'email' => $importData[14],
                        'password' => $importData[15]
                    ];
                    
                    $req = $this->createRequest($data);
                    $this->insert($req);
                    
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                }
            }
            return response()->json([
                'message' => "$j records successfully uploaded"
            ]);
        } else {
            throw new \Exception('No file was uploaded', Response::HTTP_BAD_REQUEST);
        }
    }
    
    public function checkUploadedFileProperties($extension, $fileSize)
    {
        $valid_extension = array("csv", "xlsx");
        $maxFileSize = 2097152;
        if (in_array(strtolower($extension), $valid_extension)) {
            if ($fileSize <= $maxFileSize) {
            } else {
                throw new \Exception('No file was uploaded', Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
            }
        } else {
            throw new \Exception('Invalid file extension', Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }
    }
    
    /**
     * Remove everything from the table.
     *
     * @return \Illuminate\Http\Response
     */
    public function truncate()
    {
        return Company::truncate();
    }
    
    /**
     * Restarts the table's sequence.
     *
     * @return \Illuminate\Http\Response
     */
    public function restart()
    {
        return DB::statement("ALTER SEQUENCE companies_id_seq RESTART;");
    }
    
    /**
     * Resets the tables and the sequences, for a clean loading.
     *
     * @return \Illuminate\Http\Response
     */
    public function reset()
    {
        $this->truncate();
        $this->restart();
        (new CountryController)->truncate();
        (new CountryController)->restart();
        (new CityController)->truncate();
        (new CityController)->restart();
        (new ActivityController)->truncate();
        (new ActivityController)->restart();
    }
}
