<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

class LiffBindController extends Controller
{
    public function form() { return view('liff.liff-bind'); }

    public function submit(Request $r) {
        $data = $r->validate([
            'line_user_id' => 'required|string',
            'name'         => 'required|string|max:50',
            'phone'        => 'required|string|max:30',
        ]);

        $memberId = DB::table('members')->updateOrInsert(
            ['phone'=>$data['phone']],
            ['name'=>$data['name'], 'updated_at'=>now(), 'created_at'=>now()]
        ) ? DB::table('members')->where('phone',$data['phone'])->value('id') : null;

        DB::table('line_users')->updateOrInsert(
            ['line_user_id'=>$data['line_user_id']],
            ['member_id'=>$memberId, 'updated_at'=>now(), 'created_at'=>now()]
        );

        return response()->json(['ok'=>true]);
    }
}
