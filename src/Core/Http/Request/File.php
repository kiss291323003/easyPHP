<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/3/18
 * Time: 下午8:23
 */

namespace Core\Http\Request;


class File
{
    protected $currentField;
    protected $files;

    function __construct()
    {
        $this->files = $_FILES;
    }

    function select($filed){
        $this->currentField = $filed;
        return $this;
    }
    function name(){
        return $this->accessByKey("name");
    }
    function size(){
        return $this->accessByKey("size");
    }
    function tempName(){
        return $this->accessByKey("tmp_name");
    }
    function error(){
        return $this->accessByKey("error");
    }
    function fileExt(){
        return strtolower(strrchr($this->accessByKey("name"), '.'));
    }
    function type(){
        return $this->accessByKey("type");
    }
    function save($path,$name = null){
        if (!file_exists($path) && !mkdir($path,0755,1)) {
            trigger_error("create upload path : $path fail");
        }else{
            if($name === null){
                $name = $this->name();
            }
            $realPath = $path."/$name";
            if (!(move_uploaded_file($this->tempName(), $realPath) && file_exists($realPath))) { //移动失败
                trigger_error("save upload file : $realPath fail");
            }else{
                return $realPath;
            }
        }
    }

    function isExist(){
        if(isset($this->files[$this->currentField])){
            return $this->files[$this->currentField];
        }else{
            return false;
        }
    }

    function allFile(){
        return $this->files;
    }

    private function accessByKey($key){
        if(isset($this->files[$this->currentField][$key])){
            return $this->files[$this->currentField][$key];
        }else{
            return null;
        }
    }
}