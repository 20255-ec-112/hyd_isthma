<?php

namespace App\Http\Controllers;

use App\Helpers\PushNotificationHelper;
use App\Models\Member;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Plank\Mediable\Facades\MediaUploader;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (auth()->user()->id != 1 && !auth()->user()->hasPermissionTo('View Notifications')){
            abort(403);
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
        // Storing notificaiton
        $notificaiton = Notification::create($notificationData);
        // Uploading image to server
        $imgUrl = '';
        if(!empty($request->file('notification_image'))) {
            $media = MediaUploader::fromSource($request->file('notification_image'))->toDestination('public', 'images/notification_image')->upload();
            $notificaiton->attachMedia($media, ['notification_image']);
            $imgUrl = $notificaiton->getMedia('notification_image')->first()->getUrl();
        }
        // Sending Push Notification.
        $is_send = PushNotificationHelper::sendNotification([
            'tokens' => $tokens,
            'title' => $title,
            'message' => $message,
            'imgUrl' => $imgUrl,
            'ytUrl' => $ytUrl,
        ]);
        return back()->with('success', 'Notificaiton Send Successfully');
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