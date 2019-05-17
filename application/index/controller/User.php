<?php
/**
 * Created by PhpStorm.
 * User: protecting
 * Date: 19-5-15
 * Time: 上午11:52
 */

namespace app\index\controller;

use think\Controller;
use think\Cookie;
use think\Db;

class User extends Controller
{
    //Myaccount界面
    public function index(){
        $name = Cookie::get('name');
        $email = Db::table('user')->where('name', $name)->value('email');

        $this->assign('email', $email);
        $this->assign('name', $name);

        return $this->fetch();
    }

    //更新用户名  用户民合法化未完成
    public function update()
    {
        //接收来自user.index的post数据
        $data = input('post.');
//        dump($data);
        $temp = Db::table('user')->where('name', $data['last_name'])->select();

//        dump($temp);
        if($temp == null){
            $code = Db::table('user')->where('name', $data['first_name'])->update(['name'=>$data['last_name'], 'email'=>$data['email']]);

            if($code){
                Cookie::delete('name');
                Cookie::set('name', $data['last_name']);
                $this->assign('name', $data['last_name']);
                $this->success('用户名已更改', '/');
            }
            else{
                $this->error('用户名更改失败');
            }
        }
        else{
            $this->error('新用户名已存在');
        }
    }

    //更改密码界面
    public function changePassword(){
        $name = Cookie::get('name');
        $this->assign('name', $name);

        return $this->fetch();
    }
    //更新密码  密码验证处理
    public function updatePassword(){
        $data = input('post.');

        if($data['new_password1'] != $data['new_password2']){
            $this->error('两次密码不一致');
        }

        //加密密码存入数据库
        $password = password_hash($data['new_password1'], PASSWORD_DEFAULT);

//        dump($data);
        //获取用户名
        $name = Cookie::get('name');

        //获取旧密码
        $old_password = Db::table('user')->where('name', $name)->value('password');

        if (!password_verify($data['old_password'], $old_password)){
            $this->error('请检测旧密码是否正确');
        }

        $code = Db::table('user')->where('name', $name)->update(['password'=>$password]);

        if($code){
            $this->success('密码更改成功', '/');
        }
        else{
            $this->error('密码更改失败');
        }
    }

    //修改管理员密码
    public function managerUpdatePassword(){
        $data = input('post.');
//        dump($data);
        $name = Cookie::get('name');
//        dump($name);
        if($data['newPassword1'] != $data['newPassword2']){
            $this->error('两次新密码不一致');
        }
        else{
            $oldPassword = Db::table('manager')->where('manager', $name)->value('password');

            if($data['oldPassword'] == $oldPassword){
               $code = Db::table('manager')->where('manager', $name)->update(['password'=>$data['newPassword1']]);

               if($code){
                   $this->success('密码更新成功', '/index');
               }
               else{
                   $this->error('密码更新失败');
               }
            }
            else{
                $this->error('旧密码错误');
            }
        }
    }

    //添加管理员
    public function addManager(){
        $data = input('post.');
//        dump($data);
        $code = Db::table('manager')->insert($data);

        if($code){
            $this->success('添加成功');
        }
        else{
            $this->error('添加失败');
        }
    }
}