<?php

namespace App\Http\Controllers;

use App\Mail\EmailTest;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SystemController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage system settings'))
        {
            $settings = Utility::settings();

            return view('settings.company', compact('settings'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('manage system settings'))
        {
            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            $header_text = (!isset($request->header_text) && empty($request->header_text)) ? '' : $request->header_text;
            $footer_text = (!isset($request->footer_text) && empty($request->footer_text)) ? '' : $request->footer_text;
            $arrSetting  = [
                'header_text' => $header_text,
                'footer_text' => $footer_text,
                'default_language' => $request->default_language,
                'enable_landing' => isset($request->enable_landing) ? $request->enable_landing : 'no',
            ];

            foreach($arrSetting as $key => $val)
            {
                \DB::insert(
                    'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                      $val,
                                                                                                                                                                                                                      $key,
                                                                                                                                                                                                                      \Auth::user()->creatorId(),
                                                                                                                                                                                                                      $created_at,
                                                                                                                                                                                                                      $updated_at,
                                                                                                                                                                                                                  ]
                );
            }


            if($request->favicon)
            {
                $request->validate(['favicon' => 'required|image|mimes:png|max:1024',]);
                $faviconName = 'favicon.png';
                $path        = $request->file('favicon')->storeAs('logo', $faviconName);
            }
            if($request->logo)
            {
                $request->validate(['logo' => 'required|image|mimes:png|max:1024',]);
                $logoName = 'logo.png';
                $path     = $request->file('logo')->storeAs('logo', $logoName);
            }
            if($request->landing_logo)
            {
                $request->validate(['landing_logo' => 'required|image|mimes:png|max:1024',]);
                $smallName = 'landing_logo.png';
                $path      = $request->file('landing_logo')->storeAs('logo', $smallName);
            }

            return redirect()->back()->with('success', __('Site Setting successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function saveEmailSettings(Request $request)
    {
        if(\Auth::user()->can('manage system settings'))
        {
            $request->validate(
                [
                    'mail_driver' => 'required|string|max:255',
                    'mail_host' => 'required|string|max:255',
                    'mail_port' => 'required|string|max:255',
                    'mail_username' => 'required|string|max:255',
                    'mail_password' => 'required|string|max:255',
                    'mail_encryption' => 'required|string|max:255',
                    'mail_from_address' => 'required|string|max:255',
                    'mail_from_name' => 'required|string|max:255',
                ]
            );

            $arrEnv = [
                'MAIL_DRIVER' => $request->mail_driver,
                'MAIL_HOST' => $request->mail_host,
                'MAIL_PORT' => $request->mail_port,
                'MAIL_USERNAME' => $request->mail_username,
                'MAIL_PASSWORD' => $request->mail_password,
                'MAIL_ENCRYPTION' => $request->mail_encryption,
                'MAIL_FROM_ADDRESS' => $request->mail_from_address,
                'MAIL_FROM_NAME' => $request->mail_from_name,
            ];

            $env = Utility::setEnvironmentValue($arrEnv);

            if($env)
            {
                return redirect()->back()->with('success', __('Setting successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Something went wrong.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function saveCompanySettings(Request $request)
    {
        if(\Auth::user()->can('manage company settings'))
        {
            $user = \Auth::user();
            $request->validate(
                [
                    'company_name' => 'required|string|max:50',
                    'company_email' => 'required',
                    'company_email_from_name' => 'required|string',
                ]
            );
            $post = $request->all();
            unset($post['_token']);

            foreach($post as $key => $data)
            {
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ', [
                                                                                                                                                 $data,
                                                                                                                                                 $key,
                                                                                                                                                 \Auth::user()->creatorId(),
                                                                                                                                             ]
                );
            }

            return redirect()->back()->with('success', __('Setting successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function saveCompanyPaymentSettings(Request $request)
    {
        if(\Auth::user()->can('manage company settings'))
        {
            $post = $request->all();
            unset($post['_token']);

            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            $stripe_status = $request->site_enable_stripe ?? 'off';
            $paypal_status = $request->site_enable_paypal ?? 'off';

            $validatorArray = [];

            if($stripe_status == 'on')
            {
                $validatorArray['site_stripe_key']    = 'required|string|max:255';
                $validatorArray['site_stripe_secret'] = 'required|string|max:255';
            }
            if($paypal_status == 'on')
            {
                $validatorArray['site_paypal_client_id']  = 'required|string|max:255';
                $validatorArray['site_paypal_secret_key'] = 'required|string|max:255';
            }

            $validator = Validator::make(
                $request->all(), $validatorArray
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $post['site_enable_stripe'] = $stripe_status;
            $post['site_enable_paypal'] = $paypal_status;

            foreach($post as $key => $data)
            {
                \DB::insert(
                    'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                      $data,
                                                                                                                                                                                                                      $key,
                                                                                                                                                                                                                      \Auth::user()->creatorId(),
                                                                                                                                                                                                                      $created_at,
                                                                                                                                                                                                                      $updated_at,
                                                                                                                                                                                                                  ]
                );
            }

            return redirect()->back()->with('success', __('Setting successfully updated.'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function saveSystemSettings(Request $request)
    {
        if(\Auth::user()->can('manage company settings'))
        {
            $user = \Auth::user();
            $request->validate(
                [
                    'site_currency' => 'required',
                ]
            );
            $post = $request->all();
            unset($post['_token']);

            foreach($post as $key => $data)
            {
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ', [
                                                                                                                                                                                 $data,
                                                                                                                                                                                 $key,
                                                                                                                                                                                 \Auth::user()->creatorId(),
                                                                                                                                                                                 date('Y-m-d H:i:s'),
                                                                                                                                                                                 date('Y-m-d H:i:s'),
                                                                                                                                                                             ]
                );
            }

            return redirect()->back()->with('success', __('Setting successfully updated.'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function savePusherSettings(Request $request)
    {
        if(\Auth::user()->can('manage system settings'))
        {
            if(isset($request->enable_chat))
            {
                $request->validate(
                    [
                        'pusher_app_id' => 'required',
                        'pusher_app_key' => 'required',
                        'pusher_app_secret' => 'required',
                        'pusher_app_cluster' => 'required',
                    ]
                );
            }

            $arrEnv = [
                'CHAT_MODULE' => $request->enable_chat,
                'PUSHER_APP_ID' => $request->pusher_app_id,
                'PUSHER_APP_KEY' => $request->pusher_app_key,
                'PUSHER_APP_SECRET' => $request->pusher_app_secret,
                'PUSHER_APP_CLUSTER' => $request->pusher_app_cluster,
            ];

            $env = Utility::setEnvironmentValue($arrEnv);

            if($env)
            {
                return redirect()->back()->with('success', __('Pusher Setting successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Something went wrong.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function companyIndex()
    {
        if(\Auth::user()->can('manage company settings'))
        {
            $settings = Utility::settings();

            return view('settings.company', compact('settings'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function saveTemplateSettings(Request $request)
    {
        $user = \Auth::user();
        $post = $request->all();
        unset($post['_token']);

        if(isset($post['invoice_template']) && (!isset($post['invoice_color']) || empty($post['invoice_color'])))
        {
            $post['invoice_color'] = "ffffff";
        }

        if(isset($post['estimation_template']) && (!isset($post['estimation_color']) || empty($post['estimation_color'])))
        {
            $post['estimation_color'] = "ffffff";
        }

        if($request->invoice_logo)
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'invoice_logo' => 'image|mimes:png|max:2048',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $invoice_logo         = $user->id . '_invoice_logo.png';
            $path                 = $request->file('invoice_logo')->storeAs('invoice_logo', $invoice_logo);
            $post['invoice_logo'] = $invoice_logo;
        }

        if($request->estimation_logo)
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'estimation_logo' => 'image|mimes:png|max:2048',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $estimation_logo         = $user->id . '_estimation_logo.png';
            $path                    = $request->file('estimation_logo')->storeAs('estimation_logo', $estimation_logo);
            $post['estimation_logo'] = $estimation_logo;
        }

        foreach($post as $key => $data)
        {
            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ', [
                                                                                                                                             $data,
                                                                                                                                             $key,
                                                                                                                                             \Auth::user()->creatorId(),
                                                                                                                                         ]
            );
        }

        if(isset($post['invoice_template']))
        {
            return redirect()->back()->with('success', __('Invoice Setting updated successfully'));
        }

        if(isset($post['estimation_template']))
        {
            return redirect()->back()->with('success', __('Estimation Setting updated successfully'));
        }
    }

    // Test Mail
    public function testEmail()
    {
        $user = \Auth::user();
        if($user->type == 'company')
        {
            return view('settings.test_email');
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function testEmailSend(Request $request)
    {
        $validator = \Validator::make($request->all(), ['email' => 'required|email']);
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        try
        {
            Mail::to($request->email)->send(new EmailTest());
        }
        catch(\Exception $e)
        {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', __('Email send Successfully'));
    }
}
