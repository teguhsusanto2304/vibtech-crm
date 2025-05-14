<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\GlobalConfig;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ConfigurationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $config = GlobalConfig::get();
        $grouped = [];

        foreach ($config as $key => $value) {
            $parts = explode('_', $key, 2);
            if($parts[0]=='site'){
                $category = $parts[0]; // e.g., 'site', 'contact'
                $grouped[$category][$key] = $value;
            }
        }

        return view('configuration.index',['groupedConfig' => $grouped])->with('title', 'Configuration')->with('breadcrumb', ['Home', 'Master Data', 'Configuration']);
    }

    public function update(Request $request)
    {
        $path = config_path('global.json');
    $settings = json_decode(file_get_contents($path), true);

    // Handle text input
    if ($request->has('site_login_title')) {
        $settings['site_login_title']['value'] = $request->input('site_login_title');
    }

    // Handle file input
    if ($request->hasFile('site_login_image')) {
        $file = $request->file('site_login_image');
        $filename = uniqid() . $file->getClientOriginalName();
        $file->move(public_path('assets/img'), $filename);
        $settings['site_login_image']['value'] = "assets/img/" . $filename;
    }

    file_put_contents($path, json_encode($settings, JSON_PRETTY_PRINT));

    return back()->with('success', 'Settings updated successfully.');
    }

    protected function findConfigDefinition($key)
    {
        foreach (config('your_config_file') as $category => $settings) {
            if (isset($settings[$key])) {
                return $settings[$key];
            }
        }
        return null;
    }

    protected function saveConfiguration($key, $value)
    {
        // Implementasikan logika penyimpanan konfigurasi Anda di sini
        // Ini bisa berupa penyimpanan ke file konfigurasi, database, atau cara lainnya.

        // Contoh menyimpan ke file konfigurasi (hati-hati, ini bisa kompleks dan berisiko):
        $path = config_path('your_config_file.php');
        $configArray = config('your_config_file');

        foreach ($configArray as $category => &$settings) {
            if (isset($settings[$key])) {
                $settings[$key]['value'] = $value;
                break;
            }
        }

        $content = "<?php\n\nreturn " . var_export($configArray, true) . ";\n";
        file_put_contents($path, $content);
        Artisan::call('config:clear'); // Clear cache agar perubahan diterapkan

        // Contoh menyimpan ke database (asumsi Anda memiliki model Configuration):
        // \App\Models\Configuration::where('key', $key)->update(['value' => $value]);
    }
}
