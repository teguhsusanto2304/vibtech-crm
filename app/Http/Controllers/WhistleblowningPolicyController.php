<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalConfig;
use App\Mail\WhistleblowingPolicyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class WhistleblowningPolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-whistleblowing-policy', ['only' => ['index', 'show', 'report']]);
        $this->middleware('permission:create-whistleblowing-policy', ['only' => ['create', 'update']]);
        $this->middleware('permission:edit-whistleblowing-policy', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-whistleblowing-policy', ['only' => ['destroy']]);

    }

    public function index()
    {
        $config = GlobalConfig::get();
        $grouped = [];

        foreach ($config as $key => $value) {
            $parts = explode('_', $key, 2);
            $category = $parts[0]; // e.g., 'site', 'contact'
            $grouped[$category][$key] = $value;
        }
        $whistleblowningPolicyContent = $config['whistleblowing_policy'];
        $whistleblowningPolicyCreatedBy = $config['whistleblowing_policy_created_by'];
        $whistleblowningPolicyCreatedAt = $config['whistleblowing_policy_created_at'];
        $whistleblowningPolicyUpdatedBy = $config['whistleblowing_policy_updated_by'];
        $whistleblowningPolicyUpdatedAt = $config['whistleblowing_policy_updated_at'];

        return view('whistleblowning.index', compact('whistleblowningPolicyContent', 'whistleblowningPolicyCreatedBy', 'whistleblowningPolicyCreatedAt', 'whistleblowningPolicyUpdatedBy', 'whistleblowningPolicyUpdatedAt'), ['groupedConfig' => $grouped])->with('title', 'Whistleblowing Policy')->with('breadcrumb', ['Home', 'Staff Task', 'Whistleblowing Policy']);
    }

    public function create()
    {
        $whistleblowningPolicyContent = '';

        return view('whistleblowning.form', compact('whistleblowningPolicyContent'))->with('title', 'Create Whistleblowing Policy Content')->with('breadcrumb', ['Home', 'Staff Task', 'Whistleblowing Policy', 'Create']);
    }

    public function edit()
    {
        $config = GlobalConfig::get();
        $whistleblowningPolicyContent = $config['whistleblowing_policy'];

        return view('whistleblowning.form', compact('whistleblowningPolicyContent'))->with('title', 'Edit Whistleblowing Policy Content')->with('breadcrumb', ['Home', 'Staff Task', 'Whistleblowing Policy', 'Edit']);
    }

    public function update(Request $request)
    {
        $data = Arr::except($request->except(['_token', 'category-table_length', 'name']), ['category_table_length']);

        foreach ($data as $key => $value) {
            GlobalConfig::set($key, $value);
        }

        if (empty(GlobalConfig::get('whistleblowing_policy_created_by'))) {
            GlobalConfig::set('whistleblowing_policy_created_by', auth()->user()->name);
            GlobalConfig::set('whistleblowing_policy_created_at', date('d M Y'));
        } else {
            GlobalConfig::set('whistleblowing_policy_updated_by', auth()->user()->name);
            GlobalConfig::set('whistleblowing_policy_updated_at', date('d M Y'));
        }

        return redirect()->route('v1.whistleblowing-policy')->with('success', 'Whistleblowong Policy updated successfully.');
    }

    public function destroy(Request $request)
    {
        GlobalConfig::set('whistleblowing_policy', '');
        GlobalConfig::set('whistleblowing_policy_created_by', '');
        GlobalConfig::set('whistleblowing_policy_created_at', '');
        GlobalConfig::set('whistleblowing_policy_updated_by', '');
        GlobalConfig::set('whistleblowing_policy_updated_at', '');

        return redirect()->route('v1.whistleblowing-policy')->with('success', 'Whistleblowing Policy deleted successfully.');
    }

    public function report(Request $request)
    {
        $request->validate([
            'description' => 'required',
        ]);
        $data['createdBy'] = auth()->user()->name;
        $data['description'] = $request->input('description');
        Mail::to('houston.teo@vib-tech.com.sg')->send(new WhistleblowingPolicyMail($data));

        return redirect()->route('v1.whistleblowing-policy')->with('success', 'Whistleblowong Policy Report sent successfully.');
    }
}
