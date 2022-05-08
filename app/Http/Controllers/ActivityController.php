<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Activity::all();
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
        
        return Activity::create($request->all());
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Activity::find($id);
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
        $activity = Activity::find($id);
        $activity->update($request->all());
        return $activity;
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Activity::destroy($id);
    }
    
    /**
     * Remove everything from the table.
     *
     * @return \Illuminate\Http\Response
     */
    public function truncate()
    {
        return Activity::truncate();
    }
    
    /**
     * Restarts the table's sequence.
     *
     * @return \Illuminate\Http\Response
     */
    public function restart()
    {
        return DB::statement("ALTER SEQUENCE activities_id_seq RESTART;");
    }
    
    /**
     * Search for a name.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function getIdByName($name)
    {
        return Activity::where('name', $name)->value('id');
    }
    
    public function countries()
    {
        return $this->hasMany(Activity::class, 'id');
    }
}
