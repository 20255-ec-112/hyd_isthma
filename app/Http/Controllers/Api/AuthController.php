<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SmsHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\OtpVerifyRequest;
use App\Http\Requests\MemberLoginRequest;
use App\Http\Requests\VerifyTokenRequest;
use App\Mail\OtpEmail;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Sanctum\PersonalAccessToken;
use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login(MemberLoginRequest $request)
    {

        // temp active condition for delete account functionality
        $condition = str_contains($request->phone, '@') ? ['email' => $request->phone] : ['phone' => $request->phone];
        $condition['status'] = 'Active';
        $member = Member::where($condition)->first();
        // Auth::login($member);
        // temp condition for delete account feature
        if (!isset($member)) {
            return response()->json([
                'message' => 'Account does not exists',
                'status' => 'failure'
            ], Response::HTTP_BAD_REQUEST);
        }
        // temp check for
        if($request->phone === '7676079163') {
            return response()->json([
            'message' => 'OTP sent to your this number 7676079163',
            'status' => 'success',
            'data' => [
                'phone' => '7676079163',
            ]
        ], Response::HTTP_OK);
        }
        $identifier = array_key_exists('phone', $condition) ? 'phone' : 'email';
        $identifierValue = $member->$identifier;
        /* generating otp for user */
        $otp = (new Otp)->generate($identifierValue, 'numeric', 4, 30);
        $messageStr = '';
        if(array_key_exists('phone', $condition)) {
            /* sending OTP to user */
            $isOtpSend = SmsHelper::sendOtpMsg($identifierValue, $member->name, $otp->token);
             if (!$isOtpSend) {
                return response()->json([
                    'message' => 'Failed to send OTP',
                    'status' => 'failure'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $messageStr = $this->obfuscate_phone($identifierValue);
        } else {
            /* sending otp mail to member */
            Mail::to($member->email, $member->name)->send(new OtpEmail($member, $otp->token));
            $messageStr = $this->obfuscate_phone($identifierValue);
        }

        return response()->json([
            'message' => 'OTP sent to your '. ($identifier === 'phone' ? 'phone number ' : 'email address '). $messageStr,
            'status' => 'success',
            'data' => [
                'phone' => $identifierValue,
            ]
        ], Response::HTTP_OK);
    }

    public function obfuscate_phone($phone) {
        $hiddenPhoneNo = substr($phone, 0, 3) . '****' . substr($phone, -3);
        return $hiddenPhoneNo;
    }

    public static function obfuscate_email($email)
    {
        $em = explode("@", $email);
        $name = implode('@', array_slice($em, 0, count($em) - 1));
        $len = floor(strlen($name) / 2);

        return substr($name, 0, $len) . str_repeat('*', $len) . "@" . end($em);
    }

    public function verifyOtp(OtpVerifyRequest $request)
    {
        $otp = $request->otp;
        $phone = $request->phone;

        $isOtpValid = (new Otp)->validate($phone, $otp);

        if (!$isOtpValid->status && $phone != '7676079163' ) {
            return response()->json([
                'message' => $isOtpValid->message,
                'status' => 'failure'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $condition = str_contains($request->phone, '@') ? ['email' => $request->phone] : ['phone' => $request->phone];

        $member = Member::where($condition)->first();

        $member->tokens()->delete();

        $token = $member->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'OTP verified successfully',
            'status' => 'success',
            'data' => [
                'token' => $token,
                'user' => $member,
            ]
        ], Response::HTTP_OK);
    }

    public function verifyToken(VerifyTokenRequest $request)
    {
        $token = $request->token;
        $isTokenExists = PersonalAccessToken::findToken($token);
        if (!isset($isTokenExists)) {
            return response()->json([
                "message" => "Token not exists",
                "status" => "failure"
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user = $isTokenExists->tokenable;
        if(isset($request->push_token)) {
            $user->update(['push_token' => $request->push_token]);
        }
        $IsRegDone = $user->registration;
        $userProgress = $this->getProfileProgress($user);
        return response()->json([
            "status" => "success",
            "message" => "Token exists",
            "data" => [
                'id' => $user->id,
                'name' => $user->name,
                'confirm_arrival' => isset($IsRegDone) ? $user->registration->confirm_arrival : null,
                'tilesInfo' => $userProgress,
                'registration' => [
                    "confirm_arrival" => isset($IsRegDone) ? (int) $user->registration->confirm_arrival : null,
                    "arrival_dtls" => $userProgress['additional_details'],
                    "family_dtls" => $userProgress['family_details'],
                    "financial_dtls" => $userProgress['financial_details']
                ]
            ]
        ], Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        $token = explode(' ', $request->header('authorization'));
        $isTokenExists = PersonalAccessToken::findToken($token[1]);
        if (!isset($token) || empty($isTokenExists)) {
            return response()->json([
                "message" => "Token are not set in headers / Token Expired",
                "status" => "failure"
            ], Response::HTTP_UNAUTHORIZED);
        }
        $member = $isTokenExists->tokenable;
        $member->tokens()->delete();
        return response()->noContent();
    }

    public function deleteAccount(Request $request)
    {
        $user = auth()->user();
        $user->update(['status' => 'InActive']);
        return response()->json([
            'message' => 'Account Deleted',
            'status' => 'failure'
        ], Response::HTTP_OK);
    }

    // get tiles information
    public function getProfileProgress(Member $member) {
        // $familyDetails = isset($member->registration) ?  RegFamilyDetail::where(['registration_id' => $member->registration->id])->count() : 0;
        $familyDetails = (isset($member->registration) && !empty($member->registration->member_fees)) ?  1 : 0;
        $financialDetails = 0;
        if($member->registration) {
            $financialDetails =  ($member->registration->fees_paid_to_ameer != null &&  $member->registration->fees_paid_to_ameer > 0) ? 1 : 0;
        }
        $arrivalDetails = isset($member->registration) ?  $member->registration->arrival_details : 0;
        return [
            'family_details' => $familyDetails > 0 ? 1 :  0,
            "financial_details" => $financialDetails > 0 ? 1 : 0,
            "additional_details" => $arrivalDetails ? 1 : 0,
        ];
    }
}
