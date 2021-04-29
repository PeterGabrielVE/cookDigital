<?php

namespace App;

use App\Mail\CommonEmailTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Pusher\Pusher;

class Utility extends Model
{
    public static function settings()
    {
        $data = DB::table('settings');

        if(Auth::check())
        {
            $data->where('created_by', '=', Auth::user()->creatorId())->orWhere('created_by', '=', 1);
        }
        else
        {
            $data->where('created_by', '=', 1);
        }

        $data = $data->get();

        $settings = [
            "site_currency" => "USD",
            "site_currency_symbol" => "$",
            "site_enable_stripe" => "off",
            "site_stripe_key" => "",
            "site_stripe_secret" => "",
            "site_enable_paypal" => "off",
            "site_paypal_mode" => "sandbox",
            "site_paypal_client_id" => "",
            "site_paypal_secret_key" => "",
            "site_currency_symbol_position" => "pre",
            "site_date_format" => "M j, Y",
            "site_time_format" => "g:i A",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            "company_email" => "",
            "company_email_from_name" => "",
            "invoice_prefix" => "#INV",
            "bug_prefix" => "#ISSUE",
            "estimation_prefix" => "#EST",
            "invoice_template" => "template1",
            "invoice_color" => "fffff",
            "estimation_template" => "template1",
            "estimation_color" => "fffff",
            "default_language" => "en",
            "footer_title" => "Payment Information",
            "footer_note" => "Thank you for your business.",
            "enable_landing" => "yes",
        ];

        foreach($data as $row)
        {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function getValByName($key)
    {
        $setting = Utility::settings();

        if(!isset($setting[$key]) || empty($setting[$key]))
        {
            $setting[$key] = '';
        }

        return $setting[$key];
    }

    public static function languages()
    {
        $dir     = base_path() . '/resources/lang/';
        $glob    = glob($dir . "*", GLOB_ONLYDIR);
        $arrLang = array_map(
            function ($value) use ($dir){
                return str_replace($dir, '', $value);
            }, $glob
        );
        $arrLang = array_map(
            function ($value) use ($dir){
                return preg_replace('/[0-9]+/', '', $value);
            }, $arrLang
        );
        $arrLang = array_filter($arrLang);

        return $arrLang;
    }

    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str     = file_get_contents($envFile);

        if(count($values) > 0)
        {
            foreach($values as $envKey => $envValue)
            {
                $keyPosition       = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine           = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
                // If key does not exist, add it
                if(!$keyPosition || !$endOfLinePosition || !$oldLine)
                {
                    $str .= "{$envKey}='{$envValue}'\n";
                }
                else
                {
                    $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                }
            }
        }
        $str = substr($str, 0, -1);
        $str .= "\n";
        if(!file_put_contents($envFile, $str))
        {
            return false;
        }

        return true;
    }

    public static function dateFormat($date)
    {
        $settings = Utility::settings();

        return date($settings['site_date_format'], strtotime($date));
    }

    public static function invoiceNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["invoice_prefix"] . sprintf("%05d", $number);
    }

