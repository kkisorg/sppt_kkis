<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

use Facebook;
use GuzzleHttp;

use App\Announcement;
use App\AnnouncementOnlineMediaPublishSchedule;
use App\AnnouncementOnlineMediaPublishRecord;
use App\Media;
use App\User;
use App\UserActivityTracking;
use App\Mail\PublishToOnlineMedia;
use App\Mail\PublishToWebsite;

class AnnouncementOnlineMediaPublishScheduleController extends Controller
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
     * Display the list of announcement online media publish schedule
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'DISPLAY',
            'activity_details' => 'ANNOUNCEMENT_ONLINE_MEDIA_PUBLISH_SCHEDULE_INDEX',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }

        $last_month = Carbon::now()->subMonth();
        $last_month_timestamp = $last_month->timestamp;

        $schedules = AnnouncementOnlineMediaPublishSchedule
            ::where('publish_timestamp', '>=', $last_month_timestamp)
            ->orWhere('status', '!=', 'SUCCESS')
            ->orderBy('publish_timestamp', 'desc')
            ->get();
        foreach ($schedules as $schedule) {
            $schedule->publish_datetime =
                Carbon::createFromTimestamp($schedule->publish_timestamp)->format('l, j F Y, g:i a');
        }
        return view('announcementonlinemediapublishschedule.index', ['announcement_online_media_publish_schedules' => $schedules]);
    }

    /**
     * Display the view publish schedule page
     *
     * @param Request $request
     * @param string $schedule_id
     * @return Response
     */
    public function view(Request $request, string $schedule_id)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'DISPLAY',
            'activity_details' => 'ANNOUNCEMENT_ONLINE_MEDIA_PUBLISH_SCHEDULE_VIEW',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }

        $schedule = AnnouncementOnlineMediaPublishSchedule::findOrFail($schedule_id);

        // Convert some schedule details into the frontend format
        $schedule->publish_datetime =
            Carbon::createFromTimestamp($schedule->publish_timestamp)->format('l, j F Y, g:i a');

        // Retrieve the records
        $records = $schedule
            ->announcement_online_media_publish_record()
            ->orderBy('create_timestamp')
            ->get();
        foreach ($records as $record) {
            $record->request_parameter =
                json_encode(json_decode($record->request_parameter), JSON_PRETTY_PRINT);
            $record->response_content =
                json_encode(json_decode($record->response_content), JSON_PRETTY_PRINT);
            $record->create_datetime = $record->create_timestamp->format('l, j F Y, g:i a');
        }

        return view('announcementonlinemediapublishschedule.view', [
            'schedule' => $schedule,
            'records' => $records
        ]);
    }

    /**
     * Run publish jobs automatically.
     *
     * Publish schedule with status = 'INITIAL' will be attempted immediately.
     * Publish schedule with status = 'FAILED' will be reattempted every hour.
     *
     * @return void
     */
    public function __invoke()
    {
        $now = Carbon::now();
        $current_timestamp = $now->timestamp;

        $schedules = AnnouncementOnlineMediaPublishSchedule
            ::where('publish_timestamp', '<=', $current_timestamp);
        if ($now->format('i') !== '00') {
            // First attempt only.
            $schedules = $schedules->where('status', 'INITIAL');
        } else {
            // First attempt and re-attempt of the failed ones.
            $schedules = $schedules->where('status', '!=', 'SUCCESS');
        }
        $schedule_ids = $schedules->pluck('id')->toArray();
        $this->run($schedule_ids);
        return;
    }

    /**
     * Run publish jobs manually.
     *
     * @return void
     */
    public function manual_invoke(Request $request, string $schedule_id)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'CLICK',
            'activity_details' => 'ANNOUNCEMENT_ONLINE_MEDIA_PUBLISH_SCHEDULE_MANUAL_INVOKE',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }

        $this->run(array($schedule_id), true);
        return back(303)->with('success_message', 'Task publikasi ke media online telah berhasil dieksekusi.');
    }

    /**
     * Run online media publication jobs.
     *
     * @return void
     */
    private function run(array $announcement_online_media_publish_schedule_id, bool $is_manual = false)
    {
        $schedules = AnnouncementOnlineMediaPublishSchedule
            ::whereIn('id', $announcement_online_media_publish_schedule_id)
            ->get();

        foreach ($schedules as $schedule) {
            // First, mark as ongoing
            $schedule->update(['status' => 'ON_PROGRESS']);

            $media = $schedule->media;
            $media_name = $media->name;
            $media_name_lower = strtolower($media_name);

            $record = AnnouncementOnlineMediaPublishRecord::create([
                'announcement_online_media_publish_schedule_id' => $schedule->id,
                'request_parameter' => '',
                'is_manual' => $is_manual,
                'creator_id' => $is_manual ? Auth::id() : null
            ]);

            try {
                switch ($media_name_lower) {
                    case 'website':
                        // Send email to the website mailbox.

                        $request_parameter = array(
                            'announcement_online_media_publish_schedule_id' => $schedule->id,
                            'announcement_online_media_publish_schedule' => $schedule->toJson(),
                            'to' => array(env('WEBSITE_MAILBOX_EMAIL')),
                            'bcc' => array(config('mail.from.address'))
                        );
                        $record->update(['request_parameter' => json_encode($request_parameter)]);

                        Mail::to(env('WEBSITE_MAILBOX_EMAIL'))
                            ->bcc(config('mail.from.address'))
                            ->send(new PublishToWebsite($schedule));
                        break;
                    case 'facebook':
                        // Prepare content and attachment(s).
                        $image_path_array = array();
                        $content = $schedule->content;
                        // Collect attachment(s).
                        preg_match_all('/(<img src=")(.*?)(")/', $content, $tmp_image_path_array);
                        foreach ($tmp_image_path_array[2] as $image_path) {
                            if (substr($image_path, strpos($image_path, '/storage/'), strlen('/storage/')) == '/storage/') {
                                $image_path = substr($image_path, strpos($image_path, '/storage/'));
                                $image_path = env('APP_URL').$image_path;
                            }
                            array_push($image_path_array, $image_path);
                        }
                        // Reformat content.
                        $content = preg_replace('#<br\s*\/?>#', "\n", $content);
                        $content = preg_replace('#<\/p>#', "\n\n", $content);
                        $content = strip_tags($content);

                        if (count($image_path_array) == 0) {
                            $request_parameter = array('message' => $content);
                        } else {
                            $request_parameter = array(
                                'caption' => $content, 'url' => $image_path_array[0]
                            );
                        }
                        $record->update(['request_parameter' => json_encode($request_parameter)]);

                        // Initialize Facebook SDK.
                        $client = new GuzzleHttp\Client;
                        $fb = new Facebook\Facebook([
                            'app_id' => env('FACEBOOK_APP_ID_'),
                            'app_secret' => env('FACEBOOK_APP_SECRET'),
                            'http_client_handler' => new Guzzle6HttpClient($client),
                        ]);
                        // Make a HTTP POST request.
                        if (count($image_path_array) == 0) {
                            // Text only.
                            $response = $fb->post(
                                '/'.env('FACEBOOK_PAGE_ID').'/feed',
                                array('message' => $content),
                                env('FACEBOOK_PAGE_ACCESS_TOKEN')
                            );
                            $graphNode = $response->getGraphNode();
                            $response_content = array('id' => $graphNode['id']);
                        } else {
                            // Text and image.
                            // Only take the 1st image.
                            $response = $fb->post(
                                '/'.env('FACEBOOK_PAGE_ID').'/photos',
                                array('caption' => $content, 'url' => $image_path_array[0]),
                                env('FACEBOOK_PAGE_ACCESS_TOKEN')
                            );
                            $graphNode = $response->getGraphNode();
                            $response_content = array(
                                'id' => $graphNode['id'], 'post_id' => $graphNode['post_id']
                            );
                        }

                        // If success, record the response.
                        $record->update([
                            'response_content' => json_encode($response_content)
                        ]);
                        break;
                    default:
                        // Send email to administrator.
                        $admin_email_array = User::where('is_admin', true)
                                                ->pluck('email')->toArray();

                        $request_parameter = array(
                            'announcement_online_media_publish_schedule_id' => $schedule->id,
                            'announcement_online_media_publish_schedule' => $schedule->toJson(),
                            'to' => $admin_email_array,
                            'bcc' => array(config('mail.from.address'))
                        );
                        $record->update(['request_parameter' => json_encode($request_parameter)]);

                        Mail::to($admin_email_array)
                            ->bcc(config('mail.from.address'))
                            ->send(new PublishToOnlineMedia($schedule));

                }
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                // If not success, mark as failed.
                $schedule->update(['status' => 'FAILED']);
                $record->update([
                    'status' => 'FAILED',
                    'error' => 'Facebook SDK returned an error: ' . $e->getMessage()."\n".$e->getTraceAsString()
                ]);
                continue;
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                // If not success, mark as failed.
                $schedule->update(['status' => 'FAILED']);
                $record->update([
                    'status' => 'FAILED',
                    'error' => 'Graph returned an error: ' . $e->getMessage()."\n".$e->getTraceAsString()
                ]);
                continue;
            } catch (\Exception $e) {
                // If not success, mark as failed.
                $schedule->update(['status' => 'FAILED']);
                $record->update([
                    'status' => 'FAILED',
                    'error' => $e->getMessage()."\n".$e->getTraceAsString()
                ]);
                continue;
            } catch (Exception $e) {
                // If not success, mark as failed.
                $schedule->update(['status' => 'FAILED']);
                $record->update([
                    'status' => 'FAILED',
                    'error' => $e->getMessage()."\n".$e->getTraceAsString()
                ]);
                continue;
            }

            // Finally, mark as success.
            $schedule->update(['status' => 'SUCCESS']);
            $record->update(['status' => 'SUCCESS']);
        }
    }
}

