<?php
namespace Home\Model;
use Think\Model;
use Think\Page as Page;

class BaseModel extends Model{
    protected static $_instance = array();

    public static function getInstance() {
        $class = get_called_class();
        if(!isset(self::$_instance[$class])) {
            self::$_instance[$class] = new $class();
        }
        return self::$_instance[$class];
    }

    public function getList($where = array(),$order='id desc',$limit=20,$field=true) {
        $page_num  = isset($_GET['p']) ? intval($_GET['p']) : 1;
        $data = $this->field($field)->where($where)->order($order)->page($page_num . ',' . $limit)->select();
        if($data === false) {
            \Lib\Log::error('内容获取失败；params:' . json_encode(func_get_args()) . ' ; SQL:' .$this->getLastSql());
        }
        $count = $this->where($where)->count();
        if($count === false) {
            \Lib\Log::error('内容获取失败; params: '.json_encode(func_get_args()) .';SQL: ' .$this->getLastSql());
        }
        $page = new Page($count,$limit);
        $pages  = $page->show();
        return array("data" => $data, "page" => $pages, 'count' => $count, 'page_total' => ceil($count/$limit), 'cur_page' => $page_num);
        }


}