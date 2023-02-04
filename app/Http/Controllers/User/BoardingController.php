<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Instance;
use App\BillingInfos;
use Carbon\Carbon;
use App\Community;
use Illuminate\Support\Facades\Mail;
use App\Mail\AssistanceNeeded;

class BoardingController extends BaseController
{

    /*
     * proposer les 2 formules d'abonnement
     * si formule payante l'ecran d'apres sera la saisie de la carte bancaire, sinon la suite du boarding
     */
    public function adminBoarding1()
    {
        $data = [
            'instanceId' => session('instanceId'),
            'stepBoarding' => 3,
        ];
        return view('welcome.subscription', $data);
    }

    public function adminBoardingCB()
    {
        $data = [];
        $data['instanceId'] = session('instanceId');

        if (request()->isMethod('POST')) {
            $rule = array(
                'card_number' => 'required|numeric',
                'card_expiry' => 'required|max:5|min:5',
                'card_crypto' => 'required|max:3|min:3',
            );
            $inputPost = array(
                'card_number' => trim(request()->get('card_number')),
                'card_expiry' => trim(request()->get('card_expiry')),
                'card_crypto' => trim(request()->get('card_crypto')),
            );
            $validation = validator($inputPost, $rule);
            if ($validation->fails()) {
                return redirect()->route('boarding.admin.stepCB')
                                 ->withErrors($validation)
                                 ->withInput();
            }

            // create srtipe customer
            $expiry_date = request()->get('card_expiry');
            $month = substr($expiry_date, 0, 2);
            $year = substr($expiry_date, 3);
            $card = [
                'number' => request()->get('card_number'),
                'expiry-month' => $month,
                'expiry-year' => $year,
                'crypto' => request()->get('card_crypto'),
            ];

            $stripeCustomer = \App\Helpers\StripeHelper::createCustomer($card);

            if ($stripeCustomer['result'] == 'success') {
                $stripeInstance = new BillingInfos();
                $stripeInstance->instances_id = session('instanceId');
                $stripeInstance->type = 'stripe_customer';

                $stripeInstance->value = json_encode($stripeCustomer['infos']);
                $stripeInstance->save();

                $instance = Instance::find(session('instanceId'));
                $instance->begin_date = Carbon::now()->addDays(45)->format('Y-m-d H:i:s');
                $instance->update();
                $bi = new BillingInfos();
                $bi->type = 'card';
                $bi->value = json_encode([
                    'name' => 'card',
                    'number' => request()->get('card_number'),
                    'expiry-month' => $month,
                    'expiry-year' => $year,
                    'crypto' => request()->get('card_crypto')
                ]);
                $bi->instances_id = session('instanceId');
                $bi->save();

                session()->forget('inCreation');
                session(['justCreated' => true]);

                $profile = auth()->guard('web')->user();
                return redirect($profile->getUrl());
            } else {
                $errorMessage = $stripeCustomer['error']->stripeCode;
                $data['errorCb'] = $errorMessage;
            }
        }

        $data['stepBoarding'] = 4;

        return view('welcome.card', $data);
    }

    public function adminBoarding2()
    {
        $profile = auth()->guard('web')->user();
        return redirect()->route('user.timeline');
    }

    public function adminBoarding3()
    {
    }

    public function adminBoarding4()
    {
    }


    public function userBoarding1()
    {
    }

    public function userBoarding2()
    {
    }

    public function userBoarding3()
    {
    }

    public function modalWelcome()
    {
        return view('welcome.modals.modal-welcome');
    }

    public function modalCallBack()
    {
        // get phone number
        $phoneNumber = request()->get('phone');
        $user = auth()->guard('web')->user();
        $user->phone = $phoneNumber;
        $user->save();

        $instance = Instance::find(session('instanceId'));

        // mail support
        Mail::to('contact@netframe.co')->send(new AssistanceNeeded($instance, $user));

        $data['phone'] = $phoneNumber;
        $view = view('welcome.modals.modal-phone-call', $data)->render();
        return response()->json([
            'view' => $view,
        ]);
    }

    public function modalGroup()
    {
        $community = new Community();

        $data = [
            'community' => $community,
        ];

        return view('welcome.modals.modal-create-group', $data);
    }

    public function modalInvite()
    {
        $instance = Instance::find(session('instanceId'));

        $data = [
            'instance' => $instance,
        ];

        return view('welcome.modals.modal-invite', $data);
    }

    public function acceptCharter()
    {
        $instance = Instance::find(session('instanceId'));
        if ($instance->getParameter('local_consent_state') &&
            !auth()->guard('web')->user()->getParameter('local_consent_state')
            ) {
                auth()->guard('web')->user()->setParameter('local_consent_state', true);
        }

        // redirect to previous page
        session()->flash('justCreated', true);
        return redirect(url()->previous());
    }
}
