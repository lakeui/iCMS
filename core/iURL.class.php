<?php
/**
* iPHP - i PHP Framework
* Copyright (c) 2012 iiiphp.com. All rights reserved.
*
* @author coolmoo <iiiphp@qq.com>
* @site http://www.iiiphp.com
* @licence http://www.iiiphp.com/license
* @version 1.0.1
* @package iURL
* @$Id: iURL.class.php 2408 2014-04-30 18:58:23Z coolmoo $
*/
class iURL {
    public static $config   = null;
    public static $uriArray = null;
	public static function init($config){
        $config['router']= array(
            'http'     => array('rule'=>'0','PRI'=>''),
            'category' => array('rule'=>'1','PRI'=>'cid'),
            'article'  => array('rule'=>'2','PRI'=>'id'),
            'software' => array('rule'=>'2','PRI'=>'id'),
            'tag'      => array('rule'=>'3','PRI'=>'id'),
        );
        self::$config = $config;
        //var_dump($config);
	}
    private static function CPDIR($cid="0") {
        $C    = iCache::get('iCMS/category/'.$cid);
        $C['rootid'] && $dir.=self::CPDIR($C['rootid']);
        $dir.='/'.$C['dir'];
        return $dir;
    }

    private static function domain($cid="0",$akey='dir') {
        $ii       = new stdClass();
        $C        = iCache::get('iCMS/category/'.$cid);
        $rootid   = $C['rootid'];
        $ii->sdir = $C[$akey];
        if($rootid && empty($C['domain'])) {
            $dm         = self::domain($rootid);
            $ii->pd     = $dm->pd;
            $ii->domain = $dm->domain;
            $ii->pdir   = $dm->pdir.'/'.$C[$akey];
            $ii->dmpath = $dm->dmpath.'/'.$C[$akey];
        }else {
            $ii->pd     = $ii->pdir   = $ii->sdir;
            $ii->dmpath = $ii->domain = $C['domain']?('http://'.ltrim($C['domain'],'http://')):'';
        }
        return $ii;
    }

