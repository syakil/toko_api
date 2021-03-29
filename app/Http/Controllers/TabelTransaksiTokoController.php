<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class TabelTransaksiTokoController extends Controller
{
    public function index(){

        $data = DB::connection('mysql2')->select('SELECT * FROM `tabel_transaksi_toko` LIMIT 5');

        dd($data);

    }
}
