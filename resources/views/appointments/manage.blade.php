<div
    x-show="booking"
    x-cloak
    class="container py-3"
    x-transition.opacity.duration.200ms
>
    <div class="flex items-center justify-between gap-3 rounded-xl border border-[#E9E1D5] bg-white/70 px-4 py-2">
        <p class="text-xs leading-6 text-[#5B6470] sm:text-sm">لديك حجز زيارة منزلية محجوز</p>
        <button
            type="button"
            class="shrink-0 rounded-full px-3 py-1.5 text-xs font-semibold text-[#79552F] transition hover:bg-[#F3EBDF] sm:text-sm"
            @click="openManage()"
        >عرض التفاصيل</button>
    </div>
</div>

<div
    x-cloak
    x-show="showManage"
    x-transition.opacity
    class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto bg-[#202A35]/60 px-4 py-6 backdrop-blur-sm"
    @click.self="showManage = false"
    @keydown.escape.window="showManage = false"
>
    <section
        role="dialog"
        aria-modal="true"
        aria-labelledby="manage-booking-title"
        class="my-auto w-full max-w-xl rounded-3xl border border-[#D8C19F] bg-[#FCFAF6] p-6 shadow-[0_24px_80px_rgba(20,24,29,0.3)] sm:p-8"
    >
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold tracking-[0.18em] text-[#927047]">معرض الشياح للستائر</p>
                <h2 id="manage-booking-title" class="mt-2 text-2xl font-semibold text-[#202A35]">تفاصيل حجز الزيارة</h2>
            </div>
            <button type="button" class="rounded-full px-3 py-1 text-2xl text-[#5B6470] hover:bg-[#F3EBDF]" aria-label="إغلاق" @click="showManage = false">×</button>
        </div>

        <dl class="mt-6 grid gap-3 rounded-2xl border border-[#E9E1D5] bg-white/80 p-4 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-xs text-[#7A838D]">رقم الحجز</dt>
                <dd class="mt-1 font-semibold text-[#344252]" x-text="booking?.id"></dd>
            </div>
            <div>
                <dt class="text-xs text-[#7A838D]">الحالة</dt>
                <dd class="mt-1 font-semibold text-[#344252]" x-text="statusLabel(booking?.status)"></dd>
            </div>
            <div>
                <dt class="text-xs text-[#7A838D]">الاسم</dt>
                <dd class="mt-1 font-semibold text-[#344252]" x-text="booking?.name"></dd>
            </div>
            <div>
                <dt class="text-xs text-[#7A838D]">رقم الهاتف</dt>
                <dd class="mt-1 font-semibold text-[#344252]" dir="ltr" x-text="booking?.phone"></dd>
            </div>
            <div x-show="booking?.address_details" class="sm:col-span-2">
                <dt class="text-xs text-[#7A838D]">تفصيل العنوان</dt>
                <dd class="mt-1 font-semibold text-[#344252]" x-text="booking?.address_details"></dd>
            </div>
        </dl>

        <form class="mt-5 space-y-4" @submit.prevent="updateBooking()">
            <div class="form-field">
                <label for="manage-address">العنوان</label>
                <input id="manage-address" x-model="draft.address" type="text" maxlength="255" required>
            </div>
            <div class="form-field">
                <label for="manage-date">التاريخ المناسب للزيارة</label>
                <input id="manage-date" x-model="draft.date" type="date" min="{{ now()->format('Y-m-d') }}" required>
            </div>
            <div class="form-field">
                <label for="manage-notes">ملاحظات</label>
                <textarea id="manage-notes" x-model="draft.notes" maxlength="1000"></textarea>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-[#E9E1D5] pt-5 sm:flex-row sm:justify-between">
                <button
                    type="button"
                    class="button button-danger"
                    :disabled="isCancelling"
                    @click="cancelBooking()"
                    x-text="isCancelling ? 'جارٍ الإلغاء...' : 'إلغاء الموعد'"
                ></button>
                <div class="flex flex-col-reverse gap-3 sm:flex-row">
                    <button type="button" class="button button-light" @click="showManage = false">إغلاق</button>
                    <button type="submit" class="button button-dark" :disabled="isSaving" x-text="isSaving ? 'جارٍ الحفظ...' : 'حفظ التعديلات'"></button>
                </div>
            </div>
        </form>
    </section>
</div>

<div
    x-cloak
    x-show="toast"
    x-transition
    role="status"
    aria-live="polite"
    class="fixed bottom-24 left-4 z-[110] max-w-sm rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-lg md:bottom-6"
    :class="toastIsError ? 'bg-[#A23D3D]' : 'bg-[#28704F]'"
    x-text="toast"
></div>