    public static function rule($matches) {
    	$b	= $matches[1];
    	list($a,$c,$tc) = self::$uriArray;

        switch($b) {
            case 'ID':		$e = $a['id'];break;
            case '0xID':	$e = sprintf("%08s",$a['id']);break;
            case '0x3ID':	$e = substr(sprintf("%08s",$a['id']), 0, 4);break;
            case '0x3,2ID':	$e = substr(sprintf("%08s",$a['id']), 4, 2);break;
            case 'MD5':     $e = md5($c['id']);$e=substr(md5($e),8,16);break;

            case 'CID':     $e = $c['cid'];break;
            case '0xCID':   $e = sprintf("%08s",$c['cid']);break;
            case 'CDIR':    $e = $c['dir'];break;
            case 'CPDIR':   $e = substr(self::CPDIR($c['rootid']),1);break;

            case 'TIME':	$e = $a['pubdate'];break;
            case 'YY':		$e = get_date($a['pubdate'],'y');break;
            case 'YYYY':	$e = get_date($a['pubdate'],'Y');break;
            case 'M':		$e = get_date($a['pubdate'],'n');break;
            case 'MM':		$e = get_date($a['pubdate'],'m');break;
            case 'D':		$e = get_date($a['pubdate'],'j');break;
            case 'DD':		$e = get_date($a['pubdate'],'d');break;

            case 'NAME':    $e = urlencode(iS::escapeStr($a['name']));break;
            case 'ZH_CN':	$e = $a['name'];break;
            case 'TKEY':    $e = $a['tkey'];break;

            case 'TCID':	$e = $tc['tcid'];break;
            case 'TCDIR':	$e = $tc['dir'];break;

            case 'EXT':		$e = $c['htmlext']?$c['htmlext']:self::$config['html_ext'];break;
            case 'TITLE':   $e = urlencode(iS::escapeStr($a['title']));break;
            case 'LINK':    $e = $a['clink'];break;
            case 'P':       $e = '{P}';break;
        }
        return $e;
    }
    public static function get($uri,$a=array()) {
        $i        = new stdClass();
        $sURL     = self::$config['URL'];
        $html_dir = self::$config['html_dir'];
        $router   = self::$config['router'];
        $category = array();
        $array    = $a;
        $PRI      = $router[$uri]['PRI'];
        $rule     = $router[$uri]['rule'];
        switch($rule) {
            case '0':
                $i->href = $array['url'];
                $url     = $array['urlRule'];
                break;
            case '1':
                $category = $array;
                $i->href  = $category['url'];
                $url      = $category['mode']?$category['categoryRule']:'{PHP}';
                ($category['password'] && $category['mode']=="1") && $url = '{PHP}';
                if($url=='{PHP}'){
                    $document_uri = $uri.'.php?';
                    $category['categoryURI'] && $document_uri = $category['categoryURI'].'.php?';
                    $document_uri.= $PRI.'='.$category[$PRI];
                }
                break;
            case '2':
                $array    = (array)$a[0];
                $category = (array)$a[1];
                $i->href  = $array['url'];
                $url      = $category['mode']?$category['contentRule']:'{PHP}';
                ($category['password'] && $category['mode']=="1") && $url = '{PHP}';
                if($url=='{PHP}'){
                    $document_uri = $uri.'.php?';
                    $document_uri.= $PRI.'='.$array[$PRI];
                    $i->pageurl = $document_uri.'&p={P}';
                    strpos($i->pageurl,'http://')===false && $i->pageurl = rtrim($sURL,'/').'/'.$i->pageurl;
                }
                break;
            case '3':
                $array     = (array)$a[0];
                $category  = (array)$a[1];
                $_category = (array)$a[2];
                $html_dir  = self::$config['tag_dir'];
                $sURL      = self::$config['tag_url'];
                $i->href   = $array['url'];
                //$a       = array_merge_recursive((array)$a[0],$category);
                $url       = $category['urlRule'];
                $_category['urlRule'] && $url = $_category['urlRule'];
                $url OR $url = self::$config['tag_rule'];
                if($url=='{PHP}'){
                    $document_uri = $uri.'.php?';
                    $document_uri.= $PRI.'='.$array[$PRI];
                }
                break;
             default:
                $url = $array['urlRule'];
        }
        if($url=='{PHP}'){
            strpos($document_uri,'http://')===false && $document_uri = rtrim($sURL,'/').'/'.$document_uri;
            $i->href = $document_uri;
        }
        if($i->href) return $i;

        if(strpos($url,'{PHP}')===false) {
        	self::$uriArray	= array($array,$category,(array)$_category);

        	strpos($url,'{')===false OR $url = preg_replace_callback ("/\{(.*?)\}/",'__iurl_rule__',$url);

            $i->path = rtrim(iFS::path(iPATH.$html_dir.$url),'/') ;
            if(strpos($html_dir,'..')===false) {
                $i->href = rtrim($sURL,'/').'/'.ltrim(iFS::path($html_dir.$url),'/') ;
            }else{
                $i->href = rtrim($sURL,'/').'/'.ltrim(iFS::path($url),'/') ;
            }
			$pathA = pathinfo($i->path);

//            if(in_array($uri,array('article','content'))) {
//                $i->path    = FS::path($Curl->dmdir.'/'.$url);
//                $i->href    = FS::path($Curl->domain.'/'.$url);
//            }
            $i->hdir = pathinfo($i->href,PATHINFO_DIRNAME);
            $i->dir  = $pathA['dirname'];
            $i->file = $pathA['basename'];
            $i->name = $pathA['filename'];
            $i->ext  = '.'.$pathA['extension'];
            $i->name OR $i->name = $i->file;
//var_dump($GLOBALS['page']);
//var_dump($i);
//var_dump($pathA);

            if(empty($i->file)||substr($url,-1)=='/'||empty($pathA['extension'])) {
                $i->name = 'index';
                $i->ext  = self::$config['html_ext'];
				$category['htmlext'] && $i->ext = $category['htmlext'];
                $i->file = $i->name.$i->ext;
                $i->path = $i->path.'/'.$i->file;
                $i->dir  = dirname($i->path);
                $i->hdir = dirname($i->href.'/'.$i->file);
            }
//var_dump($i);
            if(strpos($i->file,'{P}')===false) {
                $i->pfile = $i->name."_{P}".$i->ext;
			}else{
                $i->pfile = $i->file;
            }

	        if($rule=='0'||strpos($url,'http://')!==false){
                $hi          = new stdClass();
                $hi->href    = $url;
                $hi->ext     = $i->ext;
                $hi->pageurl = $hi->href.'/'.$i->pfile ;
	        	return $hi;
	        }
//var_dump($i);
//exit;
			if($rule=='1') {
                $m    = self::domain($array['cid']);
                if($m->domain) {
                    $i->href   = str_replace($i->hdir,$m->dmpath,$i->href);
                    $i->hdir   = $m->dmpath;
                    $__dir__   = $i->dir.'/'.$m->pdir;
                    $i->path   = str_replace($i->dir,$__dir__,$i->path);
                    $i->dir    = $__dir__;
                    $i->dmdir  = iFS::path(iPATH.$html_dir.'/'.$m->pd);
                    $bits      = parse_url($i->href);
                    $i->domain = $bits['scheme'].'://'.$bits['host'];
                }else {
                    $i->dmdir  = iFS::path(iPATH.$html_dir);
                    $i->domain = $sURL;
                }
		        if(strpos($array['domain'],'http://')!==false){
                    $i->href = $array['domain'];
		        }
            }
            $i->pageurl  = $i->hdir.'/'.$i->pfile ;
            $i->pagepath = $i->dir.'/'.$i->pfile;


           $i->href	= str_replace('{P}',1,$i->href);
           $i->path	= str_replace('{P}',1,$i->path);
           $i->file	= str_replace('{P}',1,$i->file);
           $i->name	= str_replace('{P}',1,$i->name);
//var_dump($i);
//exit;
        }
        return $i;
    }
}
function __iurl_rule__($a){
	return iURL::rule($a);
}
