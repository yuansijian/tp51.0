<?php
/**
 * Created by PhpStorm.
 * User: protecting
 * Date: 19-5-16
 * Time: 下午11:02
 */

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Cookie;

class Index extends Controller
{
    //管理后台主页
    public function index(){

        return $this->fetch();
    }

    //用户管理
    public function management(){
        $user = Db::table('user')->select();
        $this->assign('user', $user);
        $count = Db::table('user')->count('id');
        $this->assign('count', $count);

        return $this->fetch();
    }
    //用户检索功能
    public function searchUser(){
        $data = input('post.');
//        dump($data);

        $user = Db::table('user')->where('name', $data['username'])->select();
//        dump($user);

        $this->assign('user', $user);

        if($user != null){
            $count = 1;
            $this->assign('count', $count);
        }
        else{
            $count = 0;
            $this->assign('count', $count);
        }

        return $this->fetch();
    }

    //板块管理
    public function boards(){
        $data = Db::table('home')->select();
        $this->assign('data', $data);
        $count = Db::table('home')->count('id');
        $this->assign('count', $count);

        return $this->fetch();
    }

    //添加板块
    public function addBoard(){
        return $this->fetch();
    }
    //插入数据库
    public function insertBoard(){
        $data = input('post.');
//        dump($data);
        $code = Db::table('home')->insert($data);

        $tableName = $data['board'];

        $sql = "create table if not exists $tableName(id int(11) auto_increment,
                                                          topic varchar(45) not null,
                                                          start varchar(45) not null,
                                                          replies int(11) default 0,
                                                          view int(11) default 0,
                                                          last_update timestamp default current_timestamp,
                                                          primary key (id, topic))";

        Db::execute($sql);

        if($code){
            $this->success('添加成功', '/boards');
        }
        else{
            $this->error('添加失败');
        }
    }

    //搜索板块
    public function searchBoard(){
        $data = input('post.');
//        dump($data);

        $user = Db::table('home')->where('board', $data['content'])->select();
//        dump($user);

        $this->assign('user', $user);

        if($user != null){
            $count = 1;
            $this->assign('count', $count);
        }
        else{
            $count = 0;
            $this->assign('count', $count);
        }

        return $this->fetch();
    }

    //删除板块
    public function deleteBoard($id){
        $tableName = Db::table('home')->where('id', $id)->value('board');
        $code = Db::table('home')->where('id', $id)->delete();
        $sql = "drop table $tableName";
        Db::execute($sql);

        if($code){
            $this->success('删除成功', '/boards');
        }
        else{
            $this->error('删除失败');
        }
    }

    //管理板块
    public function manageBoard($id){

        $data = Db::table('home')->where('id', $id)->select();
//        dump($data);
        $this->assign('data', $data[0]);
        $this->assign('id', $id);

        return $this->fetch();
    }
    //更新板块
    public function updateBoard($id){
        $data = input('post.');
        dump($data);
//        dump($id);

        $old = Db::table('home')->where('id', $id)->value('board');
        if($old != $data['board']){
            $tableName = $data['board'];
//            dump($tableName);
//            dump($old);
            $sql = "alter table $old rename to $tableName";
            Db::execute($sql);
        }

        $code = Db::table('home')->where('id', $id)->update($data);

        if($code){
            $this->success('更新成功', '/boards');
        }
        else{
            $this->error('更新失败');
        }
    }

    //删除用户
    public function deleteUser($id){
        $username = Db::table('user')->where('id', $id)->value('name');
        $code = Db::table('user')->where('id', $id)->update(['name'=>'已注销']);

        if($code){
            $this->success('删除成功');
        }
        else{
            $this->error('删除失败');
        }
    }

    //添加管理员
    public function add(){
        return $this->fetch();
    }

//    //评论列表
//    public function comment(){
//        Db::startTrans();
//        try{
//            $board = Db::table('home')->value('board');
//            $post = Db::table('post')->value('topic');
//        }
//    }
}