    public static function estimateNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["estimation_prefix"] . sprintf("%05d", $number);
    }

    public static function sendNotification($type, $user_id, $obj)
    {
        if($user_id != \Auth::user()->id)
        {
            $notification = Notification::create(
                [
                    'user_id' => $user_id,
                    'type' => $type,
                    'data' => json_encode($obj),
                    'is_read' => 0,
                ]
            );

            // Push Notification
            $options = array(
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            );

            $pusher          = new Pusher(
                env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), $options
            );
            $data            = [];
            $data['html']    = $notification->toHtml();
            $data['user_id'] = $notification->user_id;

            $pusher->trigger('send_notification', 'notification', $data);

            // End Push Notification
        }
    }

    public static function templateData()
    {
        $arr              = [];
        $arr['colors']    = [
            '003580',
            '666666',
            '6777f0',
            'f50102',
            'f9b034',
            'fbdd03',
            'c1d82f',
            '37a4e4',
            '8a7966',
            '6a737b',
            '050f2c',
            '0e3666',
            '3baeff',
            '3368e6',
            'b84592',
            'f64f81',
            'f66c5f',
            'fac168',
            '46de98',
            '40c7d0',
            'be0028',
            '2f9f45',
            '371676',
            '52325d',
            '511378',
            '0f3866',
            '48c0b6',
            '297cc0',
            'ffffff',
            '000',
        ];
        $arr['templates'] = [
            "template1" => "New York",
            "template2" => "Toronto",
            "template3" => "Rio",
            "template4" => "London",
            "template5" => "Istanbul",
            "template6" => "Mumbai",
            "template7" => "Hong Kong",
            "template8" => "Tokyo",
            "template9" => "Sydney",
            "template10" => "Paris",
        ];

        return $arr;
    }

    // Email Template Modules Function START
    // Common Function That used to send mail with check all cases
    public static function sendEmailTemplate($emailTemplate, $user_id, $obj)
    {
        $usr = Auth::user();
        if($user_id != $usr->id)
        {
            $mailTo = User::find($user_id)->email;

            if($usr->type != 'super admin')
            {
                // find template is exist or not in our record
                $template = EmailTemplate::where('name', 'LIKE', $emailTemplate)->first();

                if(isset($template) && !empty($template))
                {
                    // check template is active or not by company
                    $is_active = UserEmailTemplate::where('template_id', '=', $template->id)->where('user_id', '=', $usr->creatorId())->first();

                    if($is_active->is_active == 1)
                    {
                        $settings = self::settings();

                        // get email content language base
                        $content       = EmailTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', $usr->lang)->first();
                        $content->from = $template->from;
                        if(!empty($content->content))
                        {
                            $content->content = self::replaceVariable($emailTemplate, $content->content, $obj);

                            // send email
                            try
                            {
                                Mail::to($mailTo)->send(new CommonEmailTemplate($content, $settings));
                            }
                            catch(\Exception $e)
                            {
                                $error = __('E-Mail has been not sent due to SMTP configuration');
                            }

                            if(isset($error))
                            {
                                $arReturn = [
                                    'is_success' => false,
                                    'error' => $error,
                                ];
                            }
                            else
                            {
                                $arReturn = [
                                    'is_success' => true,
                                    'error' => false,
                                ];
                            }
                        }
                        else
                        {
                            $arReturn = [
                                'is_success' => false,
                                'error' => __('Mail not send, email is empty'),
                            ];
                        }

                        return $arReturn;
                    }
                    else
                    {
                        return [
                            'is_success' => true,
                            'error' => false,
                        ];
                    }
                }
                else
                {
                    return [
                        'is_success' => false,
                        'error' => __('Mail not send, email not found'),
                    ];
                }
            }
        }
    }

    // used for replace email variable (parameter 'template_name','id(get particular record by id for data)')
    public static function replaceVariable($name, $content, $obj)
    {
        $arrVariable = [
            '{project_name}',
            '{project_label}',
            '{project_status}',
            '{task_name}',
            '{task_priority}',
            '{task_status}',
            '{task_old_stage}',
            '{task_new_stage}',
            '{estimation_name}',
            '{estimation_client}',
            '{estimation_status}',
            '{app_name}',
            '{company_name}',
            '{email}',
            '{password}',
            '{app_url}',
        ];
        $arrValue    = [
            'project_name' => '-',
            'project_label' => '-',
            'project_status' => '-',
            'task_name' => '-',
            'task_priority' => '-',
            'task_status' => '-',
            'task_old_stage' => '-',
            'task_new_stage' => '-',
            'estimation_name' => '-',
            'estimation_client' => '-',
            'estimation_status' => '-',
            'app_name' => '-',
            'company_name' => '-',
            'email' => '-',
            'password' => '-',
            'app_url' => '-',
        ];

        foreach($obj as $key => $val)
        {
            $arrValue[$key] = $val;
        }

        $arrValue['app_name']     = env('APP_NAME');
        $arrValue['company_name'] = self::settings()['company_name'];
        $arrValue['app_url']      = '<a href="' . env('APP_URL') . '" target="_blank">' . env('APP_URL') . '</a>';

        return str_replace($arrVariable, array_values($arrValue), $content);
    }

    // Make Entry in email_tempalte_lang table when create new language
    public static function makeEmailLang($lang)
    {
        $template = EmailTemplate::all();
        foreach($template as $t)
        {
            $default_lang                 = EmailTemplateLang::where('parent_id', '=', $t->id)->where('lang', 'LIKE', 'en')->first();
            $emailTemplateLang            = new EmailTemplateLang();
            $emailTemplateLang->parent_id = $t->id;
            $emailTemplateLang->lang      = $lang;
            $emailTemplateLang->subject   = $default_lang->subject;
            $emailTemplateLang->content   = $default_lang->content;
            $emailTemplateLang->save();
        }
    }
    // Email Template Modules Function END

    // get font-color code accourding to bg-color
    public static function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3)
        {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        }
        else
        {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array(
            $r,
            $g,
            $b,
        );

        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    public static function getFontColor($color_code)
    {
        $rgb = self::hex2rgb($color_code);
        $R   = $G = $B = $C = $L = $color = '';

        $R = (floor($rgb[0]));
        $G = (floor($rgb[1]));
        $B = (floor($rgb[2]));

        $C = [
            $R / 255,
            $G / 255,
            $B / 255,
        ];

        for($i = 0; $i < count($C); ++$i)
        {
            if($C[$i] <= 0.03928)
            {
                $C[$i] = $C[$i] / 12.92;
            }
            else
            {
                $C[$i] = pow(($C[$i] + 0.055) / 1.055, 2.4);
            }
        }

        $L = 0.2126 * $C[0] + 0.7152 * $C[1] + 0.0722 * $C[2];

        if($L > 0.179)
        {
            $color = 'black';
        }
        else
        {
            $color = 'white';
        }

        return $color;
    }

    // Function not used any where just create for translate some keyword language based.
    public function extraKeyword()
    {
        [
            __('account'),
            __('user'),
            __('client'),
            __('role'),
            __('company settings'),
            __('project'),
            __('product'),
            __('lead'),
            __('lead stage'),
            __('project stage'),
            __('lead source'),
            __('label'),
            __('product unit'),
            __('expense category'),
            __('expense'),
            __('tax'),
            __('invoice'),
            __('payment'),
            __('invoice product'),
            __('invoice payment'),
            __('task'),
            __('checklist'),
            __('plan'),
            __('note'),
            __('bug report'),
            __('timesheet'),
            __('language'),
            __('permission'),
            __('system settings'),
            __('Create Milestone'),
            __('Upload File'),
            __('Create Task'),
            __('Move'),
        ];
    }

    // End

    public static function get_messenger_packages_migration()
    {
        $totalMigration = 0;
        $messengerPath  = glob(base_path() . '/vendor/munafio/chatify/database/migrations' . DIRECTORY_SEPARATOR . '*.php');
        if(!empty($messengerPath))
        {
            $messengerMigration = str_replace('.php', '', $messengerPath);
            $totalMigration     = count($messengerMigration);
        }

        return $totalMigration;
    }

    public static function delete_directory($dir)
    {
        if(!file_exists($dir))
        {
            return true;
        }

        if(!is_dir($dir))
        {
            return unlink($dir);
        }

        foreach(scandir($dir) as $item)
        {
            if($item == '.' || $item == '..')
            {
                continue;
            }

            if(!self::delete_directory($dir . DIRECTORY_SEPARATOR . $item))
            {
                return false;
            }

        }

        return rmdir($dir);
    }
}
