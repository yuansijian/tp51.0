<?php
/**
 * Created by PhpStorm.
 * User: protecting
 * Date: 19-5-13
 * Time: 下午8:54
 */

namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Cookie;

class Login extends Controller
{
    //登录界面
    public function index(){
        //有Cookie清初Cookie
        if(Cookie::has('name')){
            Cookie::clear('name');
        }

        return $this->fetch();
    }

    //登录验证
    public function checkUser(){
        $data = input('post.');
//        dump($data);

        if(in_array('admin', $data)) {
            $temp = Db::table('manager')->where('manager', $data['username'])->select();
//            dump($temp);
            if($temp == null || $temp[0]['manager'] == null || $temp[0]['password'] == null){
                $this->error('登录失败');
            }
            if($data['username']==$temp[0]['manager'] && $data['password']==$temp[0]['password']){
                //设置用户Cookie
                Cookie::set('name', $data['username'], 3600);
                $this->success('登录成功', '/index');
            }
            else{
//            dump($temp[0]['password']);
//            dump($data['password']);
                $this->error('登录失败');
            }
        }
        else{
            $temp = Db::table('user')->where('name',$data['username'])->select();

            if($temp == null || $temp[0]['name'] == null || $temp[0]['password'] == null){
                $this->error('登录失败');
            }
//        dump($temp[0]['password']);


//        $temp[0]['password'] = password_hash($temp[0]['password'], PASSWORD_DEFAULT);

            if($data['username']==$temp[0]['name'] && password_verify($data['password'], $temp[0]['password'])){
                //设置用户Cookie
                Cookie::set('name', $data['username'], 3600);
                $this->success('登录成功', '/');
            }
            else{
//            dump($temp[0]['password']);
//            dump($data['password']);
                $this->error('登录失败');
            }
        }
    }

    public function signUp(){
        return $this->fetch();
    }

    //用户信息插入数据库
    public function insertDatabase(){
        //接受来自注册页面的post数据
        $data = input('post.');
//        dump($data);
        //加密方式有待改进
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $isExist = Db::table('user')->where('name', $data['name'])->select();

        if($isExist != null){
            $this->error('用户名已被注册');
        }
        else{
            $code = Db::table('user')->insert($data);

            if($code){
                Cookie::set('name', $data['name'], 3600);
                $this->success('注册成功', '/');
            }
            else{
                $this->error('注册失败');
            }
        }

//        $flag = Db::table('user')->where('username', $data['username'])->value('username');
//
//        if($flag == null){
//            $code = Db::table('user')->insert($data);
//
//            if($code){
//                $this->success();
//            }
//        }
    }

    //忘记密码处理  有待完善
    public function forgetPassword(){
        return $this->fetch();
    }
}