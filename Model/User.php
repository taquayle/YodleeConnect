<?php
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
class User extends Model implements AuthenticatableContract, CanResetPasswordContract {
    use Authenticatable, CanResetPassword;
    protected $dates = ['deleted_at'];
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
    protected $fillable = array('username', 'email');
    protected $visible = ['id','email','username','initials', 'last_login'];
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
        //$this->attributes['password'] = Hash::make($value);
        $this->attributes['password'] = $value;
    }

    public function lastLoggedOn(){
        return $this->hasMany('UserLoginDetail')->
            select('user_id','logged_on')->
            orderBy('logged_on','desc')->limit(1);
    }
    /**
     * @param $email
     * @param $roleId
     */
    public static function createNew($email)
    {
        $user = new static(compact('email'));
    }
}
