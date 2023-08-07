<?php

namespace App\Models;

use App\Database\DatabaseConnector;
use Database\Factories\Company\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $connection = DatabaseConnector::DB_CONNECTION_ADMIN;
    protected $fillable = [
        'name',
        'db_name',
        'status',
        'subdomain',
        'db_password',
        'db_user',
        'owner_name',
        'owner_phone'
    ];

    protected static function newFactory(): CompanyFactory
    {
        return new CompanyFactory();
    }
}
