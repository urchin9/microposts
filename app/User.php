<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function microposts() {
        return $this->hasMany(Micropost::class);
    }
    
    public function followings() {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    public function followers() {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function favorite_posts() {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    public function follow($userId) {
        
        // すでにフォローしているか
        $exist = $this->is_following($userId);
        // 自分自身でないか確認する
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me) {
            // すでにフォローしていたら何もしない
            return false;
        } else {
            // 未フォローだったらフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId) {
        // すでにフォローしているか
        $exist = $this->is_following($userId);
        // 自分自身でないか
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) {
            // unfollow if following
            $this->followings()->detach($userId);
            return true;
        } else {
            // nothing to do if not following
            return false;
        }
    }
    
    public function is_following($userId) {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts() {
        $follow_user_ids = $this->followings()->pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
        public function favorite($micropostId) {
        $exist = $this->is_favorite($micropostId);

        if ($exist) {
            return false;
        } else {
            $this->favorite_posts()->attach($micropostId);
            return true;
        }
    }
    
    public function unfavorite($micropostId) {
        $exist = $this->is_favorite($micropostId);
        
        if ($exist) {
            $this->favorite_posts()->detach($micropostId);
            return true;
        } else {
            return false;
        }
    }
    
    
    public function is_favorite($micropostId) {
        return $this->favorite_posts()->where('micropost_id', $micropostId)->exists();
    }

}
