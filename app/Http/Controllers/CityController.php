<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return City::all();
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
        
        return City::create($request->all());
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return City::find($id);
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
        $city = City::find($id);
        $city->update($request->all());
        return $city;
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return City::destroy($id);
    }
    
    /**
     * Remove everything from the table.
     *
     * @return \Illuminate\Http\Response
     */
    public function truncate()
    {
        return City::truncate();
    }
    
    /**
     * Restarts the table's sequence.
     *
     * @return \Illuminate\Http\Response
     */
    public function restart()
    {
        return DB::statement("ALTER SEQUENCE cities_id_seq RESTART;");
    }
    
    /**
     * Search for a name.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function getIdByName($name)
    {
        return City::where('name', $name)->value('id');
    }
    
}
