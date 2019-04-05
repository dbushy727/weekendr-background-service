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
        $data = $request->all();

        switch ($request->get('type')) {
            case 'subscribe':
                return $this->addUser(array_get($data, 'data'));
                break;

            case 'unsubscribe':
                return $this->deleteUser(array_get($data, 'data'));
                break;

            case 'profile':
            case 'upemail':
                return $this->updateUser(array_get($data, 'data'));
                break;

            case 'campaign':
                // just letting me know that the campaign was sent. nothing to do here
                break;

            default:
                throw new \Exception('Mailchimp Webhook type not supported: ' . $data);
                break;
        }
    }

    protected function addUser($data)
    {
        return User::create(['email' => array_get($data, 'email'), 'airport_code' => array_get($data, 'merges.AIRPORT')]);
    }

    protected function deleteUser($data)
    {
        $user = User::where('email', array_get($data, 'email'))->firstOrFail();
        $user->delete();
        return [];
    }

    protected function updateUser($data)
    {
        $user = User::where('email', array_get($data, 'email'))->firstOrFail();
        $user->update(['email' => array_get($data, 'merges.EMAIL'), 'airport_code' => array_get($data, 'merges.MMERGE5')]);

        return $user = User::where('email', array_get($data, 'email'))->firstOrFail();
    }
}
