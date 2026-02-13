<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 rounded-xl">
        <!-- Welcome Section -->
        <div>
            <h1 class="text-3xl font-bold text-stone-900 dark:text-white mb-2">Welcome back, {{ auth()->user()->name }}
            </h1>
            <p class="text-stone-600 dark:text-stone-400">Your chronicles await. Where shall we begin today?</p>
        </div>

        <!-- Main Grid -->
        <div class="grid auto-rows-min gap-6 md:grid-cols-3">
            <!-- Chronicles Widget -->
            <div
                class="relative overflow-hidden rounded-xl bg-glass p-6 group hover:border-amber-500/50 transition-all">
                <div
                    class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all">
                </div>

                <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-4 flex items-center gap-2">
                    <flux:icon.book-open class="size-5 text-amber-500" /> Chronicles
                </h3>
                <div class="space-y-4">
                    <div
                        class="p-3 rounded-lg bg-stone-100/50 dark:bg-stone-950/50 border border-stone-200/50 dark:border-stone-800/50 cursor-pointer hover:bg-stone-200 dark:hover:bg-stone-900 transition-colors">
                        <div class="text-sm font-medium text-stone-900 dark:text-white">The Lost Mines</div>
                        <div class="text-xs text-stone-500">Last played 2 days ago</div>
                    </div>
                </div>
                <button
                    class="mt-6 w-full py-2 rounded-lg bg-amber-600/20 hover:bg-amber-600 text-amber-600 dark:text-amber-500 hover:text-white font-medium text-sm transition-all border border-amber-500/20 hover:border-amber-500 cursor-pointer">
                    Continue Adventure
                </button>
            </div>

            <!-- Next Session Widget -->
            <div
                class="relative overflow-hidden rounded-xl bg-glass p-6 group hover:border-orange-500/50 transition-all">
                <div
                    class="absolute -right-4 -top-4 w-24 h-24 bg-orange-500/10 rounded-full blur-2xl group-hover:bg-orange-500/20 transition-all">
                </div>
                <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-4 flex items-center gap-2">
                    <flux:icon.calendar class="size-5 text-orange-500" /> Next Session
                </h3>
                <div class="flex flex-col items-center justify-center py-4">
                    <div class="text-4xl font-bold text-stone-900 dark:text-white mb-1">Friday</div>
                    <div class="text-stone-600 dark:text-stone-400">Oct 24 • 8:00 PM</div>
                </div>
                <button
                    class="mt-4 w-full py-2 rounded-lg bg-stone-200 dark:bg-stone-800 hover:bg-stone-300 dark:hover:bg-stone-700 text-stone-700 dark:text-stone-300 hover:text-stone-900 dark:hover:text-white font-medium text-sm transition-all cursor-pointer">
                    View Schedule
                </button>
            </div>

            <!-- The Party Widget -->
            <div
                class="relative overflow-hidden rounded-xl bg-glass p-6 group hover:border-purple-500/50 transition-all">
                <div
                    class="absolute -right-4 -top-4 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/20 transition-all">
                </div>
                <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-4 flex items-center gap-2">
                    <flux:icon.users class="size-5 text-purple-500" /> The Party
                </h3>
                <div class="flex -space-x-2 overflow-hidden py-2 justify-center">
                    <img class="inline-block h-10 w-10 rounded-full ring-2 ring-stone-100 dark:ring-stone-900"
                        src="https://ui-avatars.com/api/?name=Grog&background=1c1917&color=fff" alt="" />
                    <img class="inline-block h-10 w-10 rounded-full ring-2 ring-stone-100 dark:ring-stone-900"
                        src="https://ui-avatars.com/api/?name=Vex&background=1c1917&color=fff" alt="" />
                    <img class="inline-block h-10 w-10 rounded-full ring-2 ring-stone-100 dark:ring-stone-900"
                        src="https://ui-avatars.com/api/?name=Keyleth&background=1c1917&color=fff" alt="" />
                    <div
                        class="h-10 w-10 rounded-full ring-2 ring-stone-100 dark:ring-stone-900 bg-stone-200 dark:bg-stone-800 flex items-center justify-center text-xs text-stone-500 dark:text-stone-400">
                        +2</div>
                </div>
                <button
                    class="mt-8 w-full py-2 rounded-lg bg-stone-200 dark:bg-stone-800 hover:bg-stone-300 dark:hover:bg-stone-700 text-stone-700 dark:text-stone-300 hover:text-stone-900 dark:hover:text-white font-medium text-sm transition-all cursor-pointer">
                    Manage Players
                </button>
            </div>
        </div>

    </div>
</x-layouts::app>