<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Country::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'    
        ]);
        
        return Country::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Country::find($id);
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
        $country = Country::find($id);
        $country->update($request->all());
        return $country;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Country::destroy($id);
    }
    
    /**
     * Remove everything from the table.
     *
     * @return \Illuminate\Http\Response
     */
    public function truncate()
    {
        return Country::truncate();
    }
    
    /**
     * Restarts the table's sequence.
     *
     * @return \Illuminate\Http\Response
     */
    public function restart()
    {
        return DB::statement("ALTER SEQUENCE countries_id_seq RESTART;");
    }
    
    /**
     * Search for a name.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function getIdByName($name)
    {
        return Country::where('name', $name)->value('id');
    }
    
}
