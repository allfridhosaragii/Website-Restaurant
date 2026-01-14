<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\CmsMedia;
use App\Models\CmsSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
class AdminCmsController extends Controller
{
    public function index()
    {
        $stats = [
            'total_pages' => CmsPage::count(),
            'published_pages' => CmsPage::where('is_published', true)->count(),
            'total_media' => CmsMedia::count(),
            'total_sections' => CmsSection::count(),
        ];
        $recentPages = CmsPage::orderBy('updated_at', 'desc')->limit(5)->get();
        $recentMedia = CmsMedia::orderBy('created_at', 'desc')->limit(8)->get();
        return view('admin.cms.index', compact('stats', 'recentPages', 'recentMedia'));
    }
    public function pages()
    {
        $pages = CmsPage::ordered()->get();
        return view('admin.cms.pages.index', compact('pages'));
    }
    public function createPage()
    {
        return view('admin.cms.pages.form');
    }
    public function storePage(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug',
            'content' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_published' => 'boolean',
            'template' => 'nullable|string',
        ]);
        $page = CmsPage::create([
            'title' => $request->title,
            'slug' => $request->slug ?: Str::slug($request->title),
            'content' => $request->content ?? [],
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'is_published' => $request->boolean('is_published'),
            'template' => $request->template ?? 'default',
            'order' => CmsPage::max('order') + 1,
        ]);
        return redirect('/admin/developer/pages')->with('success', 'Halaman berhasil dibuat!');
    }
    public function editPage($id)
    {
        $page = CmsPage::with('sections')->findOrFail($id);
        return view('admin.cms.pages.form', compact('page'));
    }
    public function updatePage(Request $request, $id)
    {
        $page = CmsPage::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug,' . $id,
            'content' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_published' => 'boolean',
            'template' => 'nullable|string',
        ]);
        $page->update([
            'title' => $request->title,
            'slug' => $request->slug ?: Str::slug($request->title),
            'content' => $request->content ?? [],
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'is_published' => $request->boolean('is_published'),
            'template' => $request->template ?? 'default',
        ]);
        return redirect('/admin/developer/pages')->with('success', 'Halaman berhasil diperbarui!');
    }
    public function destroyPage($id)
    {
        $page = CmsPage::findOrFail($id);
        $page->delete();
        return redirect('/admin/developer/pages')->with('success', 'Halaman berhasil dihapus!');
    }
    public function media()
    {
        $media = CmsMedia::orderBy('created_at', 'desc')->paginate(24);
        $folders = CmsMedia::distinct('folder')->pluck('folder');
        return view('admin.cms.media.index', compact('media', 'folders'));
    }
    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'folder' => 'nullable|string|max:100',
            'alt_text' => 'nullable|string|max:255',
        ]);
        $file = $request->file('file');
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('cms/media/' . ($request->folder ?? 'general'), $filename, 'public');
        $imageSize = @getimagesize($file->getRealPath());
        $media = CmsMedia::create([
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'url' => Storage::url($path),
            'type' => str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file',
            'size' => $file->getSize(),
            'alt_text' => $request->alt_text,
            'folder' => $request->folder ?? 'general',
            'mime_type' => $file->getMimeType(),
            'width' => $imageSize[0] ?? null,
            'height' => $imageSize[1] ?? null,
        ]);
        if ($request->ajax()) {
            return response()->json(['success' => true, 'media' => $media]);
        }
        return redirect('/admin/developer/media')->with('success', 'File berhasil diupload!');
    }
    public function destroyMedia($id)
    {
        $media = CmsMedia::findOrFail($id);
        Storage::disk('public')->delete($media->path);
        $media->delete();
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect('/admin/developer/media')->with('success', 'File berhasil dihapus!');
    }
    public function settings()
    {
        $settings = CmsSetting::all()->groupBy('group');
        $defaultSettings = [
            'general' => [
                ['key' => 'site_name', 'label' => 'Nama Website', 'type' => 'text'],
                ['key' => 'site_tagline', 'label' => 'Tagline', 'type' => 'text'],
                ['key' => 'site_logo', 'label' => 'Logo', 'type' => 'image'],
            ],
            'contact' => [
                ['key' => 'contact_email', 'label' => 'Email', 'type' => 'email'],
                ['key' => 'contact_phone', 'label' => 'Telepon', 'type' => 'text'],
                ['key' => 'contact_address', 'label' => 'Alamat', 'type' => 'textarea'],
            ],
            'social' => [
                ['key' => 'social_facebook', 'label' => 'Facebook URL', 'type' => 'url'],
                ['key' => 'social_instagram', 'label' => 'Instagram URL', 'type' => 'url'],
                ['key' => 'social_twitter', 'label' => 'Twitter URL', 'type' => 'url'],
            ],
        ];
        return view('admin.cms.settings.index', compact('settings', 'defaultSettings'));
    }
    public function updateSettings(Request $request)
    {
        $settings = $request->except('_token');
        foreach ($settings as $key => $value) {
            CmsSetting::set($key, $value);
        }
        return redirect('/admin/developer/settings')->with('success', 'Pengaturan berhasil disimpan!');
    }
    public function storeSection(Request $request)
    {
        $request->validate([
            'page_id' => 'required|exists:cms_pages,id',
            'type' => 'required|string',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);
        $section = CmsSection::create([
            'page_id' => $request->page_id,
            'type' => $request->type,
            'title' => $request->title,
            'content' => $request->content ?? [],
            'settings' => $request->settings ?? [],
            'order' => CmsSection::where('page_id', $request->page_id)->max('order') + 1,
        ]);
        if ($request->ajax()) {
            return response()->json(['success' => true, 'section' => $section]);
        }
        return back()->with('success', 'Section berhasil ditambahkan!');
    }
    public function updateSection(Request $request, $id)
    {
        $section = CmsSection::findOrFail($id);
        $section->update([
            'title' => $request->title,
            'content' => $request->content ?? [],
            'settings' => $request->settings ?? [],
            'is_active' => $request->boolean('is_active'),
        ]);
        if ($request->ajax()) {
            return response()->json(['success' => true, 'section' => $section]);
        }
        return back()->with('success', 'Section berhasil diperbarui!');
    }
    public function reorderSections(Request $request)
    {
        $orders = $request->input('orders', []);
        foreach ($orders as $order => $id) {
            CmsSection::where('id', $id)->update(['order' => $order]);
        }
        return response()->json(['success' => true]);
    }
    public function destroySection($id)
    {
        $section = CmsSection::findOrFail($id);
        $section->delete();
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Section berhasil dihapus!');
    }
    public function editHomepage()
    {
        $pageData = CmsSetting::get('homepage_content', []);
        return view('admin.cms.pages.edit-homepage', compact('pageData'));
    }
    public function updateHomepage(Request $request)
    {
        CmsSetting::set('homepage_content', $request->except('_token'));
        return redirect()->back()->with('success', 'Homepage berhasil diperbarui!');
    }
    public function editMenuPage()
    {
        $pageData = CmsSetting::get('menu_page_content', []);
        return view('admin.cms.pages.edit-menu', compact('pageData'));
    }
    public function updateMenuPage(Request $request)
    {
        CmsSetting::set('menu_page_content', $request->except('_token'));
        return redirect()->back()->with('success', 'Halaman Menu berhasil diperbarui!');
    }
    public function editAboutPage()
    {
        $pageData = CmsSetting::get('about_page_content', []);
        return view('admin.cms.pages.edit-about', compact('pageData'));
    }
    public function updateAboutPage(Request $request)
    {
        CmsSetting::set('about_page_content', $request->except('_token'));
        return redirect()->back()->with('success', 'Halaman About berhasil diperbarui!');
    }
    public function editContactPage()
    {
        $pageData = CmsSetting::get('contact_page_content', []);
        return view('admin.cms.pages.edit-contact', compact('pageData'));
    }
    public function updateContactPage(Request $request)
    {
        CmsSetting::set('contact_page_content', $request->except('_token'));
        return redirect()->back()->with('success', 'Halaman Contact berhasil diperbarui!');
    }
    public function editReservationPage()
    {
        $pageData = CmsSetting::get('reservation_page_content', []);
        return view('admin.cms.pages.edit-reservation', compact('pageData'));
    }
    public function updateReservationPage(Request $request)
    {
        CmsSetting::set('reservation_page_content', $request->except('_token'));
        return redirect()->back()->with('success', 'Halaman Reservation berhasil diperbarui!');
    }
    public function editLoginPage()
    {
        $pageData = CmsSetting::get('login_page_content', []);
        return view('admin.cms.pages.edit-login', compact('pageData'));
    }
    public function updateLoginPage(Request $request)
    {
        CmsSetting::set('login_page_content', $request->except('_token'));
        return redirect()->back()->with('success', 'Halaman Login berhasil diperbarui!');
    }
    public function apiUpdateContent(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'content' => 'nullable', 
        ]);
        CmsSetting::updateOrCreate(
            ['key' => $request->key],
            [
                'value' => $request->content,
                'group' => 'cms_content', 
                'type' => 'text'
            ]
        );
        return response()->json(['success' => true]);
    }
    public function apiUploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', 
        ]);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('cms/uploads', $filename, 'public');
            $url = Storage::url($path);
            return response()->json([
                'success' => true,
                'url' => $url
            ]);
        }
        return response()->json(['success' => false, 'message' => 'No image uploaded'], 400);
    }
    
    public function application()
    {
        $currentApk = null;
        $activeApk = CmsSetting::get('active_apk_filename');
        
        if ($activeApk) {
            $supabaseUrl = env('SUPABASE_URL');
            $bucket = env('SUPABASE_BUCKET');
            $publicUrl = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/apks/{$activeApk}";
            
            // We can add a simple check if the file exists on Supabase if needed, 
            // but for now we'll trust the database record for the UI.
            $currentApk = [
                'name' => $activeApk,
                'size' => CmsSetting::get('active_apk_size', '48 MB'), // We'll store size in DB too
                'date' => CmsSetting::get('active_apk_date', date('d M Y H:i')),
                'url' => $publicUrl,
            ];
        }
        
        return view('admin.application.index', compact('currentApk'));
    }
    
    public function updateApplication(Request $request)
    {
        $request->validate([
            'apk_file' => 'nullable|file|max:102400', // 100MB max
        ]);
        
        if ($request->hasFile('apk_file')) {
            $file = $request->file('apk_file');
            
            // Validate it's an APK file
            if ($file->getClientOriginalExtension() !== 'apk') {
                return redirect('/admin/application')->with('error', 'File harus berformat .apk');
            }
            
            // Move file to Supabase
            $timestamp = date('dmy-Hi');
            $newFilename = 'Culinaire-' . $timestamp . '.apk';
            $fileSize = $this->formatFileSize($file->getSize());
            $fileDate = date('d M Y H:i');
            
            $supabaseUrl = env('SUPABASE_URL');
            $serviceRole = env('SUPABASE_SERVICE_ROLE_KEY');
            $bucket = env('SUPABASE_BUCKET');

            // --- AUTO-CLEANUP LOGIC ---
            // 1. Get current active filename from database
            $oldFilename = CmsSetting::get('active_apk_filename');
            
            // 2. If old file exists, delete it from Supabase Storage
            if ($oldFilename) {
                Http::withHeaders([
                    'Authorization' => "Bearer {$serviceRole}",
                ])->delete("{$supabaseUrl}/storage/v1/object/{$bucket}/apks/{$oldFilename}");
                
                // Note: We don't block upload if delete fails (e.g. file already gone manually)
            }
            // --------------------------
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$serviceRole}",
            ])->attach(
                'file', file_get_contents($file->getRealPath()), $newFilename
            )->post("{$supabaseUrl}/storage/v1/object/{$bucket}/apks/{$newFilename}");

            if ($response->failed()) {
                return redirect('/admin/application')->with('error', 'Gagal upload ke Supabase: ' . $response->body());
            }
            
            // Store the filename and metadata in settings
            CmsSetting::set('active_apk_filename', $newFilename, 'application', 'text');
            CmsSetting::set('active_apk_size', $fileSize, 'application', 'text');
            CmsSetting::set('active_apk_date', $fileDate, 'application', 'text');
            
            return redirect('/admin/application')->with('success', 'Aplikasi berhasil diupload ke Supabase dengan nama ' . $newFilename . '!');
        }

        return redirect('/admin/application')->with('error', 'Silakan pilih file APK terlebih dahulu');
    }
    
    private function formatFileSize($bytes)
    {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1024 * 1024) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / (1024 * 1024), 1) . ' MB';
    }

    public function generateUploadUrl(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
        ]);

        $supabaseUrl = env('SUPABASE_URL');
        $serviceRole = env('SUPABASE_SERVICE_ROLE_KEY');
        $bucket = env('SUPABASE_BUCKET');

        // 1. Generate new unique filename
        $timestamp = date('dmy-Hi');
        $extension = pathinfo($request->filename, PATHINFO_EXTENSION) ?: 'apk';
        $newFilename = 'Culinaire-' . $timestamp . '.' . $extension;

        // 2. Auto-cleanup: Delete old file
        $oldFilename = CmsSetting::get('active_apk_filename');
        if ($oldFilename) {
            Http::withHeaders([
                'Authorization' => "Bearer {$serviceRole}",
            ])->delete("{$supabaseUrl}/storage/v1/object/{$bucket}/apks/{$oldFilename}");
        }

        // 3. Get Signed Upload URL from Supabase
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$serviceRole}",
            'Content-Type' => 'application/json',
        ])->post("{$supabaseUrl}/storage/v1/object/upload/sign/{$bucket}/apks/{$newFilename}", [
            'expiresIn' => 3600 // 1 hour
        ]);

        if ($response->failed()) {
            return response()->json(['success' => false, 'message' => 'Gagal membuat upload URL: ' . $response->body()], 500);
        }

        $data = $response->json();
        
        return response()->json([
            'success' => true,
            'upload_url' => "{$supabaseUrl}/storage/v1" . $data['url'],
            'filename' => $newFilename,
            'supabase_url' => $supabaseUrl
        ]);
    }

    public function finalizeUpload(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'size' => 'required|string',
        ]);

        $fileDate = date('d M Y H:i');

        CmsSetting::set('active_apk_filename', $request->filename, 'application', 'text');
        CmsSetting::set('active_apk_size', $request->size, 'application', 'text');
        CmsSetting::set('active_apk_date', $fileDate, 'application', 'text');

        return response()->json(['success' => true]);
    }
}