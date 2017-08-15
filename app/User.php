<?php

namespace App;

// use Illuminate\Notifications\Notifiable;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;


//class User extends Authenticatable
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

  use Authenticatable, CanResetPassword;
  /**
  * The database table used by the model.
  *
  * @var string
  */
  protected $table = 'users';
  /**
  * The attributes excluded from the model's JSON form.
  *
  * @var array
  */
  protected $hidden = array('password');
  protected $fillable = array('name', 'email', 'password');
  protected $visible = ['id','email','name'];
  /**
  * Get the unique identifier for the user.
  *
  * @return mixed
  */
  public function getAuthIdentifier()
  {
    return $this->getKey();
  }
  /**
  * Get the password for the user.
  *
  * @return string
  */
  public function getAuthPassword()
  {
    return $this->password;
  }
  /**
  * Passwords should always be hashed
  * @param $value
  */
  public function setPasswordAttribute($value){
    $this->attributes['password'] = Hash::make($value);
  }

}
