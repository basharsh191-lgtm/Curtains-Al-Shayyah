import './bootstrap';
import { createImagePreviewHistory } from './image-preview-history';

window.imagePreviewHistory = createImagePreviewHistory(window.history, () => window.location.href);
window.imagePreviewHistory.clearStale();

document.addEventListener('alpine:init', () => {
    window.Alpine.data('appointmentManager', (urls) => ({
        booking: null,
        showManage: false,
        isSaving: false,
        isCancelling: false,
        toast: '',
        toastIsError: false,
        toastTimeout: null,
        draft: { address: '', date: '', notes: '' },

        init() {
            try {
                const stored = window.localStorage.getItem('alshayyah_active_booking');
                this.booking = stored ? JSON.parse(stored) : null;
                if (!this.isValidBooking(this.booking)) {
                    this.booking = null;
                    if (stored) {
                        window.localStorage.removeItem('alshayyah_active_booking');
                    }
                }
            } catch {
                this.booking = null;
                this.notify('تعذر قراءة بيانات الحجز المحفوظة في هذا المتصفح.', true);
            }
        },

        isValidBooking(booking) {
            return Boolean(
                booking
                && Number.isInteger(Number(booking.id))
                && typeof booking.token === 'string'
                && booking.token.length >= 32
                && typeof booking.name === 'string'
            );
        },

        saveBooking(booking) {
            if (!this.isValidBooking(booking)) {
                this.notify('تعذر حفظ تفاصيل الحجز.', true);
                return;
            }

            this.booking = booking;
            try {
                window.localStorage.setItem('alshayyah_active_booking', JSON.stringify(booking));
                this.notify('تم حفظ تفاصيل حجزك على هذا الجهاز.');
            } catch {
                this.notify('تم الحجز، لكن تعذر حفظه في هذا المتصفح.', true);
            }
        },

        openManage() {
            if (!this.booking) return;
            this.draft = {
                address: this.booking.address || '',
                date: this.booking.date || '',
                notes: this.booking.notes || '',
            };
            this.showManage = true;
        },

        async updateBooking() {
            if (!this.booking || this.isSaving) return;
            this.isSaving = true;
            try {
                const response = await fetch(this.bookingUrl(urls.updateUrl), {
                    method: 'PATCH',
                    headers: this.requestHeaders(),
                    body: JSON.stringify({ ...this.draft, token: this.booking.token }),
                });
                const result = await this.responseData(response);
                this.booking = { ...this.booking, ...result.booking };
                window.localStorage.setItem('alshayyah_active_booking', JSON.stringify(this.booking));
                this.showManage = false;
                this.notify(result.message || 'تم تحديث تفاصيل الموعد.');
            } catch (error) {
                this.notify(error.message || 'تعذر تحديث الموعد. حاول مرة أخرى.', true);
            } finally {
                this.isSaving = false;
            }
        },

        async cancelBooking() {
            if (!this.booking || this.isCancelling) return;
            if (!window.confirm('هل أنت متأكد من إلغاء الموعد؟')) return;

            this.isCancelling = true;
            try {
                const response = await fetch(this.bookingUrl(urls.deleteUrl), {
                    method: 'DELETE',
                    headers: this.requestHeaders(),
                    body: JSON.stringify({ token: this.booking.token }),
                });
                const result = await this.responseData(response);
                window.localStorage.removeItem('alshayyah_active_booking');
                this.booking = null;
                this.showManage = false;
                this.notify(result.message || 'تم إلغاء الموعد.');
            } catch (error) {
                this.notify(error.message || 'تعذر إلغاء الموعد. حاول مرة أخرى.', true);
            } finally {
                this.isCancelling = false;
            }
        },

        bookingUrl(template) {
            return template.replace('APPOINTMENT_ID', encodeURIComponent(this.booking.id));
        },

        statusLabel(status) {
            return {
                pending: 'بانتظار التأكيد',
                confirmed: 'مؤكد',
                contacted: 'تم التواصل',
                completed: 'مكتمل',
                cancelled: 'ملغى',
            }[status] || 'غير معروف';
        },

        requestHeaders() {
            return {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            };
        },

        async responseData(response) {
            const result = await response.json();
            if (!response.ok) {
                const firstError = result.errors ? Object.values(result.errors)[0][0] : null;
                throw new Error(firstError || result.message || 'تعذر تنفيذ الطلب.');
            }
            return result;
        },

        notify(message, isError = false) {
            this.toast = message;
            this.toastIsError = isError;
            window.clearTimeout(this.toastTimeout);
            this.toastTimeout = window.setTimeout(() => { this.toast = ''; }, 4000);
        },
    }));
});
