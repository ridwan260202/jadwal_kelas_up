<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\QueryException;

class DosenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (request()->ajax() || $request->input('ajax')) {
            $query = Dosen::with(['dataProdi:id,nama_prodi']);

            if ($request->has('prodi') && $request->input('prodi')) {
                $query->where('id_prodi', $request->input('prodi'));
            }

            $data = $query->get();
            $dataTable = DataTables::of($data);
            $dataTable->addIndexColumn()
            ->addColumn('account', function ($row) {
                return $this->checkAccount($row->email);
            })
            ->rawColumns(['account']);
            return $dataTable->make(true);
        }

        return view('dosen.index');
    }
    private function checkAccount($email){
        
        $accountExist = User::where('email',$email)->exists();

        return $accountExist;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $insert = Dosen::create($request->all());
        $data = [
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $request->all()
        ];
        return response()->json($data, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Dosen  $dosen
     * @return \Illuminate\Http\Response
     */
    public function show(Dosen $dosen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Dosen  $dosen
     * @return \Illuminate\Http\Response
     */
    public function edit(Dosen $dosen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dosen  $dosen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dosen $dosen)
    {
        $dosen->fill($request->all())->save();
        $data = [
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $request->all()
        ];
        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dosen  $dosen
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dosen $dosen)
    {
        try {
            $dosen->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Data Berhasil Di hapus',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Tidak Dapat dihapus karena terhubung ke data penjadwalan',
            ], 422);
        }
    }
}
