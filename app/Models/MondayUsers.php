<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MondayUsers extends Model
{
    use HasFactory;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'id', 'name', 'company_name', 'phone', 'email', 'updated_at', 'created_at', 'board_id'
    ];

    /**
    * The attributes excluded from the model's JSON form.
    *
    * @var array
    */
    protected $hidden = [
        'password',
    ];


    public static function createUser( array $data ){
        $if_exists = DB::table( 'monday_users' )
        ->where( 'email', $data[ 'email' ] )
        ->exists();
        if (!$if_exists) {
            $insertID = DB::table( 'monday_users' )->insertGetId( $data );
            if ($insertID) {
                return array( "status" => "success" );
            }
            return array( "status" => "error" );
        }
        return array( "status" => "already" );
    }

    public static function loginUser( array $params ) {
        $if_exists = DB::table( 'monday_users' )
        ->where(array('email' => $params[ 'email' ]))->first();
        if( !empty( $if_exists ) ) {
            if( Hash::check($params[ 'password' ], $if_exists->password) ) {
                if ($if_exists->status == '1') {
                    $data = array( 'id' => $if_exists->id, 'user_data' =>  $if_exists);
                    return array( 'status' => 'success', 'data' => $data );
                }
                return array( 'status' => 'not_verified' );
            }
            return array( 'status' => 'wrong_pass' );
        }
        return array( 'status' => 'not_found' );

        // if( !empty( $if_exists ) ) {
        //     if( $params[ 'password' ] == $if_exists->password ) {
        //         if ($if_exists->status == '1') {
        //             $data = array( 'id' => $if_exists->id );
        //             return array( 'status' => 'success', 'data' => $data );
        //         }
        //         return array( 'status' => 'not_verified' );
        //     }
        //     return array( 'status' => 'wrong_pass' );
        // }
        // return array( 'status' => 'not_found' );
    }

    public function roles()
    {
        return $this->hasOne(Role::class,'id', 'role');
    }

    public static function getUser( array $params ) {
        if (!empty($params)) {
            return DB::table( 'monday_users' )->where($params)->first();
        }
        return false;
    }

    public static function setUser( array $params, array $data ) {
        if (!empty($params) && !empty($data)) {
            return DB::table( 'monday_users' )->where($params)->update( $data );
        }
        return false;
    }
}
