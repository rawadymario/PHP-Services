<?php
	namespace RawadyMario\Classes\Common;

    use RawadyMario\Classes\Core\Cookie;
    use RawadyMario\Classes\Database\Logs\UserLoginLog;
    use RawadyMario\Classes\Database\UserTokens;

    class AuthLogged {

        public static function IsLogged() {
			$isLogged = false;
            $cookie = self::GetCookie();
			
            if ($cookie != "") {
                $isLogged = true;
            }

			return $isLogged;
        }

        public static function SetLogged($token, $additionalTime="") {
            self::SetCookie($token, $additionalTime);
        }

        public static function GetLogged() {
            return self::GetCookie();
        }

        public static function Logout($saveLog=false, $tokenKey=null) {
            if ($saveLog) {
                UserLoginLog::logLogout(LOGGED_ID);
            }

            $activeToken = new UserTokens();
		    $activeToken->loadByToken($tokenKey);
            if ($activeToken->count > 0) {
                $activeToken->delete();
            }
			
            self::ClearCookie();
            Auth::ClearActions();

            return true;
		}
        

        private static function SetCookie($value="", $additionalTime="") {
            $time = time() + $additionalTime;

            $cookie = new Cookie("auth");
            $cookie->SetTime($time);
            $cookie->SetCookie($value);

            return true;
        }
        
        private static function GetCookie() {
            $cookie = new Cookie("auth");
            return $cookie->GetCookie();
        }
        
        private static function ClearCookie() {
            $cookie = new Cookie("auth");
            return $cookie->ClearCookie();
        }

    }


?>