import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

export function useNotification() {
    const notifySuccess = (message) => {
        toast.success(message, {
            position: toast.POSITION.TOP_RIGHT,
            autoClose: 3000,
        });
    };

    const notifyError = (message) => {
        toast.error(message, {
            position: toast.POSITION.TOP_RIGHT,
            autoClose: 4000,
        });
    };

    const notifyWarning = (message) => {
        toast.warning(message, {
            position: toast.POSITION.TOP_RIGHT,
            autoClose: 3000,
        });
    };

    const notifyInfo = (message) => {
        toast.info(message, {
            position: toast.POSITION.TOP_RIGHT,
            autoClose: 3000,
        });
    };

    /**
     * Tự động xử lý lỗi từ API, parse validation errors (422) và thông báo.
     * @param {Object} response - Fetch API Response Object (if available)
     * @param {Object} data - Parsed JSON Data (if available)
     * @param {Error} error - Catch block Error object
     * @returns {Object} Các lỗi validation được parse dạng object { field: 'message' }
     */
    const handleApiError = async (err, res = null) => {
        let validationErrors = {};
        let errorMessage = 'Đã có lỗi xảy ra. Vui lòng thử lại sau!';

        try {
            if (res && res.status === 422) {
                // Validation Error
                const data = await res.json();
                errorMessage = data.message || 'Dữ liệu không hợp lệ!';
                if (data.errors) {
                    // Chuyển mảng lỗi thành 1 câu chuỗi hoặc lưu vào object
                    Object.keys(data.errors).forEach(key => {
                        validationErrors[key] = data.errors[key][0]; // Lấy lỗi đầu tiên của mỗi field
                    });
                }
            } else if (res && res.status >= 500) {
                errorMessage = 'Lỗi máy chủ (500). Vui lòng báo cho quản trị viên!';
            } else if (res && res.status === 403) {
                errorMessage = 'Bạn không có quyền thực hiện thao tác này!';
            } else if (err && err.message) {
                errorMessage = err.message;
            }

            notifyError(errorMessage);
        } catch (parseError) {
            notifyError('Lỗi kết nối đến máy chủ!');
        }

        return validationErrors;
    };

    return {
        notifySuccess,
        notifyError,
        notifyWarning,
        notifyInfo,
        handleApiError
    };
}
