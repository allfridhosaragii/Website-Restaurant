<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
class ApiReservationController extends Controller
{
    public function index(Request $request)
    {
        $reservations = Reservation::where('user_id', $request->user()->id)
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();
        return response()->json([
            'success' => true,
            'reservations' => $reservations->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'date' => $reservation->date,
                    'time' => $reservation->time,
                    'guests' => $reservation->guests,
                    'name' => $reservation->name,
                    'phone' => $reservation->phone,
                    'notes' => $reservation->notes,
                    'status' => $reservation->status,
                    'deposit_amount' => $reservation->deposit_amount ?? 150000,
                    'deposit_status' => $reservation->deposit_status ?? 'pending',
                    'deposit_proof' => $reservation->deposit_proof,
                    'created_at' => $reservation->created_at,
                ];
            }),
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
            'guests' => 'required|integer|min:1|max:50',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'table_id' => 'required|integer|exists:tables,id',
            'notes' => 'nullable|string|max:500',
        ]);
        $existingReservation = Reservation::where('table_id', $request->table_id)
            ->where('date', $request->date)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();
        if ($existingReservation) {
            return response()->json([
                'success' => false,
                'message' => 'Meja ini sudah dipesan untuk tanggal tersebut',
            ], 422);
        }
        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'date' => $request->date,
            'time' => $request->time,
            'guests' => $request->guests,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'table_id' => $request->table_id,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);
        $points = 10000;
        $request->user()->increment('points', $points);
        \App\Models\PointTransaction::create([
            'user_id' => $request->user()->id,
            'points' => $points,
            'type' => 'reservation',
            'description' => 'Reservasi Meja (Table ' . $request->table_id . ')',
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Reservation created successfully',
            'reservation' => [
                'id' => $reservation->id,
                'date' => $reservation->date,
                'time' => $reservation->time,
                'guests' => $reservation->guests,
                'table_id' => $reservation->table_id,
                'status' => $reservation->status,
            ],
        ], 201);
    }
    public function show(Request $request, $id)
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();
        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'reservation' => [
                'id' => $reservation->id,
                'date' => $reservation->date,
                'time' => $reservation->time,
                'guests' => $reservation->guests,
                'name' => $reservation->name,
                'phone' => $reservation->phone,
                'notes' => $reservation->notes,
                'status' => $reservation->status,
                'deposit_amount' => $reservation->deposit_amount ?? 150000,
                'deposit_status' => $reservation->deposit_status ?? 'pending',
                'deposit_proof' => $reservation->deposit_proof,
                'created_at' => $reservation->created_at,
            ],
        ]);
    }
    public function uploadProof(Request $request, $id)
    {
        $request->validate([
            'deposit_proof' => 'required|image|max:5120', 
        ]);
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();
        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found',
            ], 404);
        }
        $file = $request->file('deposit_proof');
        $cloudinaryUrl = $this->uploadToCloudinary($file, 'deposit_proofs');
        if (!$cloudinaryUrl) {
            $path = $file->store('deposit_proofs', 'public');
            $cloudinaryUrl = asset('storage/' . $path);
        }
        $reservation->update([
            'deposit_proof' => $cloudinaryUrl,
            'deposit_status' => 'paid',
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Deposit proof uploaded successfully',
            'deposit_proof' => $cloudinaryUrl,
            'deposit_status' => 'paid',
        ]);
    }
    private function uploadToCloudinary($file, $folder = 'uploads')
    {
        $cloudinaryUrl = env('CLOUDINARY_URL');
        if (!$cloudinaryUrl) {
            return null;
        }
        preg_match('/cloudinary:\/\/([^:]+):([^@]+)@(.+)/', $cloudinaryUrl, $matches);
        if (count($matches) < 4) {
            return null;
        }
        $apiKey = $matches[1];
        $apiSecret = $matches[2];
        $cloudName = $matches[3];
        $timestamp = time();
        $params = [
            'folder' => $folder,
            'timestamp' => $timestamp,
        ];
        ksort($params);
        $signatureString = '';
        foreach ($params as $key => $value) {
            $signatureString .= $key . '=' . $value . '&';
        }
        $signatureString = rtrim($signatureString, '&') . $apiSecret;
        $signature = sha1($signatureString);
        $uploadUrl = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";
        $postData = [
            'file' => new \CURLFile($file->getPathname(), $file->getMimeType(), $file->getClientOriginalName()),
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'folder' => $folder,
            'signature' => $signature,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uploadUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return $result['secure_url'] ?? null;
        }
        \Log::error('Cloudinary upload failed', ['response' => $response, 'httpCode' => $httpCode]);
        return null;
    }
}