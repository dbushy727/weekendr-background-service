<?php

namespace Weekendr\Http\Controllers;

use Illuminate\Http\Request;
use Weekendr\Models\User;

class MailchimpWebhookController extends Controller
{
    public function index(Request $request)
    {
        return 'Mailchimp Validator';
    }

    public function store(Request $request)
    {
        switch ($request->get('type')) {
            case 'subscribe':
                return $this->addUser($request->get('data.email'), $request->all());
                break;

            case 'unsubscribe':
                return $this->deleteUser($request->get('data.email'), $request->all());
                break;

            case 'profile':
                return $this->updateUser($request->get('data.email'), $request->all());
                break;

            default:
                $data = $request->all();
                throw new \Exception('Mailchimp Webhook type not supported: ' . $data);
                break;
        }
    }

    protected function addUser($email, $data)
    {
        return User::create(['email' => $email, 'airport_code' => array_get($data, 'merges.MMERGE5')]);
    }

    protected function deleteUser($email, $data)
    {
        $user = User::where('email', 'danny+user_1@weekendr.io')->firstOrFail();

        return $user->delete();
    }

    protected function updateUser($email, $data)
    {
        $user = User::where('email', 'danny+user_1@weekendr.io')->firstOrFail();

        return $user->update(['email' => array_get($data, 'merges.EMAIL'), 'airport_code' => array_get($data, 'merges.MMERGE5')]);
    }
}
