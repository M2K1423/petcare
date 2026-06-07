<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * Gửi OTP/Token quên mật khẩu qua Email.
     * Vì project demo chạy local, chúng ta sẽ lưu OTP vào database 
     * và trả về luôn OTP trong response để dễ dàng kiểm thử không cần cấu hình Mail SMTP thật.
     */
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ]);

        $email = $request->input('email');
        // Tạo mã OTP 6 chữ số
        $otp = (string) rand(100000, 999999);

        // Lưu hoặc cập nhật OTP trong bảng password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        // Trong thực tế sẽ gửi email ở đây:
        // Mail::raw("Mã OTP khôi phục mật khẩu của bạn là: $otp", function($message) use ($email) {
        //     $message->to($email)->subject('Khôi phục mật khẩu PetCare');
        // });
        \Illuminate\Support\Facades\Log::info("Mã OTP khôi phục mật khẩu cho {$email} là: {$otp}");

        return response()->json([
            'message' => 'Mã xác thực đã được gửi tới email của bạn.',
            'otp_demo' => $otp, // Trả về mã OTP ở response để test local thuận tiện
        ]);
    }

    /**
     * Xác nhận mã OTP và đặt lại mật khẩu mới.
     */
    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'otp' => ['required', 'string', 'min:6', 'max:6'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $email = $request->input('email');
        $otp = $request->input('otp');

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record) {
            throw ValidationException::withMessages([
                'otp' => ['Yêu cầu khôi phục mật khẩu không hợp lệ.'],
            ]);
        }

        // Kiểm tra OTP hết hạn (ví dụ quá 15 phút)
        if (now()->subMinutes(15)->gt($record->created_at)) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            throw ValidationException::withMessages([
                'otp' => ['Mã OTP đã hết hạn.'],
            ]);
        }

        // Kiểm tra khớp OTP
        if (!Hash::check($otp, $record->token)) {
            throw ValidationException::withMessages([
                'otp' => ['Mã OTP không chính xác.'],
            ]);
        }

        // Cập nhật mật khẩu mới
        $user = User::where('email', $email)->firstOrFail();
        $user->password = $request->input('password'); // Sẽ tự động hash nhờ attribute cast hoặc ta hash thủ công
        // Nếu user model không tự động hash, ta nên hash thủ công:
        // $user->password = Hash::make($request->input('password'));
        $user->save();

        // Xóa mã OTP sau khi sử dụng thành công
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return response()->json([
            'message' => 'Mật khẩu của bạn đã được khôi phục thành công.',
        ]);
    }
}
