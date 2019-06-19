<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Tsop_report_files extends Model
{
    const UPDATED_AT = 'last_updated';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'report_Id', 'execution_type', 'sequence','async','file_name','file_name_mask','url','sp_needed','spid','file_type','embed_attachment','template_file','template_data',
        'last_updated','active','send_blank','script_path'
    ];

    protected $casts = [
        'date_created' => 'datetime',
        'last_updated' => 'datetime',
    ];    


}


