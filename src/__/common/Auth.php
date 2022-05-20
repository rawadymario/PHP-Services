<?php
	namespace RawadyMario\Classes\Common;

    class Auth {

        public static function SetAction($actionName="") {
			// $_SESSION[SESSION_NAME]["user_permission"][] = $actionName;
        }
        
        public static function ClearActions() {
			// unset($_SESSION[SESSION_NAME]["user_permission"]);
        }
        
        public static function Can($module, $action="any") {
            $can = true;

            // if (!IS_SUPER && !IS_DEV) {
            //     if (!is_array($actionName)) {
            //         $actionName = [$actionName];
            //     }
    
            //     foreach ($actionName AS $action) {
            //         if (!in_array($action, $_SESSION[SESSION_NAME]["user_permission"])) {
            //             $can = false;
            //         }
            //     }
            // }
            
            return $can;
        }
        
        public static function Deny() {
            redirect(getFullUrl(PAGE_UNAUTHORIZED));
        }

        public static function PagePrivilege($actionName) {
            if (!self::Can($actionName)) {
                self::Deny();
            }
        }
    }


?>