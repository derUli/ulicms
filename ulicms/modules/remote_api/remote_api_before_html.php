<?php



if(isModuleInstalled("IXR_Library") and getconfig("remote_api_enabled")
   and isset($_GET["remote"])){
   include_once(getModulePath("IXR_Library").'IXR_Library.php');

function register_xmlrpc_call($name, $callback){

        global $xmlrpc_calls;
        $xmlrpc_calls[$name] = $callback;
}


class SimpleServer extends IXR_Server {
    function SimpleServer() {
        $this->user = null;
        global $xmlrpc_calls;
        $xmlrpc_calls = array(
            'demo.sayHello' => 'this:sayHello',
            'demo.addTwoNumbers' => 'this:addTwoNumbers',
            'demo.fortune' => 'this:fortune',
            'version.release' => 'this:getRelease',
            'version.internal' => 'this:getInternalVersion',
            'version.development' => 'this:isDevelopmentVersion',
            'auth.login' => 'this:checkLogin',
            'cache.clear' => 'this:clear_cache',
            'users.onlinenow' => 'this:onlineUsers',
            'modules.list' => 'this:listModules',
            'properties.list' => 'this:propertyList',
            'languages.list' => 'this:languagesList',
            'menus.list' => 'this:menusList',
            'pages.list' => "this:listPages"
        );
        
        // Hook fürs hinzufügen weiter API Calls
        add_hook("xmlrpc_calls");
        
        $this->IXR_Server($xmlrpc_calls);
    }
    
    function fortune(){
      if(!isModuleInstalled("fortune"))    
          return null;
          
      if(!function_exists("getRandomFortune"))
          include_once getModulePath("fortune")."fortune_lib.php";
      
      return getRandomFortune();        
    
    }    
    
    function sayHello($args) {
        return 'Hello World!';
    }
    
    function onlineUsers($args){
           if(!$this->checkLogin(array($args[0], $args[1])))
          return null;
       
          return array_values(getOnlineUsers());
    }
    
    function listModules($args){
       if(!$this->checkLogin(array($args[0], $args[1])))
          return null;
       
          return array_values(getAllModules());
    }
    
   function listPages($args){
       if(!$this->checkLogin(array($args[0], $args[1])))
          return null;
       
          return array_values(getAllSystemNames());
    }
    
    function languagesList($args){
           if(!$this->checkLogin(array($args[0], $args[1])))
             return null;
       
          return array_values(getAllLanguages());
    }
    
    function menusList($args){
         if(!$this->checkLogin(array($args[0], $args[1])))
            return null;
          return array_values(getAllMenus());
    
    }
    
    function clear_cache($args){
       if(!$this->checkLogin(array($args[0], $args[1])))
          return false;
          
       clearCache();    
       return true;
    }

    function getRelease(){
       $version = new ulicms_version();
       return $version->getVersion();    
    }
    
    function propertyList($args){
           if(!$this->checkLogin(array($args[0], $args[1])))
              return null;

          
          $stat = array();
          $stat["page_count"] = count(getAllSystemNames());
          $stat["user_count"] = count(getUsers());
          $stat["homepage_title"] = getconfig("homepage_title");
          $stat["motto"] = getconfig("motto");
          $stat["owner"] = getconfig("homepage_owner");
          $stat["email"] = getconfig("email");
          $stat["meta_keywords"] = getconfig("meta_keywords");
          $stat["meta_description"] = getconfig("meta_description");
          $stat["timezone"] = getconfig("timezone");
          $stat["frontpage"] = getconfig("frontpage");
          
          
          if(isModuleInstalled("blog")){
             $query_articles = mysql_query("select * from ".tbname("blog"));
             $stat["blog_entry_count"] = mysql_num_rows($query_articles);
             
             $query_comments = mysql_query("select * from ".tbname("blog_comments"));
             $stat["blog_comment_count"] = mysql_num_rows($query_comments);
          }
          
          ksort($stat);
          return $stat;
    }
    
    
     function isDevelopmentVersion(){
       $version = new ulicms_version();
       return $version->getDevelopmentVersion();   
    }

    function getInternalVersion(){
       $version = new ulicms_version();
       return $version->getInternalVersion();
    }    
    
    function checkLogin($args){
        $data = validate_login($args[0], $args[1]);
        if($data){
          $this->user = $data;
          return true;
        } else{
          $this->user = null;
          return false;        
        }
    
    }
    
    function addTwoNumbers($args) {
        $number1 = $args[0];
        $number2 = $args[1];
        return $number1 + $number2;
    }
}

$server = new SimpleServer();
   
   
} else if(isModuleInstalled("IXR_Library") and !getconfig("remote_api_enabled")
   and isset($_GET["remote"])){
   header("HTTP/1.0 503 Service Temporarily Unavailable");
   header("Content-Type: text/plain;");
   die("Remote API is disabled.");
}

   
?>
