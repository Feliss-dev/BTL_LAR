<div class="container-fluid fixed-top" x-data>
    @if (session('success'))
        <div x-init="$nextTick(() => {
            $dispatch('client-notification', {
                message: '{{ session('success') }}',
                type: 'success'
            });
        })"></div>
    @endif

    @if (session('error'))
        <div x-init="$nextTick(() => {
            $dispatch('client-notification', {
                message: '{{ session('success') }}',
                type: 'danger'
            });
        })"></div>
    @endif

    <ul x-data="{
        alerts: [],

        addAlert(detail) {
            for (let i = 0; i < this.alerts.length; i++) {
                if (this.alerts[i].active) continue;

                let index = i;
                this.alerts[i] = { active: true, detail: detail, timer: setTimeout(() => {
                    this.removeAlert(index);
                }, 2000) };
                return;
            }

            let c = this.alerts.length;
            this.alerts.push({ active: true, detail: detail, timer: setTimeout(() => {
                this.removeAlert(c);
            }, 2000) });
        },

        removeAlert(index) {
            this.alerts[index].active = false;
            clearTimeout(this.alerts[index].timer);
        }
    }" x-on:client-notification.window="addAlert($event.detail);" class="container position-relative mt-3">
        <template x-for="(alert, index) in alerts" :key="index">
            <div class="position-absolute w-100" :style="`top: ${index * 3.75}rem`"
                 x-show="alert.active"
                 x-transition:enter.duration.500ms
                 x-transition:leave.duration.500ms>

                <div class="alert alert-dismissible" :class="alert.detail.type === 'danger' ? 'alert-danger' : alert.detail.type === 'success' ? 'alert-success' : 'alert-secondary'">
                    <button class="close" @click="removeAlert(index)">×</button>
                    <p x-text="alert.detail.message"></p>
                </div>
            </div>
        </template>
    </ul>
</div>
