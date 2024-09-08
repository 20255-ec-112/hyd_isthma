<?php

namespace App\Http\Controllers;

use App\Helpers\PushNotificationHelper;
use App\Models\Member;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Plank\Mediable\Facades\MediaUploader;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class NotificationsController extends Controller
{
    public function index(Request $request, DataTables $dataTables)
    {
        $user = User::find(auth()->user()->id);
        if (auth()->user()->id != 1 && !$user->hasPermissionTo('View Notifications')){
            abort(403);
        }
        if($request->ajax())
        {
            $query = Notification::query();
            return $dataTables->eloquent($query)
            ->addColumn('image', function (Notification $notification) {
                $imageSrc = !empty($notification->getMedia('notification_image')->first()) ? $notification->getMedia('notification_image')->first()->getUrl() : '/assets/img/no-image.png';
                return '<img src="'.$imageSrc.'" width="80px" height="80px">';
            }) ->addColumn('document', function (Notification $notification) {
                $docLink = !empty($notification->getMedia('notificaiton_doc')->first()) ? $notification->getMedia('notificaiton_doc')->first()->getUrl() : '';
                return !empty($docLink) ? '<span class="badge badge-primary text-white"><a target="_blank" href="'.$docLink.'"><i class="fas fa-eye text-white"></i></a></span>' : '';
            })->rawColumns(['image', 'document'])->makeHidden(['criteria'])
                ->addIndexColumn()
                ->make(true);

        }
        return view('admin.notifications.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.notifications.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $region = $request->input('region');
        $regionKey = $region . '_name';
        $regionValue = $request->input($regionKey);
        // adding validation rules
        $rules = [
            'region' => 'required',
            'title' => 'required',
            'message' => 'required'
        ];
        if($regionValue === null ){
            $rules[$regionKey] =  'required';
        }
        // validating input fields.
        $request->validate($rules);

        // collecting input data
        $title = $request->input('title');
        $message = $request->input('message');
        $ytUrl = $request->input('youtube_url');
        $regStatus = $request->input('reg_status');
        $gender = $request->input('gender');

        // Query to fetch data with selected criteria
        $query = Member::with('registration')->whereHas('registration', function ($q) use ($regStatus) {
             (!empty($regStatus) && $regStatus === 1) ? $q->where('confirm_arrival', $regStatus) : $q ;
        })->where($region . '_name' , $regionValue);

        if(!empty($gender)) {
            $query->where('gender', $gender);
        }
        $result = $query->get()->pluck('push_token')->toArray();
        $tokens = array_values(array_values($result));

        if (empty($tokens)) {
            return back()->with(['error' => 'No Users with registered tokens found with provided condition']);
        }
        // preparing
        $notificationData = array(
            'title' => $title,
            'message' => $message,
            'criteria' => array(
                'region_type' => $region,
                'region_value' => $regionValue,
                'gender' => $gender,
                'reg_status' => $regStatus
            ),
            'youtube_url' => $ytUrl
            );
        // Storing notification
        $notification = Notification::create($notificationData);
        // Uploading image to server
        $imgUrl = '';
        if(!empty($request->file('notification_image'))) {
            $media = MediaUploader::fromSource($request->file('notification_image'))->toDestination('public', 'images/notification_image')->useFilename(Str::uuid())->upload();
            $notification->attachMedia($media, ['notification_image']);
            $imgUrl = $notification->getMedia('notification_image')->first()->getUrl();
        }
        // upload document to server
        if(!empty($request->file('notificaiton_doc'))) {
            $media = MediaUploader::fromSource($request->file('notificaiton_doc'))->toDestination('public', 'images/notificaiton_doc')->useFilename(Str::uuid())->upload();
            $notification->attachMedia($media, ['notificaiton_doc']);
        }
        // Sending Push Notification.
        $is_send = PushNotificationHelper::sendNotification([
            'tokens' => $tokens,
            'title' => $title,
            'message' => $message,
            'imgUrl' => $imgUrl,
            'ytUrl' => $ytUrl,
            'id' => $notification->id
        ]);
        return back()->with('success', 'Notification Send Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
