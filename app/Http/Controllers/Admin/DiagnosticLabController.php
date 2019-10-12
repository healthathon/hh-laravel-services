<?php

namespace App\Http\Controllers\Admin;

use App\Model\LabsTest;
use App\Http\Controllers\Controller;
use App\Model\MMGBookingMailInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class DiagnosticLabController
 * @package App\Http\Controllers\Admin
 */
class DiagnosticLabController extends Controller
{

    public function __construct()
    {
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showThyrocareTestsView($id = null)
    {
        // Get Only 1 Data
        $id = $id == null ? 1 : $id;
        $thyrocareTest = LabsTest::where('id', $id)->first()->toArray();
        $count = LabsTest::all()->count();
        return view('admin.diagnosticLab.testsView', compact('thyrocareTest', 'count'));
    }

    public function getSpecificTestInfo($id)
    {
        $thyrocareTests = LabsTest::where('id', $id)->first()->toJson();
        return $thyrocareTests;
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function updateTestInfo(Request $request, $id)
    {
        $message = "";
        try {
            $updatedTestInfo = LabsTest::where('id', $id)->update($request->get('updateData'));
            if ($updatedTestInfo) {
                $message = "Updated Successfully";
                return [
                    'status' => true,
                    'message' => $message,
                    'data' => $request->get('updateData')
                ];
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        return [
            'status' => false,
            'message' => $message
        ];
    }

    public function fetchTests()
    {
        $assessmentTests = LabsTest::with(['lab:id,name'])->get(['id AS test_id', 'test_name', 'profile', 'abbr', 'test_code', 'price', 'lab_id']);
        return $assessmentTests;
    }

    public function getMMGMailReceiversPage()
    {
        return view("admin.diagnosticLab.MailReceivers");
    }

    public function fetchMMGMailReceiverMembers()
    {
        $members = MMGBookingMailInfo::all();
        return $members;
    }

    public function storeMMGMailReceiverMembers(Request $request)
    {
        $validate = Validator::make($request->get("item"), [
            'user_name' => 'required',
            'user_email' => 'required|email|unique:mmg_booking_mail_infos',
            'to_send' => 'required',
        ], [
            'user_email.email' => "Please Enter Valid Email Address"
        ]);
        if ($validate->fails())
            return ["error" => $validate->getMessageBag()->first()];
        try {
            MMGBookingMailInfo::create($request->get("item"));
            return ["data" => "Member Added"];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function updateMMGMailReceiverMembers(Request $request, $id)
    {
        $validate = Validator::make($request->get("item"), [
            'user_name' => 'required',
            'user_email' => 'required|email',
            'to_send' => 'required',
        ], [
            'user_email.email' => "Please Enter Valid Email Address"
        ]);
        if ($validate->fails())
            return ["error" => $validate->getMessageBag()->first()];
        try {
            MMGBookingMailInfo::where("id", $id)->update($request->get("item"));
            return ["data" => "Member Updated"];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function deleteMMGMailReceiverMembers($id)
    {
        try {
            MMGBookingMailInfo::where('id', $id)->delete();
            return ["data" => "Member Deleted"];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
}
