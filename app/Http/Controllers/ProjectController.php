<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
class ProjectController extends Controller
{
    public function index()
    {
        $steps = $this->generateAllSteps();
        $members = [
            [
                'id' => 1,
                'name' => 'Edo',
                'role' => 'Project Lead & Core Backend',
                'email' => 'pedoprimasaragi@gmail.com',
                'steps' => array_filter($steps, fn($s) => $s['step'] >= 1 && $s['step'] <= 200),
            ],
            [
                'id' => 2,
                'name' => 'Haidar',
                'role' => 'Database & Admin Panel',
                'email' => 'haiidarmirza8289@gmail.com',
                'steps' => array_filter($steps, fn($s) => $s['step'] >= 201 && $s['step'] <= 400),
            ],
            [
                'id' => 3,
                'name' => 'Dimas',
                'role' => 'Features & Customer Logic',
                'email' => 'dimasaryadesta2@gmail.com',
                'steps' => array_filter($steps, fn($s) => $s['step'] >= 401 && $s['step'] <= 600),
            ],
            [
                'id' => 4,
                'name' => 'Bernard',
                'role' => 'UI & Public Pages',
                'email' => 'bernardprawira54@gmail.com',
                'steps' => array_filter($steps, fn($s) => $s['step'] >= 601 && $s['step'] <= 800),
            ],
        ];
        return view('project.index', compact('members'));
    }
    public function getProgress()
    {
        $progress = DB::table('project_progress')->first();
        $updates = $this->getGitUpdates();
        if (!$progress) {
            return response()->json([
                'completed_steps' => [],
                'repo_submissions' => [],
                'updates' => $updates,
                'updated_at' => now()->toISOString()
            ]);
        }
        return response()->json([
            'completed_steps' => json_decode($progress->completed_steps, true) ?? [],
            'repo_submissions' => json_decode($progress->repo_submissions, true) ?? [],
            'updates' => $updates,
            'updated_at' => $progress->updated_at
        ]);
    }
    private function getGitUpdates(): array
    {
        return [
            ['id' => 1, 'title' => 'fix: Simplified code updates to manual array only', 'description' => 'Removed GitHub API and database lookups for full control over code updates display', 'file_path' => 'app/Http/Controllers/ProjectController.php', 'update_type' => 'fix', 'update_date' => '2025-12-22'],
            ['id' => 2, 'title' => 'feat: Project page admin access and delete functionality', 'description' => 'Added admin@super.admin to allowed emails, added delete route for updates', 'file_path' => 'app/Http/Middleware/ProjectAccess.php, routes/web.php', 'update_type' => 'feature', 'update_date' => '2025-12-22'],
            ['id' => 3, 'title' => 'fix: Perfect vertical centering for mobile navbar', 'description' => 'Used absolute positioning with transform translate(-50%, -50%) for pixel-perfect centering', 'file_path' => 'resources/views/components/navbar.blade.php', 'update_type' => 'fix', 'update_date' => '2025-12-22'],
            ['id' => 4, 'title' => 'feat: Add luxury close button to mobile navbar', 'description' => 'Circular gold-bordered X button at top-right with rotate animation on hover', 'file_path' => 'resources/views/components/navbar.blade.php', 'update_type' => 'feature', 'update_date' => '2025-12-22'],
            ['id' => 5, 'title' => 'fix: Add GPU acceleration for smooth mobile navbar', 'description' => 'Added translateZ(0), will-change, backface-visibility for hardware acceleration', 'file_path' => 'resources/views/components/navbar.blade.php', 'update_type' => 'fix', 'update_date' => '2025-12-22'],
            ['id' => 6, 'title' => 'feat: Luxury fullscreen mobile navbar redesign', 'description' => 'Fullscreen glassmorphism overlay, gold accents, staggered animations, radial gradient effects', 'file_path' => 'resources/views/components/navbar.blade.php', 'update_type' => 'feature', 'update_date' => '2025-12-22'],
            ['id' => 7, 'title' => 'fix: Remove buggy infinite loop JS for testimonials', 'description' => 'Removed JavaScript infinite scroll that caused crashes, simplified to CSS scroll-snap only', 'file_path' => 'resources/views/welcome.blade.php', 'update_type' => 'fix', 'update_date' => '2025-12-22'],
            ['id' => 8, 'title' => 'fix: Enable smooth horizontal scroll on all devices', 'description' => 'Updated testimonial container CSS for desktop and mobile scroll with hidden scrollbar', 'file_path' => 'public/css/app.css', 'update_type' => 'fix', 'update_date' => '2025-12-22'],
            ['id' => 9, 'title' => 'feat: Add 8 testimonials with random shuffle', 'description' => 'Added 8 testimonials (Edo, Haidar, Dimas pattern) with JavaScript random order on page load', 'file_path' => 'resources/views/welcome.blade.php, lang/en/messages.php, lang/id/messages.php', 'update_type' => 'feature', 'update_date' => '2025-12-22'],
            ['id' => 10, 'title' => 'feat: Luxury testimonials section redesign', 'description' => 'Dark glassmorphism cards, gold accents, decorative quotes, serif typography, gold star ratings', 'file_path' => 'public/css/app.css', 'update_type' => 'feature', 'update_date' => '2025-12-22'],
            ['id' => 11, 'title' => 'fix: Mobile horizontal scroll for testimonials section', 'description' => 'Changed overflow from hidden to clip, added overflow-x visible for mobile breakpoint', 'file_path' => 'public/css/app.css', 'update_type' => 'fix', 'update_date' => '2025-12-22'],
            ['id' => 12, 'title' => 'feat: Add translation for experience badge', 'description' => 'Badge 15+ Tahun Pengalaman now translates using data-i18n attribute', 'file_path' => 'lang/en/messages.php, lang/id/messages.php', 'update_type' => 'feature', 'update_date' => '2025-12-22'],
            ['id' => 13, 'title' => 'feat: Add glassmorphism effect to experience badge', 'description' => 'Badge matches navbar scrolled state with blur(4px) and rgba(255,255,255,0.30) background', 'file_path' => 'public/css/app.css', 'update_type' => 'feature', 'update_date' => '2025-12-22'],
            ['id' => 14, 'title' => 'fix: How To Order desktop scroll bug', 'description' => 'Added overflow hidden to luxury-card-wrapper, moved 100vw styles to mobile-only query', 'file_path' => 'public/css/app.css', 'update_type' => 'fix', 'update_date' => '2025-12-21'],
            ['id' => 15, 'title' => 'fix: About section Our Journey scroll issue', 'description' => 'Added overflow-hidden to video container, repositioned badge from -20px to 20px', 'file_path' => 'resources/views/welcome.blade.php', 'update_type' => 'fix', 'update_date' => '2025-12-21'],
            ['id' => 16, 'title' => 'chore: Complete project comment cleanup', 'description' => 'Removed all code comments from navbar, controllers, middleware, JS, and CSS files', 'file_path' => 'Multiple files', 'update_type' => 'chore', 'update_date' => '2025-12-21'],
            ['id' => 17, 'title' => 'feat: Add Code Updates section to project page', 'description' => 'New collapsible section showing all code changes after 800 steps complete', 'file_path' => 'resources/views/project/index.blade.php', 'update_type' => 'feature', 'update_date' => '2025-12-21'],
            ['id' => 18, 'title' => 'feat: Revamp Project Timeline page with luxury design', 'description' => 'Dark glass UI, gold accents, progress circles, member cards with step tracking', 'file_path' => 'resources/views/project/index.blade.php', 'update_type' => 'feature', 'update_date' => '2025-12-21'],
            ['id' => 19, 'title' => 'feat: Real-time sync for project progress', 'description' => 'Added polling every 3 seconds to sync progress between all team members', 'file_path' => 'resources/views/project/index.blade.php', 'update_type' => 'feature', 'update_date' => '2025-12-21'],
            ['id' => 20, 'title' => 'feat: Lock/unlock system for member sections', 'description' => 'Next member section locked until previous completes 100% of their steps', 'file_path' => 'resources/views/project/index.blade.php', 'update_type' => 'feature', 'update_date' => '2025-12-21'],
            ['id' => 21, 'title' => 'feat: Add viewer account with read-only access', 'description' => 'New viewer role that can access all pages (public, customer, admin) but cannot perform any actions', 'file_path' => 'database/migrations/2025_12_22_154200_add_role_to_users_table.php, app/Models/User.php', 'update_type' => 'feature', 'update_date' => '2025-12-22'],
            ['id' => 22, 'title' => 'feat: ViewerMiddleware to block write operations', 'description' => 'Middleware blocks all non-GET requests for viewer accounts, returns 403 with message', 'file_path' => 'app/Http/Middleware/ViewerMiddleware.php, bootstrap/app.php', 'update_type' => 'feature', 'update_date' => '2025-12-22'],
            ['id' => 23, 'title' => 'feat: ViewerSeeder for test@ account', 'description' => 'Seeder creates viewer account with email test@ and password test for demo purposes', 'file_path' => 'database/seeders/ViewerSeeder.php, database/seeders/DatabaseSeeder.php', 'update_type' => 'feature', 'update_date' => '2025-12-22'],
            ['id' => 24, 'title' => 'fix: Allow non-email format login for viewer', 'description' => 'Changed login input type from email to text, removed email validation rule for test@ format', 'file_path' => 'resources/views/auth/login.blade.php, app/Http/Controllers/Auth/AuthController.php', 'update_type' => 'fix', 'update_date' => '2025-12-22'],
            ['id' => 25, 'title' => 'feat: Visual CMS with Live Preview', 'description' => 'Added visual editor for public site, split-view admin interface, and real-time content updates API', 'file_path' => 'app/Helpers/GlobalHelpers.php, public/js/cms-editor.js, resources/views/admin/cms/index.blade.php', 'update_type' => 'feature', 'update_date' => '2025-12-23'],
            ['id' => 26, 'title' => 'feat: Dashboard UI 3D Icons & Layout', 'description' => 'Replaced flat icons with premium 3D glossy assets (80px), improved spacing and typography', 'file_path' => 'resources/views/customer/dashboard.blade.php', 'update_type' => 'feature', 'update_date' => '2025-12-24'],
            ['id' => 27, 'title' => 'refactor: Reservation System to Eloquent', 'description' => 'Migrated from raw DB queries to Eloquent ORM, added Reservation model for robust data handling', 'file_path' => 'app/Models/Reservation.php, app/Http/Controllers/ReservationController.php', 'update_type' => 'refactor', 'update_date' => '2025-12-24'],
            ['id' => 28, 'title' => 'feat: Upcoming Reservations Display', 'description' => 'Added real-time upcoming reservation card to dashboard with status filtering', 'file_path' => 'resources/views/customer/dashboard.blade.php', 'update_type' => 'feature', 'update_date' => '2025-12-24'],
            ['id' => 29, 'title' => 'feat: Reward Points System', 'description' => 'Implemented /customer/point with 3D animation, transaction history, and dashboard integration', 'file_path' => 'routes/web.php, resources/views/customer/points.blade.php', 'update_type' => 'feature', 'update_date' => '2025-12-24'],
            ['id' => 30, 'title' => 'feat: Favorite Menu System', 'description' => 'Implemented favorites database table, models, and /customer/favorite page with elegant grid layout', 'file_path' => 'routes/web.php, resources/views/customer/favorites.blade.php, app/Models/Favorite.php', 'update_type' => 'feature', 'update_date' => '2025-12-24'],
            ['id' => 31, 'title' => 'feat: Interactive Dashboard Cards', 'description' => 'Linked all dashboard metric cards to their respective feature pages for improved navigation', 'file_path' => 'resources/views/customer/dashboard.blade.php', 'update_type' => 'feature', 'update_date' => '2025-12-24'],
        ];
    }
    public function saveProgress(Request $request)
    {
        $completedSteps = $request->input('completed_steps', []);
        $repoSubmissions = $request->input('repo_submissions', []);
        $exists = DB::table('project_progress')->exists();
        if ($exists) {
            DB::table('project_progress')->update([
                'completed_steps' => json_encode($completedSteps),
                'repo_submissions' => json_encode($repoSubmissions),
                'updated_at' => now()
            ]);
        } else {
            DB::table('project_progress')->insert([
                'completed_steps' => json_encode($completedSteps),
                'repo_submissions' => json_encode($repoSubmissions),
                'updated_at' => now()
            ]);
        }
        return response()->json(['success' => true, 'updated_at' => now()->toISOString()]);
    }
    public function getUpdates()
    {
        $updates = DB::table('project_updates')->orderBy('id', 'desc')->get();
        return response()->json(['updates' => $updates]);
    }
    public function deleteUpdate($id)
    {
        $user = auth()->user();
        if (!$user || $user->email !== 'admin@super.admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        DB::table('project_updates')->where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Update deleted successfully']);
    }
    private function generateAllSteps(): array
    {
        $steps = [];
        $stepNumber = 1;
        $edoFiles = [
            '.env.example',
            'composer.json',
            'package.json',
            'vite.config.js',
            'config/app.php',
            'config/database.php',
            'config/auth.php',
            'bootstrap/app.php',
            'app/Models/User.php',
            'app/Http/Controllers/Auth/AuthController.php',
            'app/Http/Controllers/Auth/GoogleController.php',
            'resources/views/layouts/guest.blade.php',
            'resources/views/auth/login.blade.php',
            'resources/views/auth/register.blade.php',
        ];
        $stepNumber = $this->processFilesIntoSteps($edoFiles, $steps, $stepNumber, 200);
        $haidarFiles = [
            'database/migrations/0001_01_01_000000_create_users_table.php',
            'database/migrations/0001_01_01_000001_create_cache_table.php',
            'database/migrations/0001_01_01_000002_create_jobs_table.php',
            'database/migrations/2025_12_16_035649_add_google_id_to_users_table.php',
            'database/migrations/2025_12_16_044126_add_is_admin_to_users_table.php',
            'database/migrations/2025_12_16_044137_create_menus_table.php',
            'database/migrations/2025_12_16_044139_create_activity_logs_table.php',
            'database/migrations/2025_12_16_050152_add_status_to_users_table.php',
            'database/migrations/2025_12_16_053030_create_reservations_table.php',
            'database/migrations/2025_12_17_151701_add_slug_to_menus_table.php',
            'database/migrations/2025_12_18_000001_create_orders_table.php',
            'app/Models/Menu.php',
            'app/Models/Reservation.php',
            'app/Models/Order.php',
            'resources/views/layouts/admin.blade.php',
            'resources/views/admin/dashboard.blade.php',
            'resources/views/admin/menus/index.blade.php',
            'resources/views/admin/menus/create.blade.php',
            'resources/views/admin/menus/edit.blade.php',
            'resources/views/admin/users/index.blade.php',
            'resources/views/admin/orders/index.blade.php',
            'resources/views/admin/orders/show.blade.php',
            'resources/views/admin/reservations/index.blade.php',
            'resources/views/admin/reservations/show.blade.php',
        ];
        $stepNumber = $this->processFilesIntoSteps($haidarFiles, $steps, $stepNumber, 400);
        $dimasFiles = [
            'app/Http/Controllers/ReservationController.php',
            'app/Http/Controllers/OrderController.php',
            'resources/views/reservation/create.blade.php',
            'resources/views/menu/index.blade.php',
            'resources/views/customer/dashboard.blade.php',
            'resources/views/customer/profile.blade.php',
            'resources/views/customer/orders/index.blade.php',
            'resources/views/customer/orders/create.blade.php',
            'resources/views/customer/orders/show.blade.php',
            'resources/views/customer/reservations/index.blade.php',
            'resources/views/customer/reservations/show.blade.php',
        ];
        $stepNumber = $this->processFilesIntoSteps($dimasFiles, $steps, $stepNumber, 600);
        $bernardFiles = [
            'resources/views/welcome.blade.php',
            'resources/views/about.blade.php',
            'resources/views/contact.blade.php',
            'resources/views/components/navbar.blade.php',
            'resources/views/components/footer.blade.php',
            'public/css/app.css',
            'public/js/cursor.js',
        ];
        $stepNumber = $this->processFilesIntoSteps($bernardFiles, $steps, $stepNumber, 800);
        return $steps;
    }
    private function processFilesIntoSteps(array $files, array &$steps, int $startStep, int $maxStep): int
    {
        $currentStep = $startStep;
        $targetSteps = $maxStep - $startStep + 1;
        $allFileContents = [];
        $totalLines = 0;
        foreach ($files as $filePath) {
            $absolutePath = base_path($filePath);
            if (File::exists($absolutePath)) {
                $content = File::get($absolutePath);
                $lines = explode("\n", $content);
                $allFileContents[$filePath] = $lines;
                $totalLines += count($lines);
            }
        }
        if ($totalLines === 0) {
            return $currentStep;
        }
        $linesPerStep = max(1, ceil($totalLines / $targetSteps));
        foreach ($allFileContents as $filePath => $lines) {
            $lineCount = count($lines);
            $currentLine = 0;
            while ($currentLine < $lineCount && $currentStep <= $maxStep) {
                $endLine = min($currentLine + $linesPerStep, $lineCount);
                $chunk = array_slice($lines, $currentLine, $endLine - $currentLine);
                $codeContent = implode("\n", $chunk);
                if (trim($codeContent) !== '') {
                    $steps[] = [
                        'step' => $currentStep,
                        'file' => $filePath,
                        'line_start' => $currentLine + 1,
                        'line_end' => $endLine,
                        'code' => $codeContent,
                        'action' => $currentLine === 0 ? 'Create file and add' : 'Continue adding',
                    ];
                    $currentStep++;
                }
                $currentLine = $endLine;
            }
        }
        while ($currentStep <= $maxStep) {
            $steps[] = [
                'step' => $currentStep,
                'file' => 'N/A',
                'line_start' => 0,
                'line_end' => 0,
                'code' => 'Step reserved for future expansion',
                'action' => 'Reserved',
            ];
            $currentStep++;
        }
        return $currentStep;
    }
}