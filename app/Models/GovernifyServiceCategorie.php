<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GovernifyServiceCategorie extends Model
{
    use HasFactory;

    protected $table = 'governify_service_categories';

    protected $fillable = ['icon', 'title', 'subtitle', 'description', 'created_at', 'updated_at', 'deleted_at', 'service_categories_index'];

    public function serviceRequest()
    {
        return $this->hasMany(GovernifyServiceRequest::class);
    }

    public function service_category_request()
    {
        return $this->hasMany(GovernifyServiceRequest::class, 'service_categorie_id');
    }

    public function requestForm()
    {
        return $this->belongsTo(GovernifyServiceRequestForms::class, 'form');
    }

    public static function insertTableData(string $table_name, array $inputData)
    {
        if (!empty($table_name) && !empty($inputData)) {
            $insertID = DB::table($table_name)->insertGetId($inputData);
            if ($insertID) {
                $data = array('id' => $insertID);
                return array("status" => "success", "data" => $data);
            }
            return array("status" => "error");
        }
        return array("status" => "empty");
    }

    public static function updateTableData(string $table_name, array $params, array $data)
    {
        if (!empty($params) && !empty($data)) {
            $updatedData = DB::table($table_name)
                ->where($params)->update($data);
            if ($updatedData > 0) {
                return array("status" => "success");
            } else {
                return array("status" => "error");
            }
        }
        return array("status" => "empty");
    }

    public static function getTableData(string $table_name, array $params = array())
    {
        if (!empty($params['id'])) {
            $tableDataInDb = DB::table($table_name)
                ->where($params)
                ->get()->toArray();
            return $tableDataInDb;
        } else {
            $tableDataInDb = DB::table($table_name)
                ->get()->toArray();
            return $tableDataInDb;
        }
    }

    public static function getData(string $table_name, array $params = array()) {
        return DB::table( $table_name )->where($params)->get()->toArray();
    }

    public function serviceRequests()
    {
        return $this->hasMany(GovernifyServiceRequest::class, 'service_categorie_id');
    }
}