class Guzzle6HttpClient implements Facebook\HttpClients\FacebookHttpClientInterface
{
    // Work around to make Facebook PHP SDK works with Guzzle 6.x
    // Reference: https://www.sammyk.me/how-to-inject-your-own-http-client-in-the-facebook-php-sdk-v5#writing-a-guzzle-6-http-client-implementation-from-scratch

    private $client;

    public function __construct(GuzzleHttp\Client $client)
    {
        $this->client = $client;
    }

    public function send($url, $method, $body, array $headers, $timeOut)
    {
        $request = new GuzzleHttp\Psr7\Request($method, $url, $headers, $body);
        try {
            $response = $this->client->send($request, ['timeout' => $timeOut, 'http_errors' => false]);
        } catch (GuzzleHttp\Exception\RequestException $e) {
            throw new Facebook\Exceptions\FacebookSDKException($e->getMessage(), $e->getCode());
        }
        $httpStatusCode = $response->getStatusCode();
        $responseHeaders = $response->getHeaders();

        foreach ($responseHeaders as $key => $values) {
            $responseHeaders[$key] = implode(', ', $values);
        }

        $responseBody = $response->getBody()->getContents();

        return new Facebook\Http\GraphRawResponse(
                        $responseHeaders,
                        $responseBody,
                        $httpStatusCode);
    }
}
