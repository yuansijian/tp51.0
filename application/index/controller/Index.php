<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Model;
use think\Cookie;

class Index extends  Controller
{
    //主页显示
    public function index()
    {
        $name = Cookie::get('name');
        $data = Db::query("select * from home");

        $this->assign('data', $data);
        $this->assign('name', $name);

        return view();
    }

    //板块显示
    public function board($id)
    {
        $username = Cookie::get('name');
        $this->assign('name', $username);

        $name = Db::query("select board from home where id=?", [$id]);
//        dump($name[0]['board']);
//        $data = Db::query("select * from ?", [$name[0]['board']]);
        $data = Db::table($name[0]['board'])->select();
//        dump($data);



        $this->assign('board_name', $name[0]['board']);
        $this->assign('data', $data);
//        dump($data);

        return $this->fetch();
    }

    //新话题编辑界面
    public function newTopic($name){
        $username = Cookie::get('name');
        $this->assign('name', $username);

        $id = Db::query("select id from home where board=?", [$name]);
//        dump($id);

        $this->assign('id', $id[0]['id']);
        $this->assign('newName', $name);
        return $this->fetch();
    }
    //新话题存储进数据库
    public function insertDatabase($boardName){
        $username = Cookie::get('name');
        $this->assign('name', $username);

        //接受全部post数据
        $data = input('post.');
        $time = date('Y-m-d H:i:s');
        $data["create_time"] = $time;
        $data["user"] = $username;
//        dump($data);
//        dump($boardName);
        //插入数据库

        $temp['topic'] = $data['topic'];
        $temp['start'] = $username;

        //判断话题是否有重复
        $flag = Db::table($boardName)->where('topic', $temp['topic'])->value('topic');
        if($flag==null){
            //创建存储的回复表
            $tableName = $boardName."_".$temp['topic']."_reply";
            $sql = "create table if not exists $tableName(id int(11) auto_increment,
                                                      user varchar(45) not null ,
                                                      time timestamp not null default current_timestamp,
                                                      ran int(11) not null,
                                                      content text not null,
                                                      primary key (id))";

            $code3 = Db::execute($sql);

            //创建浏览表
            $tableName = $boardName."_".$temp['topic']."_view";
            $sql = "create table if not exists $tableName(id int(11) auto_increment,
                                                      user varchar(45) not null,
                                                      primary key (id))";
            $code4 = Db::execute($sql);
        }
        else{
            $this->error('话题重复');
        }


//        $code1 = Db::execute("insert into post value (null, :topic, :content, :create_time, :users)", $data);
        $code1 = Db::table('post')->insert($data);

        $code2 = Db::table($boardName)->insert($temp);

        if($code2 && $code1){
            $id = Db::table('home')->where('board', $boardName)->value('id');
//            $tableName = Db::table('home')->select();
            $count = Db::table($boardName)->count('topic');
//            $count++;
            Db::table('home')->where('board', $boardName)->update(['topic'=>$count]);
            $this->success("发表成功", "/board/{$id}");
        }
        else{
            $this->error("发表失败");
        }
    }


    //帖子内容  表创建问题
    public function content($name, $topic){
        //查询帖子数据
        $data = Db::query("select * from post where topic = ?", [$topic]);
        $table_name = $name."_".$topic."_"."reply";
//        dump($table_name);
        $reply = Db::table($table_name)->select();
//        dump($reply);
        $start = Db::table($name)->where('topic', $topic)->value('start');
//        dump($data);
//        dump($name);
        $username = Cookie::get('name');

        //统计查看人数
        $table_view = $name."_".$topic."_"."view";
        $user = Db::query("select distinct user from $table_view");
        $len = Db::table($table_view)->count('user');
//        dump($table_name);
//        dump($user);
//        dump($username);
//        dump($len);
//        dump($table_view);

        $flag = false;
        for($i=0; $i<$len; $i++){
//            dump($user[$i]['user']);
            if($user[$i]['user'] == $username){
                $flag = true;
                break;
            }
        }
//        dump($flag);
        if($flag){

        }
        else{
            Db::startTrans();
            try{
                $code1 = Db::table($table_view)->insert(['user'=>$username]);
                $count = Db::table($table_view)->count('user');
//            dump($count);
//            $count++;
                $code = Db::table($name)->where('topic', $topic)->update(['view'=>$count]);

                Db::commit();

                if(!$code && $code1){
                    $this->error("error 未知");
                }
            }
            catch (\ Exception $e){
                Db::rollback();
            }
        }

        $id = Db::table('home')->where('board', $name)->value('id');
        $this->assign('data', $data[0]);
        $this->assign('name', $username);
        $this->assign('topic_name', $name);
        $this->assign('id', $id);
        $this->assign('start', $start);
        $this->assign('reply', $reply);


        return $this->fetch();
    }
    //回复功能
    public function reply($name, $topic){

        $username = Cookie::get('name');
        $this->assign('name', $username);

        $id = Db::table('home')->where('board', $name)->value('id');
        $this->assign('name', $username);
        $this->assign('topic_name', $name);
        $this->assign('small_topic', $topic);
        $this->assign('id', $id);


        return $this->fetch();
    }
    //回复内容插入数据库
    public function replyInsert(){
        //获取回复内容
        $data = input('post.');

        if($data['text'] == null){
            $this->error('回复不能为空');
        }

//        dump($data);
        //拼凑表名
        $tableName = $data['board']."_".$data['topic']."_reply";
        $board = $data['board'];
        $topic = $data['topic'];
//        dump($tableName);

        //获取当前用户
        $name = Cookie::get('name');
        $this->assign('name', $name);
        //获取第几位评论数
        $rank = Db::table($tableName)->max('ran');
//        dump($rank);
        $rank++;

        $temp['user'] = $name;
        $temp['ran'] = $rank;
        $temp['content'] = $data['text'];
        $code = Db::table($tableName)->insert($temp);

        //更新最新回复时间
        $time = date('Y-m-d H:i:s');
        $code1 = Db::table($board)->where('topic', $topic)->update(['last_update'=>$time]);

        //更新回复数
        $replies = Db::table($board)->where('topic', $topic)->value('replies');
        $replies++;
        $code2 = Db::table($board)->where('topic', $topic)->update(['replies'=>$replies]);

        if($code && $code1 && $code2){
            $this->success('评论成功', "/content/{$board}/{$topic}");
        }
        else{
            $this->error('评论失败');
        }
    }
    //重新编辑
    public function replyRedict($name, $topic, $ran){
        $username = Cookie::get('name');
        $this->assign('name', $username);

        $id = Db::table('home')->where('board', $name)->value('id');
        $this->assign('name', $username);
        $this->assign('topic_name', $name);
        $this->assign('small_topic', $topic);
        $this->assign('id', $id);
        $this->assign('ran', $ran);

        return $this->fetch();
    }
    //将重新编辑的插入数据库
    public function replyUpdate(){
        $data = input('post.');

        $name = Cookie::get('name');
        $this->assign('name', $name);
        $board = $data['board'];
        $topic = $data['topic'];

//        dump($data);

        $tableName = $data['board']."_".$data['topic']."_reply";
        $code = Db::table($tableName)->where('ran', $data['ran'])->update(['content'=>$data['text']]);

        if($code){
            $this->success('更新成功', "/content/{$board}/{$topic}");
        }
        else{
            $this->error('更新失败');
        }
    }

}
