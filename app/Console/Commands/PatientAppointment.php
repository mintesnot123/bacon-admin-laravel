<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


use Auth;

use DB;

class PatientAppointment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patient:appointment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Beacon Patient Appointment Schedule Notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        //\Log::info("Cron is working fine!");

         $notifications = DB::table('user_notifications')->where('sending_time','<=',date('Y-m-d H:i:s'))->where('sending_status',0)->get();

         foreach ($notifications as $key => $data) {

            $patient = DB::table('users')->where('id',$data->user_id)->where('status',1)->first();

            if($patient && !empty($patient->token)){

                $regId = $patient->token;
                $notification = array();
                $arrNotification = array();          
                $arrData = array();   

                $arrNotification["body"] = $data->message;
                $arrNotification["title"] = "Beacon Patient Care Doctor Appointment Notification";
                $arrNotification["sound"] = "default";
                $arrNotification["type"] = 1;

                $result = $this->send_notification($regId, $arrNotification,"Android");
                // $result = $this->send_notification($regId, $arrNotification,"IOS");

                if($result && $result['success'])
                   $notification_status = DB::table('user_notifications')->where('id', $data->id)->update([ 'sending_status' => 1]);   
                \Log::info($result);
            }   
         }

        $this->info('patient:appointment Cummand Run successfully!');
    }


    public function send_notification($registatoin_ids, $notification, $device_type) {
              

        $url = 'https://fcm.googleapis.com/fcm/send';
          

          if($device_type == "Android"){
                $fields = array(
                    'to' => $registatoin_ids,
                    'data' => $notification
                );
          } else {
                $fields = array(
                    'to' => $registatoin_ids,
                    'notification' => $notification
                );
          }

          //return $fields;
          $server_key = getenv('FIREBASE_SERVER_KEY');
          // Firebase API Key
          $headers = array(
            "Authorization:key=".$server_key
            ,"Content-Type:application/json"
          );
          // Open connection
          $ch = curl_init();
          // Set the url, number of POST vars, POST data
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          // Disabling SSL Certificate support temporarly
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
          $result = curl_exec($ch);

         //  \Log::info($result);

          if ($result === FALSE) {
              die('Curl failed: ' . curl_error($ch));
          }

          curl_close($ch);

          return json_decode($result, true);
    }
}
