<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\EmailSendRecord;
use App\EmailSendSchedule;
use App\OfflineDistribution;
use App\User;

use App\Mail\ActivateAccount;
use App\Mail\PasswordChanged;
use App\Mail\ResetPassword;
use App\Mail\ShareOfflineDistribution;


class EmailController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Run email sending jobs automatically
     *
     * @return void
     */
    public function __invoke()
    {
        $this->run();
        return;
    }

    /**
     * Execute email sending jobs
     *
     * @return void
     */
    private function run(int $email_send_schedule_id = null)
    {
        if ($email_send_schedule_id !== null) {
            $schedules = EmailSendSchedule
                ::where('id', $email_send_schedule_id)
                ->get();
        } else {
            $now = Carbon::now();
            $current_timestamp = $now->timestamp;
            $schedules = EmailSendSchedule
                ::where('status', '!=', 'SUCCESS')
                ->where('send_timestamp', '<=', $current_timestamp)
                ->get();
        }

        foreach ($schedules as $schedule) {
            // First, mark as ongoing
            $schedule->update(['status' => 'ON_PROGRESS']);

            $record = EmailSendRecord::create([
                'email_send_schedule_id' => $schedule->id,
                'request_parameter' => $schedule->request_parameter
            ]);
            $request_parameter = json_decode($schedule->request_parameter, true);

            try {
                switch ($schedule->email_class) {
                    case 'ActivateAccount':
                        $user_id = $request_parameter['user_id'];
                        $user = User::findOrFail($user_id);
                        $request_parameter['user'] = $user->toJson();
                        $request_parameter['to'] = array($user->email);
                        $request_parameter['bcc'] = array(config('mail.from.address'));

                        $record->update(['request_parameter' => json_encode($request_parameter)]);

                        Mail::to($request_parameter['to'])
                            ->bcc($request_parameter['bcc'])
                            ->send(new ActivateAccount($user, $request_parameter['token']));

                        break;
                    case 'PasswordChanged':
                        $user_id = $request_parameter['user_id'];
                        $user = User::findOrFail($user_id);
                        $request_parameter['user'] = $user->toJson();
                        $request_parameter['to'] = array($user->email);
                        $request_parameter['bcc'] = array(config('mail.from.address'));

                        $record->update(['request_parameter' => json_encode($request_parameter)]);

                        Mail::to($request_parameter['to'])
                            ->bcc($request_parameter['bcc'])
                            ->send(new PasswordChanged($user));

                        break;
                    case 'ResetPassword':
                        $user_id = $request_parameter['user_id'];
                        $user = User::findOrFail($user_id);
                        $request_parameter['user'] = $user->toJson();
                        $request_parameter['to'] = array($user->email);
                        $request_parameter['bcc'] = array(config('mail.from.address'));

                        $record->update(['request_parameter' => json_encode($request_parameter)]);

                        Mail::to($request_parameter['to'])
                            ->bcc($request_parameter['bcc'])
                            ->send(new ResetPassword($user, $request_parameter['token'], $request_parameter['create_time']));

                        break;
                    case 'ShareOfflineDistribution':
                        $offline_distribution_id = $request_parameter['offline_distribution_id'];
                        $offline_distribution = OfflineDistribution::findOrFail($offline_distribution_id);
                        $request_parameter['offline_distribution'] = $offline_distribution->toJson();
                        $request_parameter['to'] = explode(',', $offline_distribution->recipient_email);
                        $request_parameter['bcc'] = array(config('mail.from.address'));

                        $record->update(['request_parameter' => json_encode($request_parameter)]);

                        Mail::to($request_parameter['to'])
                            ->bcc($request_parameter['bcc'])
                            ->send(new ShareOfflineDistribution($offline_distribution));

                        break;
                    default:
                        throw new Exception('Unknown email class name.');
                }
            } catch (Exception $e) {
                // If not success, mark as failed.
                $schedule->update(['status' => 'FAILED']);
                $record->update([
                    'status' => 'FAILED',
                    'error' => $e->getMessage().'\n'.$e->getTraceAsString()
                ]);
                continue;
            } catch (\Exception $e) {
                // If not success, mark as failed.
                $schedule->update(['status' => 'FAILED']);
                $record->update([
                    'status' => 'FAILED',
                    'error' => $e->getMessage().'\n'.$e->getTraceAsString()
                ]);
                continue;
            }

            // Finally, mark as success.
            $schedule->update(['status' => 'SUCCESS']);
            $record->update(['status' => 'SUCCESS']);
        }
    }
}
