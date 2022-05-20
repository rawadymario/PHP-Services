<?php
	namespace RawadyMario\Classes\Core;

    use RawadyMario\Classes\Database\User;
    use RawadyMario\Classes\Database\Store;
    use RawadyMario\Classes\Core\Notification\Manager\Email\PhpMailerManager;
    use RawadyMario\Classes\Core\Notification\Manager\WebNotification\WebNotificationManager;

    class NotificationManager {
        //Common Params
        public $response = [];

        //Email Parameters
        public $haveEmail = false;
        private $emailManager;
        
        //Web Notification Parameters
        public $haveWebNotification = false;
        private $webNotificationManager;
        
        //Mobile Notification Parameters
        public $haveMobileNotification = false;
        
        //SMS Parameters
        public $haveSms = false;

        
        public function __construct() {
            $this->emailManager = new PhpMailerManager();
            $this->webNotificationManager = new WebNotificationManager();
        }

        public function Send() {
            $retArr = [
                "status" => SUCCESS
            ];

            if ($this->haveEmail) {
                $retArr["data"]["email"] = $this->emailManager->Send();
            }

            if ($this->haveWebNotification) {
                //Sending the Web Notification Happens Here
                // $retArr["data"]["web_notification"] = $this->webNotificationManager->Send();
            }

            if ($this->haveMobileNotification) {
                //Sending the Mobile Notification Happens Here
                // $retArr["mobile_notification"] = 
            }

            if ($this->haveSms) {
                //Sending the Mobile SMS Happens Here
                // $retArr["sms"] = 
            }

            if (count($retArr) == 1) {
                $retArr = [
                    "status" => ERROR,
                    "message" => "Error Initializing Notification"
                ];
            }
            else {
                // foreach ($retArr["data"] AS $retRow) {
                //     if ($retRow["status"] != SUCCESS) {
                //         $retArr["status"] = $retRow["status"];
                //         $retArr["message"][] = $retRow["message"];
                //     }
                // }
            }

            $this->response = $retArr;
            return $retArr;
        }

        //BEGIN: Setters

        #Global
        public function SetQueueName(string $str) : void {
            $this->SetEmailQueueName($str);
        }
        
        public function SetPayload(array $payload) : void {
            $this->SetEmailPayload($payload);
        }

        public function SetUser(User $user) {
            $this->SetEmailUser($user);
            $this->SetWebNotificationUser($user);
        }
        
        public function SetStore(Store $store) {
            $this->SetWebNotificationStore($store);
        }
        
        public function SetFromUserId($id) {
            $this->SetWebNotificationFromUserId($id);
        }
        
        public function SetFromStoreId($id) {
            $this->SetWebNotificationFromStoreId($id);
        }
        
        public function SetToStoreId($id) {
            $this->SetWebNotificationToStoreId($id);
        }

        public function SetTemplate($templateName) {
            $this->SetEmailTemplate($templateName);
            $this->SetWebNotificationTemplate($templateName);
        }

        public function SetTemplateData($templateData) {
            $this->SetEmailTemplateData($templateData);
            $this->SetWebNotificationTemplateData($templateData);
        }
        
        public function AppendTemplateData($k, $v) {
            $this->AppendEmailTemplateData($k, $v);
            $this->AppendWebNotificationTemplateData($k, $v);
        }

        public function SetSubject($subject) {
            $this->SetEmailSubject($subject);
            $this->SetWebNotificationSubject($subject);
        }


        #Email
        public function SetEmailQueueName(string $str) : void {
            $this->emailManager->SetQueueName($str);
        }
        
        public function SetEmailPayload(array $payload) : void {
            $this->emailManager->SetPayload($payload);
        }

        public function SetEmailUser(User $user) {
            $this->emailManager->SetUser($user);
        }

        public function SetEmailMainTemplateBoxedWithButton() {
            $this->emailManager->SetMainTemplateBoxedWithButton();
        }

        public function SetEmailMainTemplateBoxed() {
            $this->emailManager->SetMainTemplateBoxed();
        }

        public function SetEmailTemplate($templateName) {
            $this->emailManager->SetTemplate($templateName);
        }

        public function SetEmailTemplateData($templateData) {
            $this->emailManager->SetTemplateData($templateData);
        }
        
        public function AppendEmailTemplateData($k, $v) {
            $this->emailManager->AppendTemplateData($k, $v);
        }

        public function SetEmailSubject($subject) {
            $this->emailManager->SetSubject($subject);
        }

        public function addEmailRecepient($email, $name="") {
            $this->emailManager->AddRecepient($email, $name);
        }

        public function AddEmailRecepients($arr) {
            $this->emailManager->AddRecepients($arr);
        }

        public function ClearEmailRecepients() {
            $this->emailManager->ClearRecepients();
        }

        public function addEmailCc($email, $name="") {
            $this->emailManager->AddCc($email, $name);
        }

        public function AddEmailCcs($arr) {
            $this->emailManager->AddCcs($arr);
        }

        public function ClearEmailCcs() {
            $this->emailManager->ClearCcs();
        }

        public function addEmailBcc($email, $name="") {
            $this->emailManager->AddBcc($email, $name);
        }

        public function AddEmailBccs($arr) {
            $this->emailManager->AddBccs($arr);
        }

        public function ClearEmailBccs() {
            $this->emailManager->ClearBccs();
        }

        public function AddEmailAttachment($name, $path) {
            $this->emailManager->AddAttachment($name, $path);
        }

        public function AddEmailAttachments($arr) {
            $this->emailManager->AddAttachments($arr);
        }

        
        #Web Notification
        public function SetWebNotificationUser(User $user) {
            $this->webNotificationManager->SetUser($user);
        }
        
        public function SetWebNotificationStore(Store $store) {
            $this->webNotificationManager->SetStore($store);
        }

        public function SetWebNotificationFromUserId($id) {
            $this->webNotificationManager->SetFromUserId($id);
        }

        public function SetWebNotificationFromStoreId($id) {
            $this->webNotificationManager->SetFromStoreId($id);
        }

        public function SetWebNotificationToStoreId($id) {
            $this->webNotificationManager->SetToStoreId($id);
        }

        public function SetWebNotificationTemplate($templateName) {
            $this->webNotificationManager->SetTemplate($templateName);
        }

        public function SetWebNotificationTemplateData($templateData) {
            $this->webNotificationManager->SetTemplateData($templateData);
        }
        
        public function AppendWebNotificationTemplateData($k, $v) {
            $this->webNotificationManager->AppendTemplateData($k, $v);
        }

        public function SetWebNotificationSubject($subject) {
            $this->webNotificationManager->SetSubject($subject);
        }
        //END: Setters
        
    }