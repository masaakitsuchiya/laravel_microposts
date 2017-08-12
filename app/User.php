<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;



class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    public function followings()//userがフォローしているuser
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();    
    }
    
    public function followers()//userをフォローしているuser
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    
    public function follow($userId)//あるuserをfollowする
    {
        //すでにfollowしているかの確認
        $exist = $this->is_following($userId);
        //自分自身ではないかの確認
        $its_me = $this->id == $userId;
        
        if($exist || $its_me){//follow_idに存在しているかまたは、自分自身ではある
            //なにもしない
            return false; 
        }else{
            $this->followings()->attach($userId);//userがfollowしているユーザーに該当のuseIdのレコードを追加する
            return true;
        }
    }
    
    
    public function unfollow($userId)//あるuserをunfollowする
    {
        //すでにフォローしているかの確認
        $exits = $this->is_following($userId);
        //自分自身を指しているかの確認
        $its_me = $this->id == $userId;
        
        if($exits && !$its_me)//もしすでにフォローしていてかつそのuserが自分自身でない場合、
        {
            $this->followings()->detach($userId);//該当のuserIdをレコードから削除する
            return true;
        }else{
            return false;
        }
    }
    
    public function is_following($userId)//すでにfollowしているリストにあるかの確認
    {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()->lists('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
    public function favorite_microposts()
    {
        return $this->belongsToMany(Micropost::class, 'favorite_micropost', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    public function like($micropostId)
    {
        $exist = $this->liked($micropostId);
        if($exist){
            return false;
        } else {
            $this->favorite_microposts()->attach($micropostId);
            return true;
        }
        
    }
    public function unlike($micropostId)
    {
         $exist = $this->liked($micropostId);
         if($exist){
             $this->favorite_microposts()->detach($micropostId);
             return true;
         } else {
             return false;
         }
    }
    
    
    public function liked($micropostId)
    {
        return $this->favorite_microposts()->where('micropost_id', $micropostId)->exists();
    }
}
