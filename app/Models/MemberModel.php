<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $table = 'members';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['username', 'email', 'password_hash', 'full_name', 'is_active'];
    protected $useTimestamps = true;
}
