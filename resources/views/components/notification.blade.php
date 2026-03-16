<div x-data="{
        notifications: [],
        add(e) {
            const id = Date.now();
            const notification = {
                id,
                title: e.detail.title || '',
                message: e.detail.message || '',
                type: e.detail.type || 'success',
                show: false
            };
            this.notifications.push(notification);
            
            // Trigger entry animation
            setTimeout(() => {
                const index = this.notifications.findIndex(n => n.id === id);
                if (index !== -1) {
                    this.notifications[index].show = true;
                }
            }, 10);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                this.remove(id);
            }, 5000);
        },
        remove(id) {
            const index = this.notifications.findIndex(n => n.id === id);
            if (index !== -1) {
                this.notifications[index].show = false;
                setTimeout(() => {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                }, 300);
            }
        }
    }" @notify.window="add($event)"
    class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 w-full max-w-sm pointer-events-none">
    <template x-for="notification in notifications" :key="notification.id">
        <div x-show="notification.show" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-full opacity-0 scale-95"
            x-transition:enter-end="translate-x-0 opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0 opacity-100 scale-100"
            x-transition:leave-end="translate-x-full opacity-0 scale-95"
            class="pointer-events-auto overflow-hidden rounded-2xl border bg-white/80 dark:bg-stone-900/80 backdrop-blur-xl shadow-2xl p-4 flex gap-4"
            :class="{
                'border-emerald-500/20 shadow-emerald-500/5': notification.type === 'success',
                'border-red-500/20 shadow-red-500/5': notification.type === 'danger' || notification.type === 'error',
                'border-amber-500/20 shadow-amber-500/5': notification.type === 'warning',
                'border-blue-500/20 shadow-blue-500/5': notification.type === 'info',
            }">
            <!-- Icon -->
            <div class="flex-shrink-0 pt-0.5">
                <template x-if="notification.type === 'success'">
                    <flux:icon.check-circle variant="mini" class="size-6 text-emerald-500" />
                </template>
                <template x-if="notification.type === 'danger' || notification.type === 'error'">
                    <flux:icon.x-circle variant="mini" class="size-6 text-red-500" />
                </template>
                <template x-if="notification.type === 'warning'">
                    <flux:icon.exclamation-circle variant="mini" class="size-6 text-amber-500" />
                </template>
                <template x-if="notification.type === 'info'">
                    <flux:icon.information-circle variant="mini" class="size-6 text-blue-500" />
                </template>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <h4 x-text="notification.title"
                    class="text-sm font-black text-stone-900 dark:text-white leading-tight mb-1"></h4>
                <p x-text="notification.message"
                    class="text-xs text-stone-600 dark:text-stone-400 font-medium leading-relaxed"></p>
            </div>

            <!-- Close Button -->
            <button @click="remove(notification.id)"
                class="flex-shrink-0 text-stone-400 hover:text-stone-600 dark:hover:text-stone-200 transition-colors">
                <flux:icon.x-mark variant="mini" class="size-5" />
            </button>
        </div>
    </template>
</div>